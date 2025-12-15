<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

// Build outstanding per bill
$sql = "
    SELECT 
        b.bill_id,
        b.customer_id,
        c.customer_name,
        b.date,
        b.amount,
        (b.amount - COALESCE(p.paid_amount,0)) AS outstanding,
        DATEDIFF(CURDATE(), b.date) AS age_days
    FROM bill_summary b
    LEFT JOIN manage_customers c ON c.cus_id = b.customer_id
    LEFT JOIN (
        SELECT bill_id, SUM(amount) AS paid_amount
        FROM bill_payment
        WHERE status = 1
        GROUP BY bill_id
    ) p ON p.bill_id = b.bill_id
    WHERE b.status = 1
      AND (b.amount - COALESCE(p.paid_amount,0)) > 0
";

$result = mysqli_query($con, $sql);
if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
    exit;
}

$byCustomer = [];
while ($row = mysqli_fetch_assoc($result)) {
    $custId = $row['customer_id'];
    $name = $row['customer_name'] ?: 'Unknown';
    $out = (float)$row['outstanding'];
    $age = (int)$row['age_days'];

    if (!isset($byCustomer[$custId])) {
        $byCustomer[$custId] = [
            'customer_id' => $custId,
            'customer_name' => $name,
            'total' => 0,
            'd30' => 0,
            'd90' => 0,
            'd365' => 0,
            'dOver' => 0
        ];
    }

    $byCustomer[$custId]['total'] += $out;
    if ($age <= 30) {
        $byCustomer[$custId]['d30'] += $out;
    } elseif ($age <= 90) {
        $byCustomer[$custId]['d90'] += $out;
    } elseif ($age <= 365) {
        $byCustomer[$custId]['d365'] += $out;
    } else {
        $byCustomer[$custId]['dOver'] += $out;
    }
}

$rows = array_values($byCustomer);

$totals = ['total' => 0, 'd30' => 0, 'd90' => 0, 'd365' => 0, 'dOver' => 0];
foreach ($rows as $r) {
    $totals['total'] += $r['total'];
    $totals['d30'] += $r['d30'];
    $totals['d90'] += $r['d90'];
    $totals['d365'] += $r['d365'];
    $totals['dOver'] += $r['dOver'];
}

echo json_encode([
    'success' => true,
    'data' => $rows,
    'total' => $totals
]);
