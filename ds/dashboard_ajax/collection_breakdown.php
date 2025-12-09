<?php
require_once dirname(__DIR__, 2) . '/db.php';
require_once dirname(__DIR__, 2) . '/auth.php';
header('Content-Type: application/json');

date_default_timezone_set('Asia/Colombo');

$response = [
  'success' => false,
  'rent_paid' => 0.0,
  'penalty_paid' => 0.0,
  'premium_paid' => 0.0,
  'discount_apply' => 0.0,
  'location_id' => null,
  'message' => ''
];

try {
  $locParam = null;
  if (isset($_GET['location_id']) && $_GET['location_id'] !== '') {
    $locParam = (int)$_GET['location_id'];
    if ($locParam <= 0) { $locParam = null; }
  }
  $response['location_id'] = $locParam;

  $sql = "SELECT 
            COALESCE(SUM(ls.paid_rent),0) AS rent_paid,
            COALESCE(SUM(ls.panalty_paid),0) AS penalty_paid,
            COALESCE(SUM(ls.premium_paid),0) AS premium_paid,
            COALESCE(SUM(ls.discount_apply),0) AS discount_apply
          FROM lease_schedules ls
          INNER JOIN leases l ON ls.lease_id = l.lease_id";
  $types = '';
  $params = [];
  if ($locParam !== null) {
    $sql .= " WHERE l.location_id = ?";
    $types .= 'i';
    $params[] = $locParam;
  }

  if ($stmt = mysqli_prepare($con, $sql)) {
    if (!empty($params)) {
      mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (mysqli_stmt_execute($stmt)) {
      mysqli_stmt_bind_result($stmt, $rent_paid, $penalty_paid, $premium_paid, $discount_apply);
      mysqli_stmt_fetch($stmt);
      $response['success'] = true;
      $response['rent_paid'] = round((float)$rent_paid, 2);
      $response['penalty_paid'] = round((float)$penalty_paid, 2);
      $response['premium_paid'] = round((float)$premium_paid, 2);
      $response['discount_apply'] = round((float)$discount_apply, 2);
      $response['message'] = 'OK';
    } else {
      $response['message'] = 'Execution failed';
    }
    mysqli_stmt_close($stmt);
  } else {
    $response['message'] = 'Preparation failed';
  }
} catch (Throwable $e) {
  $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
