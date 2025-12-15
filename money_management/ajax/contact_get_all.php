<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "SELECT contact_id, contact_name, contact_number, status 
        FROM bank_contact 
        ORDER BY contact_name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$contacts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $contacts[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $contacts
]);
