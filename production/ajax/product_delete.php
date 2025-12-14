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

$p_id = isset($_POST['p_id']) ? intval($_POST['p_id']) : 0;

if ($p_id <= 0) {
    respond(false, 'Invalid product ID.');
}

$stmt = $con->prepare("UPDATE production_product SET status = 0 WHERE p_id = ?");
$stmt->bind_param("i", $p_id);

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

UserLog("Production Product", "Inactivated", "Product ID: $p_id");

respond(true, 'Product marked as inactive.');
