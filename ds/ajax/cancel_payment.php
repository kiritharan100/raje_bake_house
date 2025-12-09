 <?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
include '../../db.php';

header('Content-Type: application/json');

if ($_POST) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $payment_id = intval($_POST['payment_id']);
        
        if ($payment_id <= 0) {
            throw new Exception("Invalid payment ID");
        }
        
        // Get payment details before cancellation
        $payment_sql = "SELECT lp.*, l.lease_number,l.file_number ,l.beneficiary_id
                       FROM lease_payments lp 
                       LEFT JOIN leases l ON lp.lease_id = l.lease_id 
                       WHERE lp.payment_id = ?";
        $stmt = $con->prepare($payment_sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();
        
        if (!$payment) {
            throw new Exception("Payment not found");
        }

        
        // Start transaction
        $con->begin_transaction();

        // 1) Mark the payment as cancelled (status = 0) instead of deleting
        $delete_sql = "UPDATE lease_payments SET status = 0 WHERE payment_id = ?";
        $stmt_delete = $con->prepare($delete_sql);
        $stmt_delete->bind_param("i", $payment_id);
        if (!$stmt_delete->execute()) {
            throw new Exception("Failed to cancel payment: " . $stmt_delete->error);
        }

        // 2) Reset all schedule payment allocations for this lease to zero,
        //    including discount_apply so discount is fully reversed.
        $reset_sql = "UPDATE lease_schedules 
                      SET paid_rent = 0, 
                          panalty_paid = 0, 
                          premium_paid = 0, 
                          total_paid = 0,
                          discount_apply = 0
                      WHERE lease_id = ?";
        $stmt_reset = $con->prepare($reset_sql);
        $stmt_reset->bind_param("i", $payment['lease_id']);
        if (!$stmt_reset->execute()) {
            throw new Exception('Failed to reset schedules: ' . $stmt_reset->error);
        }

        // 3) Reapply remaining ACTIVE payments (status = 1) for this lease
        //    in chronological order to rebuild allocations.
        $payments_sql = "SELECT payment_id, lease_id, schedule_id, payment_date, amount, payment_type, receipt_number
                         FROM lease_payments 
                         WHERE lease_id = ? AND status = 1
                         ORDER BY payment_date ASC, payment_id ASC";
        $stmt_payments = $con->prepare($payments_sql);
        $stmt_payments->bind_param("i", $payment['lease_id']);
        if (!$stmt_payments->execute()) {
            throw new Exception('Failed to fetch remaining payments: ' . $stmt_payments->error);
        }
        $res_payments = $stmt_payments->get_result();

        while ($p = $res_payments->fetch_assoc()) {
            $p_amount = floatval($p['amount']);
            $p_date   = $p['payment_date'];

            // Find target schedule for this payment (same logic as before)
            $sched_sql = "SELECT schedule_id, start_date, end_date, schedule_year, 
                                 paid_rent, panalty_paid, premium, premium_paid, total_paid
                          FROM lease_schedules 
                          WHERE lease_id = ? AND ? BETWEEN start_date AND end_date 
                          LIMIT 1";
            $stmt_sched = $con->prepare($sched_sql);
            $stmt_sched->bind_param("is", $p['lease_id'], $p_date);
            $stmt_sched->execute();
            $sched_res = $stmt_sched->get_result();
            $target_schedule = null;

            if ($sched_res->num_rows > 0) {
                $target_schedule = $sched_res->fetch_assoc();
            } else {
                // fallback by year
                $py = date('Y', strtotime($p_date));
                $sched_year_sql = "SELECT schedule_id, start_date, end_date, schedule_year, 
                                          paid_rent, panalty_paid, premium, premium_paid, total_paid
                                   FROM lease_schedules 
                                   WHERE lease_id = ? AND schedule_year = ? 
                                   LIMIT 1";
                $stmt_sy = $con->prepare($sched_year_sql);
                $stmt_sy->bind_param("ii", $p['lease_id'], $py);
                $stmt_sy->execute();
                $res_sy = $stmt_sy->get_result();
                if ($res_sy->num_rows > 0) {
                    $target_schedule = $res_sy->fetch_assoc();
                }
            }

            if (!$target_schedule) {
                continue; // nothing to allocate to
            }

            // Recompute total outstanding premium (lease-wide)
            $prem_sum_sql = "SELECT COALESCE(SUM(premium),0) AS total_premium, 
                                    COALESCE(SUM(premium_paid),0) AS total_premium_paid 
                             FROM lease_schedules 
                             WHERE lease_id = ?";
            $stmt_pm = $con->prepare($prem_sum_sql);
            $stmt_pm->bind_param("i", $p['lease_id']);
            $stmt_pm->execute();
            $prem_res = $stmt_pm->get_result();
            $prem_row = $prem_res->fetch_assoc();
            $total_premium       = floatval($prem_row['total_premium'] ?? 0);
            $total_premium_paid  = floatval($prem_row['total_premium_paid'] ?? 0);
            $premium_outstanding = max(0, $total_premium - $total_premium_paid);

            // Recompute penalties up to this schedule's end date
            $penalty_sum_sql = "SELECT SUM(COALESCE(panalty,0)) as total_penalty, 
                                       SUM(COALESCE(panalty_paid,0)) as total_penalty_paid
                                FROM lease_schedules 
                                WHERE lease_id = ? AND end_date <= ?";
            $stmt_ps = $con->prepare($penalty_sum_sql);
            $stmt_ps->bind_param("is", $p['lease_id'], $target_schedule['end_date']);
            $stmt_ps->execute();
            $pen_res = $stmt_ps->get_result();
            $pen_row = $pen_res->fetch_assoc();
            $total_penalty             = floatval($pen_row['total_penalty'] ?? 0);
            $total_penalty_paid        = floatval($pen_row['total_penalty_paid'] ?? 0);
            $total_outstanding_penalty = $total_penalty - $total_penalty_paid;

            // Allocate this payment: premium -> penalty -> rent
            $remaining_payment = $p_amount;
            $premium_payment   = 0; 
            $penalty_payment   = 0; 
            $rent_payment      = 0;

            if ($premium_outstanding > 0 && $remaining_payment > 0) {
                $premium_payment   = min($premium_outstanding, $remaining_payment);
                $remaining_payment -= $premium_payment;
            }

            if ($total_outstanding_penalty > 0 && $remaining_payment > 0) {
                $penalty_payment   = min($total_outstanding_penalty, $remaining_payment);
                $remaining_payment -= $penalty_payment;
            }

            $rent_payment = $remaining_payment;

            // Update the target schedule by incrementing values
            $update_sched_sql = "UPDATE lease_schedules SET 
                                 premium_paid = COALESCE(premium_paid,0) + ?,
                                 panalty_paid = COALESCE(panalty_paid,0) + ?,
                                 paid_rent    = COALESCE(paid_rent,0) + ?,
                                 total_paid   = COALESCE(total_paid,0) + ?
                                 WHERE schedule_id = ?";
            $stmt_upd = $con->prepare($update_sched_sql);
            // 4 decimals + 1 int
            $stmt_upd->bind_param("ddddi", $premium_payment, $penalty_payment, $rent_payment, $p_amount, $target_schedule['schedule_id']);
            if (!$stmt_upd->execute()) {
                throw new Exception('Failed to apply payment during replay: ' . $stmt_upd->error);
            }
        }

        // Commit transaction after replay
        $con->commit();
        
        // Log the action
        if (function_exists('UserLog')) {
            $ben_id = intval($payment['beneficiary_id'] ?? 0);
            UserLog(
                '2', 
                'LTL Cancel Payment', 
                "Cancelled payment: {$payment['reference_number']}, Amount: {$payment['amount']}, Lease_file: {$payment['file_number']}",
                $ben_id
            );
        }

        // Trigger penalty recalculation for this specific lease
        try {
            $_REQUEST['lease_id'] = $payment['lease_id'];
            ob_start();
            include '../cal_panalty.php';
            ob_get_clean();
        } catch (Exception $e) {
            // Non-fatal
        }

        $response['success'] = true;
        $response['message'] = "Payment {$payment['receipt_number']} has been cancelled successfully and allocations rebuilt.";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($con->in_transaction) {
            $con->rollback();
        }
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>