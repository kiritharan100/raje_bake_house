<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;
if ($bill_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid bill id.']);
    exit;
}

$stmt = $con->prepare("SELECT pay_id, bill_id, payment_mode, amount, payment_date, status FROM bill_payment WHERE bill_id = ? AND status = 1 ORDER BY payment_date DESC, pay_id DESC");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $con->error]);
    exit;
}
$stmt->bind_param("i", $bill_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    exit;
}
$res = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode(['success' => true, 'data' => $rows]);
