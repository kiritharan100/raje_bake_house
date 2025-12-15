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

$p_id            = isset($_POST['p_id']) ? intval($_POST['p_id']) : 0;
$product_name    = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$current_price   = isset($_POST['current_price']) ? trim($_POST['current_price']) : '';
$product_category = isset($_POST['product_category']) ? trim($_POST['product_category']) : '';
$status          = isset($_POST['status']) ? intval($_POST['status']) : 1;
$order_no        = isset($_POST['order_no']) ? intval($_POST['order_no']) : 0;

if ($product_name === '' || $product_category === '') {
    respond(false, 'Product name and category are required.');
}

if (!is_numeric($current_price) || floatval($current_price) < 0) {
    respond(false, 'Please enter a valid price.');
}

$current_price = floatval($current_price);
$status = $status === 0 ? 0 : 1;

if ($p_id > 0) {
    $stmt = $con->prepare("UPDATE bill_items SET product_name = ?, current_price = ?, product_category = ?, status = ?, order_no = ? WHERE p_id = ?");
    if (!$stmt) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("sdsiii", $product_name, $current_price, $product_category, $status, $order_no, $p_id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO bill_items (product_name, current_price, product_category, status, order_no) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        respond(false, 'Database error: ' . $con->error);
    }
    $stmt->bind_param("sdsii", $product_name, $current_price, $product_category, $status, $order_no);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $p_id > 0 ? $p_id : $stmt->insert_id;
UserLog("Bill Item", $action, "Item ID: $recordId - $product_name");

respond(true, "Item {$action} successfully.");
