<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "SELECT id, material_name, mesurement, current_price, status 
        FROM production_material 
        WHERE status IN ('0','1') 
        ORDER BY material_name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$materials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $materials[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $materials
]);
