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

$contact_id     = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;
$contact_name   = isset($_POST['contact_name']) ? trim($_POST['contact_name']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($contact_name === '') {
    respond(false, 'Contact name is required.');
}

$status = ($status === 0) ? 0 : 1;

if ($contact_id > 0) {
    $stmt = $con->prepare("UPDATE bank_contact SET contact_name = ?, contact_number = ?, status = ? WHERE contact_id = ?");
    $stmt->bind_param("ssii", $contact_name, $contact_number, $status, $contact_id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO bank_contact (contact_name, contact_number, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $contact_name, $contact_number, $status);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $contact_id > 0 ? $contact_id : $stmt->insert_id;
UserLog("Bank Contact", $action, "Contact ID: $recordId - $contact_name");

respond(true, "Contact {$action} successfully.");
