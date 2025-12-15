<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;
if ($bill_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid bill id.']);
    exit;
}

$stmt = $con->prepare("
    SELECT d.id, d.bill_id, d.p_id, d.quantity, d.price, d.value, d.status, i.product_name
    FROM bill_detail d
    LEFT JOIN bill_items i ON i.p_id = d.p_id
    WHERE d.bill_id = ?
");
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
