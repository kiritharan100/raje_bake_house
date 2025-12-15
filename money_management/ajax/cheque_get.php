<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$show_future = isset($_GET['show_future']) ? intval($_GET['show_future']) : 1;

// Build WHERE: always apply date range when provided.
// If show_future is checked, also include any cheque_date greater than today.
$conditions = [];
$params = [];
$types = '';

if ($from_date) {
    $conditions[] = "c.cheque_date >= ?";
    $types .= "s";
    $params[] = $from_date;
}
if ($to_date) {
    $conditions[] = "c.cheque_date <= ?";
    $types .= "s";
    $params[] = $to_date;
}

$rangeClause = '';
if (!empty($conditions)) {
    $rangeClause = '(' . implode(' AND ', $conditions) . ')';
}

$finalConditions = [];
if ($rangeClause !== '') {
    $finalConditions[] = $rangeClause;
}

if ($show_future) {
    $finalConditions[] = "c.cheque_date > CURDATE()";
}

$where = '';
if (!empty($finalConditions)) {
    $where = 'WHERE (' . implode(' OR ', $finalConditions) . ')';
}

$sql = "
    SELECT 
        c.chq_id,
        c.cheque_no,
        c.contact_id,
        bc.contact_name,
        c.issue_date,
        c.cheque_date,
        c.amount,
        c.status
    FROM bank_cheque_payment c
    LEFT JOIN bank_contact bc ON bc.contact_id = c.contact_id
    $where
    ORDER BY c.cheque_date DESC
";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $con->error
    ]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();
$cheques = [];
while ($row = $result->fetch_assoc()) {
    $cheques[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $cheques
]);
