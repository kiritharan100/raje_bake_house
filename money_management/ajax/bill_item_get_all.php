<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "SELECT p_id, product_name, current_price, product_category, status, order_no 
        FROM bill_items 
        ORDER BY order_no ASC, product_name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $rows
]);
