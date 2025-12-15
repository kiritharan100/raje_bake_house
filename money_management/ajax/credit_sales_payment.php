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

$bill_id = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
$payment_mode = isset($_POST['payment_mode']) ? trim($_POST['payment_mode']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_date = isset($_POST['payment_date']) ? trim($_POST['payment_date']) : '';

if ($bill_id <= 0 || $payment_mode === '' || $payment_date === '' || $amount <= 0) {
    respond(false, 'Bill, payment mode, date, and amount are required.');
}

// Fetch current balance
$stmt = $con->prepare("
    SELECT b.amount - COALESCE(p.paid_amount,0) AS balance
    FROM bill_summary b
    LEFT JOIN (
        SELECT bill_id, SUM(amount) AS paid_amount FROM bill_payment WHERE status = 1 GROUP BY bill_id
    ) p ON p.bill_id = b.bill_id
    WHERE b.bill_id = ?
    AND b.status = 1
");
if (!$stmt) {
    respond(false, 'Database error: ' . $con->error);
}
$stmt->bind_param("i", $bill_id);
if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
    respond(false, 'Bill not found or inactive.');
}
$balance = floatval($row['balance']);
if ($balance <= 0) {
    respond(false, 'No outstanding balance.');
}
if ($amount > $balance + 0.0001) {
    respond(false, 'Amount exceeds outstanding balance.');
}

$ins = $con->prepare("INSERT INTO bill_payment (bill_id, payment_mode, amount, payment_date, status) VALUES (?, ?, ?, ?, 1)");
if (!$ins) {
    respond(false, 'Database error: ' . $con->error);
}
$ins->bind_param("isds", $bill_id, $payment_mode, $amount, $payment_date);
if (!$ins->execute()) {
    respond(false, 'Database error: ' . $ins->error);
}

UserLog("Credit Sales", "Payment", "Bill ID: $bill_id, Amount: $amount, Mode: $payment_mode");

respond(true, 'Payment recorded successfully.');
