<?php
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__, 2) . '/auth.php';
header('Content-Type: application/json');

date_default_timezone_set('Asia/Colombo');

$res = [
  'success' => false,
  'location_id' => null,
  'as_at' => null,
  'rent_component' => 0.0,
  'penalty_component' => 0.0,
  'premium_component' => 0.0,
  'total_outstanding' => 0.0,
  'message' => ''
];

try {
  $locParam = null;
  if (isset($_GET['location_id']) && $_GET['location_id'] !== '') {
    $locParam = (int)$_GET['location_id'];
    if ($locParam <= 0) { $locParam = null; }
  }
  $asAt = isset($_GET['as_at']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['as_at']) ? $_GET['as_at'] : date('Y-m-d');
  $leaseType = isset($_GET['lease_type']) ? $_GET['lease_type'] : 'All';
  $res['location_id'] = $locParam;
  $res['as_at'] = $asAt;

  $year = (int)substr($asAt, 0, 4);
  $prevYearEnd = ($year - 1) . '-12-31';
  $currYearStart = $year . '-01-01';

  // Load leases filtered similar to report
  $leases = [];
  $sql = "SELECT l.lease_id
          FROM leases l
          WHERE l.status!='cancelled' AND l.start_date<=?";
  $types = 's';
  $params = [$asAt];
  if ($locParam !== null) { $sql .= " AND l.location_id=?"; $types .= 'i'; $params[] = $locParam; }
  if ($leaseType !== 'All') { $sql .= " AND l.type_of_project=?"; $types .= 's'; $params[] = $leaseType; }
  $sql .= " ORDER BY l.lease_id";
  if ($st = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    $rs = mysqli_stmt_get_result($st);
    while ($row = mysqli_fetch_assoc($rs)) { $leases[] = (int)$row['lease_id']; }
    mysqli_stmt_close($st);
  }

  $rentComponentTotal = 0.0; $penaltyComponentTotal = 0.0; $premiumComponentTotal = 0.0;

  // Prepared statements for reuse
  $schSQL = "SELECT schedule_id,start_date,annual_amount,panalty,premium
             FROM lease_schedules WHERE lease_id=? AND status=1 AND start_date<=? ORDER BY start_date,schedule_id";
  $paySQL = "SELECT p.payment_date,p.rent_paid,p.current_year_payment,p.panalty_paid,p.discount_apply,p.premium_paid,
                    s.start_date AS sched_start
             FROM lease_payments p
             LEFT JOIN lease_schedules s ON s.schedule_id=p.schedule_id
             WHERE p.lease_id=? AND p.status=1 AND p.payment_date<=?";

  $schStmt = mysqli_prepare($con, $schSQL);
  $payStmt = mysqli_prepare($con, $paySQL);

  foreach ($leases as $lid) {
    // Schedules
    $sch = [];
    if ($schStmt) {
      mysqli_stmt_bind_param($schStmt, 'is', $lid, $asAt);
      mysqli_stmt_execute($schStmt);
      $rs = mysqli_stmt_get_result($schStmt);
      while ($r = mysqli_fetch_assoc($rs)) { $sch[] = $r; }
    }

    // Payments
    $pay = [];
    if ($payStmt) {
      mysqli_stmt_bind_param($payStmt, 'is', $lid, $asAt);
      mysqli_stmt_execute($payStmt);
      $rs2 = mysqli_stmt_get_result($payStmt);
      while ($r = mysqli_fetch_assoc($rs2)) { $pay[] = $r; }
    }

    // Opening & current buckets (same as report)
    $openingRentDue=0; $openingPenaltyDue=0; $openingPremiumDue=0; $currentYearRentDue=0;
    foreach ($sch as $S) {
      $sd = $S['start_date'];
      $annual = (float)$S['annual_amount'];
      $pen = (float)$S['panalty'];
      $prem = (float)$S['premium'];
      if ($sd <= $prevYearEnd) {
        $openingRentDue     += $annual;
        $openingPenaltyDue  += $pen;
        $openingPremiumDue  += $prem;
      }
      if ($sd >= $currYearStart && $sd <= $asAt) { $currentYearRentDue += $annual; }
    }

    $rentPaidOpenPrev=0; $discOpenPrev=0; $rentPaidOpenCurr=0; $penaltyPaidOpening=0; $rentPaidCurrYr=0; $discCurrYr=0; $openingPremiumPaid=0;
    foreach ($pay as $P) {
      $pd = $P['payment_date'];
      $sd = $P['sched_start'];
      $rent = (float)$P['rent_paid'] + (float)$P['current_year_payment'];
      $disc = (float)$P['discount_apply'];
      $pen  = (float)$P['panalty_paid'];
      $prem = (float)$P['premium_paid'];
      if ($sd <= $prevYearEnd) {
        if ($pd <= $prevYearEnd) { $rentPaidOpenPrev += $rent; $discOpenPrev += $disc; $openingPremiumPaid += $prem; }
        if ($pd >= $currYearStart && $pd <= $asAt) { $rentPaidOpenCurr += $rent; }
        if ($pd <= $asAt) { $penaltyPaidOpening += $pen; }
      }
      if ($sd >= $currYearStart && $sd <= $asAt) {
        if ($pd >= $currYearStart && $pd <= $asAt) { $rentPaidCurrYr += $rent; $discCurrYr += $disc; }
      }
    }

    $openingRentArrears = $openingRentDue - ($rentPaidOpenPrev + $discOpenPrev);
    $arrearsPaidCurrYr  = $rentPaidOpenCurr;
    $balanceLeaseOpening = $openingRentArrears - $arrearsPaidCurrYr;
    $openingPenaltyBal   = $openingPenaltyDue - $penaltyPaidOpening;
    $openingOutstanding  = $balanceLeaseOpening + $openingPenaltyBal;
    $outstandingCurrYr   = $currentYearRentDue - $discCurrYr - $rentPaidCurrYr;
    $totalYrEnd          = $openingOutstanding + $outstandingCurrYr;
    $openingPremiumBalance = $openingPremiumDue - $openingPremiumPaid;
    // Components
    $rentComponent   = $balanceLeaseOpening + $outstandingCurrYr;
    $penaltyComponent= $openingPenaltyBal;
    $premiumComponent= $openingPremiumBalance;

    $rentComponentTotal += $rentComponent;
    $penaltyComponentTotal += $penaltyComponent;
    $premiumComponentTotal += $premiumComponent;
  }

  $res['rent_component'] = round($rentComponentTotal, 2);
  $res['penalty_component'] = round($penaltyComponentTotal, 2);
  $res['premium_component'] = round($premiumComponentTotal, 2);
  $res['total_outstanding'] = round($rentComponentTotal + $penaltyComponentTotal + $premiumComponentTotal, 2);
  $res['success'] = true;
  $res['message'] = 'OK';

  if ($schStmt) { mysqli_stmt_close($schStmt); }
  if ($payStmt) { mysqli_stmt_close($payStmt); }

} catch (Throwable $e) {
  $res['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($res);
