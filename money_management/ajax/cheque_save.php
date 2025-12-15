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

$chq_id      = isset($_POST['chq_id']) ? intval($_POST['chq_id']) : 0;
$cheque_no   = isset($_POST['cheque_no']) ? trim($_POST['cheque_no']) : '';
$contact_id  = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;
$issue_date  = isset($_POST['issue_date']) ? trim($_POST['issue_date']) : '';
$cheque_date = isset($_POST['cheque_date']) ? trim($_POST['cheque_date']) : '';
$amount      = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$status      = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($cheque_no === '' || $contact_id <= 0 || $issue_date === '' || $cheque_date === '') {
    respond(false, 'Cheque no, payee, issue date, and cheque date are required.');
}

if (!is_numeric($amount) || floatval($amount) < 0) {
    respond(false, 'Please enter a valid amount.');
}

$amount = floatval($amount);
$status = ($status === 0) ? 0 : 1;

if ($chq_id > 0) {
    $stmt = $con->prepare("UPDATE bank_cheque_payment SET cheque_no = ?, contact_id = ?, issue_date = ?, cheque_date = ?, amount = ?, status = ? WHERE chq_id = ?");
    if ($stmt === false) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("sissdii", $cheque_no, $contact_id, $issue_date, $cheque_date, $amount, $status, $chq_id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO bank_cheque_payment (cheque_no, contact_id, issue_date, cheque_date, amount, status) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("sissdi", $cheque_no, $contact_id, $issue_date, $cheque_date, $amount, $status);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $chq_id > 0 ? $chq_id : $stmt->insert_id;
UserLog("Bank Cheque", $action, "Cheque ID: $recordId - $cheque_no");

respond(true, "Cheque {$action} successfully.");
