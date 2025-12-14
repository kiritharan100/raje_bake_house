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

if ($id <= 0) {
    respond(false, 'Invalid material ID.');
}

$stmt = $con->prepare("UPDATE production_material SET status = 0 WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    respond(false, 'Database error: ' . $stmt->error);
}

UserLog("Production Material", "Inactivated", "Material ID: $id");

respond(true, 'Material marked as inactive.');
