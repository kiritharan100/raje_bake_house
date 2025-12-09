<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once dirname(__DIR__, 2) . '/db.php';
header('Content-Type: application/json');

$response = ['success'=>false,'message'=>''];

try{
    // ----------------------------------------------------
    // Resolve location_id from cookie (same as create)
    // ----------------------------------------------------
    $location_id = 0;
    if(isset($_COOKIE['client_cook'])){
        $selected_client = $_COOKIE['client_cook'];
        $sel_query = "SELECT c_id FROM client_registration WHERE md5_client=? LIMIT 1";
        if ($stmtC = mysqli_prepare($con, $sel_query)){
            mysqli_stmt_bind_param($stmtC, 's', $selected_client);
            mysqli_stmt_execute($stmtC);
            $resC = mysqli_stmt_get_result($stmtC);
            if ($resC && ($rowC = mysqli_fetch_assoc($resC))) {
                $location_id = (int)$rowC['c_id'];
            }
            mysqli_stmt_close($stmtC);
        }
    }

    // ----------------------------------------------------
    // Incoming IDs
    // ----------------------------------------------------
    $lease_id        = isset($_POST['lease_id']) ? (int)$_POST['lease_id'] : 0;
    $land_id         = isset($_POST['land_id']) ? (int)$_POST['land_id'] : 0;
    $beneficiary_id  = isset($_POST['beneficiary_id']) ? (int)$_POST['beneficiary_id'] : 0;
    if ($lease_id<=0) {
        throw new Exception('Missing lease, land or beneficiary');
    }
    // if ($lease_id<=0 || $land_id<=0 || $beneficiary_id<=0) { throw new Exception('Missing lease, land or beneficiary'); }

    // ----------------------------------------------------
    // LOAD OLD LEASE VALUES (for change detection)
    // ----------------------------------------------------
    $oldLease = null;
    if ($rsOld = mysqli_query(
        $con,
        "SELECT valuation_amount, start_date, annual_rent_percentage, end_date,
                revision_period, revision_percentage, duration_years, premium,lease_number,file_number
         FROM leases WHERE lease_id={$lease_id} LIMIT 1"
    )){
        if ($rowOld = mysqli_fetch_assoc($rsOld)) {
            $oldLease = $rowOld;
        }
        mysqli_free_result($rsOld);
    }

    // ----------------------------------------------------
    // Incoming fields
    // ----------------------------------------------------
    $valuation_amount       = floatval($_POST['valuation_amount'] ?? 0);
    $valuation_date         = $_POST['valuation_date'] ?? '';
    $value_date             = $_POST['value_date'] ?? '';
    $approved_date          = $_POST['approved_date'] ?? '';
    // if (empty($valuation_date)) { throw new Exception('Letter Date is required'); }
    // if (empty($value_date)) { throw new Exception('Valuvation Date is required'); }
    // if (empty($approved_date)) { throw new Exception('Approved Date is required'); }

    $annual_rent_percentage = floatval($_POST['annual_rent_percentage'] ?? 0);
    $revision_period        = (int)($_POST['revision_period'] ?? 0);
    $revision_percentage    = floatval($_POST['revision_percentage'] ?? 0);
    $start_date             = $_POST['start_date'] ?? '';
    $end_date               = $_POST['end_date'] ?? '';
    $duration_years         = (int)($_POST['duration_years'] ?? 0);
    $lease_type_id          = isset($_POST['lease_type_id1']) ? (int)$_POST['lease_type_id1'] : 0;
    $type_of_project        = isset($_POST['type_of_project']) ? mysqli_real_escape_string($con, $_POST['type_of_project']) : '';
    $name_of_the_project    = isset($_POST['name_of_the_project']) ? mysqli_real_escape_string($con, $_POST['name_of_the_project']) : '';
    $lease_number           = isset($_POST['lease_number']) ? mysqli_real_escape_string($con, $_POST['lease_number']) : '';
    $file_number            = isset($_POST['file_number']) ? mysqli_real_escape_string($con, $_POST['file_number']) : '';
    $premium_input          = isset($_POST['premium']) ? floatval(str_replace(',', '', $_POST['premium'])) : 0.0;


    // ----------------------------------------------------
    // Payment existence check (only active payments)
    // ----------------------------------------------------


    $changes = [];

// Helper to compare safely
 function detectChange($label, $oldValue, $newValue, &$changes) {

    // Convert to trimmed string
    $o = trim((string)$oldValue);
    $n = trim((string)$newValue);

    // Normalize null conditions
    $nullValues = ["", "null", "NULL", "0.00", "0", "0000-00-00"];

    if (in_array($o, $nullValues, true)) $o = "null";
    if (in_array($n, $nullValues, true)) $n = "null";

    // If both null → ignore
    if ($o === "null" && $n === "null") {
        return; // no change
    }

    // If both are numeric → compare numerically
    if (is_numeric($o) && is_numeric($n)) {
        if (floatval($o) == floatval($n)) {
            return; // no change
        }
        // numeric difference → format to 2 decimals
        $oFormatted = number_format(floatval($o), 2, '.', '');
        $nFormatted = number_format(floatval($n), 2, '.', '');
        $changes[] = "$label: $oFormatted > $nFormatted";
        return;
    }

    // Compare as strings
    if ($o !== $n) {
        $changes[] = "$label: $o > $n";
    }
}



detectChange("valuation_amount",       $oldLease['valuation_amount'] ?? "", $valuation_amount, $changes);
detectChange("valuation_date",         $oldLease['valuation_date'] ?? "",   $valuation_date, $changes);
detectChange("value_date",             $oldLease['value_date'] ?? "",       $value_date, $changes);
detectChange("approved_date",          $oldLease['approved_date'] ?? "",    $approved_date, $changes);
detectChange("annual_rent_percentage", $oldLease['annual_rent_percentage'] ?? "", $annual_rent_percentage, $changes);
detectChange("revision_period",        $oldLease['revision_period'] ?? "",  $revision_period, $changes);
detectChange("revision_percentage",    $oldLease['revision_percentage'] ?? "", $revision_percentage, $changes);
detectChange("start_date",             $oldLease['start_date'] ?? "",       $start_date, $changes);
detectChange("end_date",               $oldLease['end_date'] ?? "",         $end_date, $changes);
detectChange("duration_years",         $oldLease['duration_years'] ?? "",   $duration_years, $changes);
detectChange("premium",                $oldLease['premium'] ?? "",          $premium, $changes);
detectChange("lease_number",           $oldLease['lease_number'] ?? "",    $lease_number, $changes);    
detectChange("file_number",            $oldLease['file_number'] ?? "",     $file_number, $changes);



    $payments_count = 0;
    if ($stP = mysqli_prepare($con, 'SELECT COUNT(*) AS cnt FROM lease_payments WHERE lease_id=? AND status=1')){
        mysqli_stmt_bind_param($stP, 'i', $lease_id);
        mysqli_stmt_execute($stP);
        $rp = mysqli_stmt_get_result($stP);
        if ($rp && ($row = mysqli_fetch_assoc($rp))) { 
            $payments_count = (int)$row['cnt']; 
        }
        mysqli_stmt_close($stP);
    }

    // ----------------------------------------------------
    // Compute effective percentage (economy vs base)
    // ----------------------------------------------------
    $effective_pct = $annual_rent_percentage;
    if ($lease_type_id > 0) {
        $q = "SELECT base_rent_percent, economy_rate, economy_valuvation 
              FROM lease_master WHERE lease_type_id=$lease_type_id LIMIT 1";
        if ($rs = mysqli_query($con, $q)) {
            if ($lm = mysqli_fetch_assoc($rs)) {
                $base_pct = isset($lm['base_rent_percent']) ? floatval($lm['base_rent_percent']) : 0.0;
                $eco_rate = isset($lm['economy_rate']) ? floatval($lm['economy_rate']) : 0.0;
                $eco_val  = isset($lm['economy_valuvation']) ? floatval($lm['economy_valuvation']) : 0.0;
                if ($valuation_amount > 0 && $eco_val > 0 && $eco_rate > 0 && $valuation_amount <= $eco_val) {
                    $effective_pct = $eco_rate;
                } else {
                    $effective_pct = $base_pct > 0 ? $base_pct : $annual_rent_percentage;
                }
            }
            mysqli_free_result($rs);
        }
    }
    $annual_rent_percentage = $effective_pct;

    $uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    // ----------------------------------------------------
    // Recalculate premium (same rule as before)
    // ----------------------------------------------------

     $initial_annual_rent = $valuation_amount * ($annual_rent_percentage / 100.0);
    $note = '';

    
    $premium = 0.0;
    if (!empty($start_date) && strtotime($start_date) < strtotime('2020-01-01')) {
             $sql = "SELECT * FROM lease_master WHERE lease_type_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $lease_type_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $premium_times = floatval($row['premium_times']); 
        } else {
            $premium_times = 0.00;  // default if not found
        }


    $premium = $initial_annual_rent * $premium_times;
    
    }

    // ----------------------------------------------------
    // DETECT IF WE NEED FULL REBUILD + REPLAY
    // (valuation, start_date, % changed)
    // ----------------------------------------------------
    $need_rebuild_and_replay = false;
    if ($oldLease){
        $old_val   = floatval($oldLease['valuation_amount'] ?? 0);
        $old_start = $oldLease['start_date'] ?? '';
        $old_pct   = floatval($oldLease['annual_rent_percentage'] ?? 0);

        // if (round($old_val,2) != round($valuation_amount,2) ||
        //     $old_start != $start_date ||
        //     round($old_pct,4) != round($annual_rent_percentage,4)) {
        //     $need_rebuild_and_replay = true;
        // }

                  if (
              round($old_val,2) != round($valuation_amount,2) ||
              $old_start != $start_date ||
              round($old_pct,4) != round($annual_rent_percentage,4) ||
              (int)$oldLease['revision_period'] != (int)$revision_period ||
              round($oldLease['revision_percentage'],4) != round($revision_percentage,4) ||
              (int)$oldLease['duration_years'] != (int)$duration_years
          ) {
              $need_rebuild_and_replay = true;
          }
 

    }

    // ----------------------------------------------------
    // UPDATE LEASE
    // ----------------------------------------------------
    $sql = "UPDATE leases SET 
                beneficiary_id=?,
                location_id=?,
                lease_number=?,
                file_number=?,
                valuation_amount=?,
                valuation_date=?,
                value_date=?,
                approved_date=?,
                premium=?,
                annual_rent_percentage=?,
                revision_period=?,
                revision_percentage=?,
                start_date=?,
                end_date=?,
                duration_years=?,
                name_of_the_project=?,
                updated_by=?,
                updated_on=NOW()
            WHERE lease_id=?";

    if ($stmt = mysqli_prepare($con, $sql)){
        mysqli_stmt_bind_param(
            $stmt,
            'iissdsssddidssisii',
            $beneficiary_id, $location_id, $lease_number, $file_number,
            $valuation_amount, $valuation_date, $value_date, $approved_date,
            $premium, $annual_rent_percentage, $revision_period,
            $revision_percentage, $start_date, $end_date, $duration_years,
            $name_of_the_project, $uid, $lease_id
        );
        if (!mysqli_stmt_execute($stmt)){
            throw new Exception('Error updating lease: ' . mysqli_error($con));
        }
        mysqli_stmt_close($stmt);
    } else {
        throw new Exception('DB error: ' . mysqli_error($con));
    }


    if (empty($valuation_date) || $valuation_date == '0000-00-00') {

    // Remove all penalties
    mysqli_query($con, "
        UPDATE lease_schedules 
        SET panalty = 0,
            panalty_paid = 0
        WHERE lease_id = {$lease_id}
    ");

    // Flag to SKIP penalty calculation later
    $skip_penalty = true;

} else {
    $skip_penalty = false;
}

    // ----------------------------------------------------
    // SCHEDULE + PAYMENT HANDLING (NO DUPLICATES)
    // ----------------------------------------------------
   

    if ($need_rebuild_and_replay) {

        // Key fields changed → delete schedules + regenerate + replay payments
        if (!rebuildSchedulesAndReapplyPayments(
            $con,
            $lease_id,
            $initial_annual_rent,
            $premium,
            $revision_period,
            $revision_percentage,
            $start_date,
            $duration_years
        )){
            throw new Exception('Failed to rebuild schedules and reprocess payments.');
        }
 
        $note =  ' Schedules regenerated and payments reprocessed (because lease values changed).';

    } else {


        // Key values NOT changed
        if ($payments_count === 0) {

            // No payments → safe to fully regenerate schedules
            mysqli_query($con, "DELETE FROM lease_schedules WHERE lease_id=$lease_id");

            if (!generateLeaseSchedules(
                $con,
                $lease_id,
                $initial_annual_rent,
                $premium,
                $revision_period,
                $revision_percentage,
                $start_date,
                $duration_years
            )){
                throw new Exception('Failed to regenerate schedules.');
            }

            if (!$skip_penalty) {
                try {
                    $_REQUEST['lease_id'] = $lease_id;
                    ob_start();
                    include __DIR__ . '/../cal_panalty.php';
                    ob_end_clean();
                } catch (Exception $e) { }
            }

            $note = ' Schedules regenerated (no payments exist).';

        } else {
            // Payments exist + no key field change → DO NOTHING
            $note = ' Payments exist. Schedules NOT regenerated.';
        }
    }

    // ----------------------------------------------------
    // Recalculate penalties for this lease (final)
    // ----------------------------------------------------
    // try {
    //     $_REQUEST['lease_id'] = $lease_id;
    //     ob_start();
    //     include __DIR__ . '/../cal_panalty.php';
    //     ob_end_clean();
    // } catch (Exception $e) {
    //     // non-fatal
    // }


    // ----------------------------------------------------
// Run penalty calculation ONLY if valuation_date exists
// ----------------------------------------------------
if (!$skip_penalty) {
    try {
        $_REQUEST['lease_id'] = $lease_id;
        ob_start();
        include __DIR__ . '/../cal_panalty.php';
        ob_end_clean();
    } catch (Exception $e) { }
}



  if (function_exists('UserLog')) {

    if (count($changes) > 0) {
        $change_text = implode(" | ", $changes);
            $log_msg = "Lease ID=$lease_id | Lease No=$lease_number | Changes: $change_text";
            UserLog(
                "2",
                "LTL Lease Updated",
                $log_msg,
                $beneficiary_id // saves ben_id in log
            );
    } 


}


    $response['success']  = true;
    $response['lease_id'] = $lease_id;
    $response['message']  = 'Lease updated successfully!' . $note;

} catch (Exception $ex){
    $response['success'] = false;
    $response['message'] = $ex->getMessage();
}

echo json_encode($response);

/**
 * Regenerate schedules and replay all ACTIVE (status=1) payments
 * using the same allocation + discount logic as the payment AJAX.
 */
function rebuildSchedulesAndReapplyPayments(
    $con,
    $lease_id,
    $initial_rent,
    $premium,
    $revision_period,
    $revision_percentage,
    $start_date,
    $duration_years = 30
){
    // 1) Load all active payments (status = 1) in date order
    $payments = [];
    if ($st = mysqli_prepare(
        $con,
        "SELECT * FROM lease_payments 
         WHERE lease_id=? AND status=1 
         ORDER BY payment_date ASC, payment_id ASC"
    )){
        mysqli_stmt_bind_param($st, 'i', $lease_id);
        mysqli_stmt_execute($st);
        $rs = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($rs)) {
            $payments[] = $row;
        }
        mysqli_stmt_close($st);
    }

    // 2) Delete existing schedules for this lease
    if ($stDel = mysqli_prepare($con, 'DELETE FROM lease_schedules WHERE lease_id=?')){
        mysqli_stmt_bind_param($stDel, 'i', $lease_id);
        mysqli_stmt_execute($stDel);
        mysqli_stmt_close($stDel);
    }

    // 3) Regenerate schedules from scratch
    if (!generateLeaseSchedules(
        $con,
        $lease_id,
        $initial_rent,
        $premium,
        $revision_period,
        $revision_percentage,
        $start_date,
        $duration_years
    )){
        return false;
    }

    // 4) Recalculate penalties once so panalty values exist
    try {
        $_REQUEST['lease_id'] = $lease_id;
        ob_start();
        include __DIR__ . '/../cal_panalty.php';
        ob_end_clean();
    } catch (Exception $e) {
        // non-fatal
    }

    // 5) Replay each payment in date order using same logic
    foreach ($payments as $pay) {
        if (!applyPaymentToSchedules($con, $lease_id, $pay)) {
            return false;
        }
    }

    return true;
}

/**
 * Apply ONE payment row again to schedules & update the row
 * Logic is copied from your Ajax payment script.
 */
function applyPaymentToSchedules($con, $lease_id, $paymentRow){
    $payment_id     = (int)$paymentRow['payment_id'];
    $payment_date   = $paymentRow['payment_date'];
    $amount         = floatval($paymentRow['amount']);
    // $location_id    = (int)$paymentRow['location_id']; // not needed for reallocation
    $payment_method = $paymentRow['payment_method'];
    $reference_num  = $paymentRow['reference_number'];
    $notes          = $paymentRow['notes'];
    $user_id        = (int)$paymentRow['created_by'];

    if ($amount <= 0) {
        // nothing to apply
        return true;
    }

    // -------------------------------------------------
    // FIND CURRENT SCHEDULE (same as Ajax)
    // -------------------------------------------------
    $schedule_id = null;

    $schedule_check = "SELECT schedule_id, start_date, end_date, schedule_year 
                       FROM lease_schedules 
                       WHERE lease_id = ? AND ? BETWEEN start_date AND end_date 
                       LIMIT 1";
    if ($stmt_check = $con->prepare($schedule_check)){
        $stmt_check->bind_param("is", $lease_id, $payment_date);
        $stmt_check->execute();
        $schedule_result = $stmt_check->get_result();
        if ($schedule_result->num_rows > 0) {
            $curSched = $schedule_result->fetch_assoc();
            $schedule_id = $curSched['schedule_id'];
        }
        $stmt_check->close();
    }

    if (!$schedule_id){
        $next_sql = "SELECT schedule_id, start_date 
                     FROM lease_schedules 
                     WHERE lease_id=? AND start_date > ? 
                     ORDER BY start_date ASC LIMIT 1";
        if ($stmt_n = $con->prepare($next_sql)){
            $stmt_n->bind_param("is",$lease_id,$payment_date);
            $stmt_n->execute();
            $next_rs = $stmt_n->get_result();
            if($next_rs->num_rows>0){
                $curSched   = $next_rs->fetch_assoc();
                $schedule_id= $curSched['schedule_id'];
            }
            $stmt_n->close();
        }
    }

    if(!$schedule_id){
        // Cannot find schedule for this payment – do not fail the whole process
        return false;
    }

    // -------------------------------------------------
    // LOAD CURRENT SCHEDULE ROW
    // -------------------------------------------------
    $cur = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT * FROM lease_schedules WHERE schedule_id=".$schedule_id." LIMIT 1")
    );

    $current_annual_amount     = floatval($cur['annual_amount']);
    $current_rent_paid_so_far  = floatval($cur['paid_rent']);
    $current_schedule_start    = $cur['start_date'];
    $current_schedule_end      = $cur['end_date'];
    $existing_discount_applied = floatval($cur['discount_apply']);

    // -------------------------------------------------
    // FIND NEXT SCHEDULE
    // -------------------------------------------------
    $nxrs = mysqli_query(
        $con,
        "SELECT schedule_id, annual_amount
         FROM lease_schedules
         WHERE lease_id=".$lease_id." 
           AND start_date > '".$current_schedule_start."'
         ORDER BY start_date ASC LIMIT 1"
    );

    $has_next_schedule = false;
    $next_schedule_id  = null;
    $next_annual_amount= 0.0;

    if($nxrs && mysqli_num_rows($nxrs)>0){
        $nx                = mysqli_fetch_assoc($nxrs);
        $has_next_schedule = true;
        $next_schedule_id  = $nx['schedule_id'];
        $next_annual_amount= floatval($nx['annual_amount']);
    }

    // -------------------------------------------------
    // ARREARS CHECK (cumulative before this schedule)
    // -------------------------------------------------
    $arrears_sql = "
        SELECT 
            SUM(COALESCE(annual_amount,0))  AS sum_annual,
            SUM(COALESCE(paid_rent,0))      AS sum_paid_rent,
            SUM(COALESCE(panalty,0))        AS sum_pen,
            SUM(COALESCE(panalty_paid,0))   AS sum_pen_paid,
            SUM(COALESCE(premium,0))        AS sum_prem,
            SUM(COALESCE(premium_paid,0))   AS sum_prem_paid
        FROM lease_schedules
        WHERE lease_id = {$lease_id}
          AND end_date < '{$current_schedule_start}'";

    $ar = mysqli_fetch_assoc(mysqli_query($con,$arrears_sql));

    $sum_annual    = floatval($ar['sum_annual'] ?? 0);
    $sum_paid_rent = floatval($ar['sum_paid_rent'] ?? 0);
    $sum_pen       = floatval($ar['sum_pen'] ?? 0);
    $sum_pen_paid  = floatval($ar['sum_pen_paid'] ?? 0);
    $sum_prem      = floatval($ar['sum_prem'] ?? 0);
    $sum_prem_paid = floatval($ar['sum_prem_paid'] ?? 0);

    $outstanding_before = max(0, $sum_annual - $sum_paid_rent)
                        + max(0, $sum_pen    - $sum_pen_paid)
                        + max(0, $sum_prem   - $sum_prem_paid);

    $no_arrears_before_start = ($outstanding_before < 0.005);

    // -------------------------------------------------
    // PAYMENT ALLOCATION: premium → penalty → rent(current)
    // -------------------------------------------------
    $remaining_payment = $amount;

    // Premium
    $cur_premium         = floatval($cur['premium']);
    $cur_premium_paid    = floatval($cur['premium_paid']);
    $premium_outstanding = max(0,$cur_premium - $cur_premium_paid);
    $premium_payment_now = min($remaining_payment,$premium_outstanding);
    $remaining_payment  -= $premium_payment_now;

    // Penalty
    $pen_rs = mysqli_fetch_assoc(mysqli_query(
        $con,"SELECT 
                SUM(COALESCE(panalty,0)) AS p, 
                SUM(COALESCE(panalty_paid,0)) AS pp
              FROM lease_schedules
              WHERE lease_id=".$lease_id." AND end_date <= '".$cur['end_date']."'"
    ));
    $out_pen        = max(0, floatval($pen_rs['p']) - floatval($pen_rs['pp']));
    $penalty_payment= min($remaining_payment,$out_pen);
    $remaining_payment -= $penalty_payment;

    // Rent (current year)
    $rent_payment_current = $remaining_payment;
    $remaining_payment    = 0;

    // -------------------------------------------------
    // DISCOUNT LOGIC
    // -------------------------------------------------


        //     $sql = "SELECT * FROM lease_master WHERE lease_type_id = ?";
        // $stmt = $con->prepare($sql);
        // $stmt->bind_param("i", $lease_type_id);
        // $stmt->execute();
        // $result = $stmt->get_result();

        // if ($row = $result->fetch_assoc()) {
        //     $discount_rate = floatval($row['discount_rate']/100); 
        // } else {
        //     $discount_rate = 0.00;  // default if not found
        // }

        $discount_rate = 0;

// Get discount_rate from lease_master using lease_id
$sql = "
    SELECT lm.discount_rate 
    FROM leases l
    LEFT JOIN lease_master lm 
        ON l.lease_type_id = lm.lease_type_id
    WHERE l.lease_id = $lease_id
    LIMIT 1
";

$result = mysqli_query($con, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $discount_rate = floatval($row['discount_rate']) / 100;
}


    // $discount_rate     = 0.10;
    $discount_amount   = 0.0;
    $discount_applied  = false;

    $payment_ts  = strtotime($payment_date);
    $deadline_ts = strtotime($current_schedule_start." +30 days");
    $within_window = ($payment_ts <= $deadline_ts);

    $prospective_rent  = $current_rent_paid_so_far + $rent_payment_current;
    $min_for_discount  = $current_annual_amount * 0.90;

    if($existing_discount_applied == 0){
        // CASE A: discount for current period
        if($within_window && $no_arrears_before_start && $prospective_rent >= $min_for_discount){
            $discount_amount  = $current_annual_amount * $discount_rate;
            $discount_applied = true;
        }

        // CASE B: next-period early discount
        if(!$discount_applied && $has_next_schedule && $within_window){
            if($prospective_rent >= $current_annual_amount && $no_arrears_before_start){
                $discount_amount  = $next_annual_amount * $discount_rate;
                $discount_applied = true;
            }
        }
    }

    // -------------------------------------------------
    // RENT REALLOCATION IF DISCOUNT APPLIED
    // -------------------------------------------------
    $rent_payment_next = 0.0;

    if($discount_applied){
        $max_current_rent = $current_annual_amount - $discount_amount;
        $new_total_rent   = $current_rent_paid_so_far + $rent_payment_current;

        if($new_total_rent > $max_current_rent && $has_next_schedule){
            $rent_payment_next   = $new_total_rent - $max_current_rent;
            $rent_payment_current-= $rent_payment_next;

            if($rent_payment_current < 0){
                $rent_payment_next += $rent_payment_current;
                $rent_payment_current = 0;
            }
        }
    }

    // -------------------------------------------------
    // UPDATE PAYMENT ROW (reprocessed)
    // -------------------------------------------------
    $current_year_payment = $rent_payment_current;
    $total_rent_paid      = $rent_payment_current + $rent_payment_next;
    $discount_to_apply    = $discount_applied ? $discount_amount : 0.0;
    $payment_type         = 'mixed';

    if ($stUp = mysqli_prepare(
        $con,
        "UPDATE lease_payments SET 
            schedule_id=?,
            rent_paid=?,
            panalty_paid=?,
            premium_paid=?,
            discount_apply=?,
            current_year_payment=?,
            payment_type=?
         WHERE payment_id=?"
    )){
        mysqli_stmt_bind_param(
            $stUp,
            'idddddsi',
            $schedule_id,
            $total_rent_paid,
            $penalty_payment,
            $premium_payment_now,
            $discount_to_apply,
            $current_year_payment,
            $payment_type,
            $payment_id
        );
        if (!mysqli_stmt_execute($stUp)){
            mysqli_stmt_close($stUp);
            return false;
        }
        mysqli_stmt_close($stUp);
    } else {
        return false;
    }

    // -------------------------------------------------
    // UPDATE CURRENT SCHEDULE AMOUNTS
    // -------------------------------------------------
    mysqli_query($con,
        "UPDATE lease_schedules SET 
            paid_rent     = paid_rent + {$rent_payment_current},
            panalty_paid  = panalty_paid + {$penalty_payment},
            premium_paid  = premium_paid + {$premium_payment_now},
            total_paid    = total_paid + ".($rent_payment_current + $penalty_payment + $premium_payment_now).",
            discount_apply= discount_apply + {$discount_to_apply}
         WHERE schedule_id={$schedule_id}"
    );

    // Next schedule advance
    if($rent_payment_next > 0 && $has_next_schedule){
        mysqli_query($con,
            "UPDATE lease_schedules SET 
                paid_rent = paid_rent + {$rent_payment_next},
                total_paid= total_paid + {$rent_payment_next}
             WHERE schedule_id={$next_schedule_id}"
        );
    }

    return true;
}
 
 function generateLeaseSchedules(
    $con, 
    $lease_id, 
    $initial_rent, 
    $premium, 
    $revision_period, 
    $revision_percentage, 
    $start_date, 
    $duration_years = 30
){
    $start_ts = strtotime($start_date);
    if (!$start_ts) return false;

    $boundary_ts = strtotime('2020-01-01');
    $start_year  = (int)date('Y', $start_ts);
    $duration    = (int)$duration_years;
    if ($duration <= 0) return false;

    // ------------------------------------
    // PRE-2020 RULE APPLIES ONLY IF revision_period > 0
    // ------------------------------------
    $use_pre_rules = ($start_ts < $boundary_ts && $revision_period > 0);

    $pre_period_years = 5;
    $pre_pct          = 50.0;

    $post_period_years = ($revision_period > 0) ? (int)$revision_period : 0;
    $post_pct          = (float)$revision_percentage;

    // Determine FIRST revision date
    if ($use_pre_rules) {
        $next_rev_ts = strtotime("+{$pre_period_years} years", $start_ts);
    } else {
        $next_rev_ts = ($post_period_years > 0)
            ? strtotime("+{$post_period_years} years", $start_ts)
            : null;
    }

    // ------------------------------------
    // Load existing schedules
    // ------------------------------------
    $existing = [];
    $sqlEx = "SELECT schedule_id FROM lease_schedules WHERE lease_id=? ORDER BY schedule_year ASC";
    if ($stEx = mysqli_prepare($con, $sqlEx)) {
        mysqli_stmt_bind_param($stEx, 'i', $lease_id);
        mysqli_stmt_execute($stEx);
        $rsEx = mysqli_stmt_get_result($stEx);
        while ($row = mysqli_fetch_assoc($rsEx)) {
            $existing[] = $row;
        }
        mysqli_stmt_close($stEx);
    }
    $existingCount = count($existing);

    // ------------------------------------d
    // Generate Yearly Schedules
    // ------------------------------------
    $current_rent    = (float)$initial_rent;
    $revision_number = 0;

    for ($year = 0; $year < $duration; $year++) {

        $year_start_ts = strtotime("+{$year} years", $start_ts);
        $year_end_ts   = strtotime("+1 year -1 day", $year_start_ts);

        $schedule_year   = (int)date('Y', $year_start_ts);
        $year_start_date = date('Y-m-d', $year_start_ts);
        $year_end_date   = date('Y-m-d', $year_end_ts);
        $due_date        = date('Y-m-d', strtotime($schedule_year . "-03-31"));

        $is_revision_year = 0;

        // ------------------------------------------------
        // REVISION LOGIC (Pre + Post)
        // ------------------------------------------------
        if ($next_rev_ts && $year_start_ts >= $next_rev_ts) {

            $is_revision_year = 1;
            $revision_number++;

            // Determine which rule applies
            $apply_pre_rule = ($use_pre_rules && $next_rev_ts < $boundary_ts);
            $pct_to_apply   = $apply_pre_rule ? $pre_pct : $post_pct;

            if ($pct_to_apply > 0) {
                $current_rent = $current_rent * (1 + ($pct_to_apply / 100.0));
            }

            // Compute next revision point
            if ($apply_pre_rule) {

                // Continue 5-year rule until 2020
                $candidate = strtotime("+{$pre_period_years} years", $next_rev_ts);

                if ($candidate < $boundary_ts) {
                    $next_rev_ts = $candidate;
                } else {
                    // Switch to post rules
                    $next_rev_ts = ($post_period_years > 0)
                        ? strtotime("+{$post_period_years} years", $next_rev_ts)
                        : null;
                }

            } else {
                // Pure post-2020 revisions
                $next_rev_ts = ($post_period_years > 0)
                    ? strtotime("+{$post_period_years} years", $next_rev_ts)
                    : null;
            }
        }

        // ------------------------------------
        // Premium (first year only)
        // ------------------------------------
        $first_year_premium = ($year == 0 && $start_ts < $boundary_ts)
            ? $premium
            : 0.0;

        // ------------------------------------
        // UPDATE existing OR INSERT new row
        // ------------------------------------
        if ($year < $existingCount) {

            $schedule_id = $existing[$year]['schedule_id'];

            $sqlUp = "UPDATE lease_schedules SET
                        schedule_year=?, start_date=?, end_date=?, due_date=?,
                        base_amount=?, premium=?, annual_amount=?,
                        revision_number=?, is_revision_year=?
                      WHERE schedule_id=?";

            $stUp = mysqli_prepare($con, $sqlUp);
            mysqli_stmt_bind_param(
                $stUp, 'isssdddiii',
                $schedule_year,
                $year_start_date, $year_end_date, $due_date,
                $initial_rent, $first_year_premium,
                $current_rent,
                $revision_number, $is_revision_year,
                $schedule_id
            );
            mysqli_stmt_execute($stUp);
            mysqli_stmt_close($stUp);

        } else {

            // Insert new schedule row
            $sqlIns = "INSERT INTO lease_schedules (
                        lease_id, schedule_year, start_date, end_date, due_date,
                        base_amount, premium, premium_paid, annual_amount,
                        revision_number, is_revision_year, status, created_on
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, 'pending', NOW()
                    )";

            $stIns = mysqli_prepare($con, $sqlIns);
            mysqli_stmt_bind_param(
                $stIns, 'iisssdddii',
                $lease_id, $schedule_year,
                $year_start_date, $year_end_date, $due_date,
                $initial_rent, $first_year_premium,
                $current_rent,
                $revision_number, $is_revision_year
            );
            mysqli_stmt_execute($stIns);
            mysqli_stmt_close($stIns);
        }
    }

    return true;
}

 