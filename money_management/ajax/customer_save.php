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

$cus_id         = isset($_POST['cus_id']) ? intval($_POST['cus_id']) : 0;
$customer_name  = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($customer_name === '') {
    respond(false, 'Customer name is required.');
}

$status = $status === 0 ? 0 : 1;

if ($cus_id > 0) {
    $stmt = $con->prepare("UPDATE manage_customers SET customer_name = ?, contact_number = ?, status = ? WHERE cus_id = ?");
    if (!$stmt) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("ssii", $customer_name, $contact_number, $status, $cus_id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO manage_customers (customer_name, contact_number, status) VALUES (?, ?, ?)");
    if (!$stmt) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("ssi", $customer_name, $contact_number, $status);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $cus_id > 0 ? $cus_id : $stmt->insert_id;
UserLog("Customer", $action, "Customer ID: $recordId - $customer_name");

respond(true, "Customer {$action} successfully.");
