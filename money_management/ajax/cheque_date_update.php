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

$chq_id = isset($_POST['chq_id']) ? intval($_POST['chq_id']) : 0;
$cheque_date = isset($_POST['cheque_date']) ? trim($_POST['cheque_date']) : '';

if ($chq_id <= 0 || $cheque_date === '') {
    respond(false, 'Cheque ID and new cheque date are required.');
}

$stmt = $con->prepare("UPDATE bank_cheque_payment SET cheque_date = ? WHERE chq_id = ?");
if ($stmt === false) {
    respond(false, 'Database error: ' . $con->error);
}
$stmt->bind_param("si", $cheque_date, $chq_id);

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

UserLog("Bank Cheque", "Date Changed", "Cheque ID: $chq_id to date: $cheque_date");

respond(true, 'Cheque date updated successfully.');
