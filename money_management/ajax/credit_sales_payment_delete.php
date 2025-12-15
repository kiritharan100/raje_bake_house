<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

function respond($success, $message)
{
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.');
}

$pay_id = isset($_POST['pay_id']) ? intval($_POST['pay_id']) : 0;

if ($pay_id <= 0) {
    respond(false, 'Invalid payment id.');
}

$stmt = $con->prepare("UPDATE bill_payment SET status = 0 WHERE pay_id = ?");
if (!$stmt) {
    respond(false, 'Database error: ' . $con->error);
}
$stmt->bind_param("i", $pay_id);
if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

UserLog("Credit Sales", "Payment Delete", "Payment ID: $pay_id");

respond(true, 'Payment deleted.');
