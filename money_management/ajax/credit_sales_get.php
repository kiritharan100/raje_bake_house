<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';

$conditions = [];
$params = [];
$types = '';

if ($from_date) {
    $conditions[] = "b.date >= ?";
    $types .= "s";
    $params[] = $from_date;
}
if ($to_date) {
    $conditions[] = "b.date <= ?";
    $types .= "s";
    $params[] = $to_date;
}

$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT 
        b.bill_id,
        b.customer_id,
        b.date,
        b.bill_no,
        b.amount,
        b.status,
        c.customer_name,
        COALESCE(p.paid_amount, 0) AS paid_amount,
        (b.amount - COALESCE(p.paid_amount, 0)) AS balance_amount
    FROM bill_summary b
    LEFT JOIN manage_customers c ON c.cus_id = b.customer_id
    LEFT JOIN (
        SELECT bill_id, SUM(amount) AS paid_amount
        FROM bill_payment
        WHERE status = 1
        GROUP BY bill_id
    ) p ON p.bill_id = b.bill_id
    $where
    ORDER BY b.date DESC, b.bill_id DESC
";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $con->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $rows
]);
