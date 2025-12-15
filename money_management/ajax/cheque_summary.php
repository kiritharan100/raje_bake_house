<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "
    SELECT 
        c.contact_id,
        COALESCE(bc.contact_name, 'Unknown') AS contact_name,
        COUNT(*) AS cheque_count,
        SUM(CASE WHEN c.cheque_date = CURDATE() THEN 1 ELSE 0 END) AS today_count,
        SUM(CASE WHEN c.cheque_date = CURDATE() THEN c.amount ELSE 0 END) AS today_payable,
        SUM(CASE 
                WHEN c.cheque_date > CURDATE() AND c.cheque_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
                THEN c.amount ELSE 0 END) AS payable_7_days,
        SUM(c.amount) AS total_payable
    FROM bank_cheque_payment c
    LEFT JOIN bank_contact bc ON bc.contact_id = c.contact_id
    WHERE c.cheque_date >= CURDATE()
      AND c.status = 1
    GROUP BY c.contact_id, bc.contact_name
    ORDER BY payable_7_days DESC, bc.contact_name ASC
";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$rows = [];
$total_count = 0;
$total_today = 0;
$total_seven = 0;
$total_payable = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    $total_count += (int)$row['cheque_count'];
    $total_today += (float)$row['today_payable'];
    $total_seven += (float)$row['payable_7_days'];
    $total_payable += (float)$row['total_payable'];
}

echo json_encode([
    'success' => true,
    'data' => $rows,
    'total' => [
        'count' => $total_count,
        'today' => $total_today,
        'seven_days' => $total_seven,
        'total_payable' => $total_payable
    ]
]);
