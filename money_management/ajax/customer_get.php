<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "SELECT cus_id, customer_name, contact_number, status FROM manage_customers WHERE status = 1 ORDER BY customer_name ASC";
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
