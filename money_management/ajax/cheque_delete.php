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

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? intval($_POST['status']) : 0;

if ($id <= 0) {
    respond(false, 'Invalid cheque ID.');
}

$status = $status === 1 ? 1 : 0;

$stmt = $con->prepare("UPDATE bank_cheque_payment SET status = ? WHERE chq_id = ?");
if ($stmt === false) {
    respond(false, 'Database error: ' . $con->error);
}
$stmt->bind_param("ii", $status, $id);

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$action = $status === 1 ? 'Activated' : 'Deactivated';
UserLog("Bank Cheque", $action, "Cheque ID: $id");

respond(true, "Cheque {$action} successfully.");
