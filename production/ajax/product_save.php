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

$p_id             = isset($_POST['p_id']) ? intval($_POST['p_id']) : 0;
$product_name     = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$product_category = isset($_POST['product_category']) ? trim($_POST['product_category']) : '';
$current_price    = isset($_POST['current_price']) ? trim($_POST['current_price']) : '';
$batch_quantity   = isset($_POST['batch_quantity']) ? trim($_POST['batch_quantity']) : '';
$status           = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($product_name === '' || $product_category === '') {
    respond(false, 'Product name and category are required.');
}

if (!is_numeric($current_price) || floatval($current_price) < 0) {
    respond(false, 'Please enter a valid price.');
}

$batch_quantity = ($batch_quantity === '') ? null : $batch_quantity;
if (!is_numeric($batch_quantity) || intval($batch_quantity) < 0) {
    respond(false, 'Please enter a valid batch quantity.');
}

$current_price = floatval($current_price);
$batch_quantity = intval($batch_quantity);
$status = ($status === 0) ? 0 : 1; // normalize

if ($p_id > 0) {
    $stmt = $con->prepare("UPDATE production_product SET product_name = ?, product_category = ?, current_price = ?, batch_quantity = ?, status = ? WHERE p_id = ?");
    $stmt->bind_param("ssdiii", $product_name, $product_category, $current_price, $batch_quantity, $status, $p_id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO production_product (product_name, product_category, current_price, batch_quantity, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdii", $product_name, $product_category, $current_price, $batch_quantity, $status);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $p_id > 0 ? $p_id : $stmt->insert_id;
UserLog("Production Product", $action, "Product ID: $recordId - $product_name");

respond(true, "Product {$action} successfully.");
