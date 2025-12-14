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

$id             = isset($_POST['id']) ? intval($_POST['id']) : 0;
$material_name  = isset($_POST['material_name']) ? trim($_POST['material_name']) : '';
$mesurement     = isset($_POST['mesurement']) ? trim($_POST['mesurement']) : '';
$current_price  = isset($_POST['current_price']) ? trim($_POST['current_price']) : '';
$status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($material_name === '' || $mesurement === '') {
    respond(false, 'Material name and measurement are required.');
}

if (!is_numeric($current_price) || floatval($current_price) < 0) {
    respond(false, 'Please enter a valid price.');
}

$current_price = floatval($current_price);
$status = ($status === 0) ? 0 : 1; // normalize

if ($id > 0) {
    $stmt = $con->prepare("UPDATE production_material SET material_name = ?, mesurement = ?, current_price = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $material_name, $mesurement, $current_price, $status, $id);
    $action = "Updated";
} else {
    $stmt = $con->prepare("INSERT INTO production_material (material_name, mesurement, current_price, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $material_name, $mesurement, $current_price, $status);
    $action = "Created";
}

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

$recordId = $id > 0 ? $id : $stmt->insert_id;
UserLog("Production Material", $action, "Material ID: $recordId - $material_name");

respond(true, "Material {$action} successfully.");
