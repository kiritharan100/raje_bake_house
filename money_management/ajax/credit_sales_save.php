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

$bill_id     = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$bill_no     = isset($_POST['bill_no']) ? trim($_POST['bill_no']) : '';
$date        = isset($_POST['date']) ? trim($_POST['date']) : '';
$items_json  = isset($_POST['items']) ? $_POST['items'] : '[]';

if ($customer_id <= 0 || $date === '' || $bill_no === '') {
    respond(false, 'Customer, bill no, and date are required.');
}

$items = json_decode($items_json, true);
if (!is_array($items) || count($items) === 0) {
    respond(false, 'At least one item is required.');
}

// Calculate total
$total = 0;
$cleanItems = [];
foreach ($items as $item) {
    $p_id = intval($item['p_id'] ?? 0);
    $qty = floatval($item['quantity'] ?? 0);
    $price = floatval($item['price'] ?? 0);
    if ($p_id <= 0 || $qty <= 0) {
        respond(false, 'Each item must have a product and quantity.');
    }
    $value = $qty * $price;
    $total += $value;
    $cleanItems[] = [
        'p_id' => $p_id,
        'quantity' => $qty,
        'price' => $price,
        'value' => $value
    ];
}

mysqli_begin_transaction($con);

try {
    if ($bill_id > 0) {
        $stmt = $con->prepare("UPDATE bill_summary SET customer_id = ?, date = ?, bill_no = ?, amount = ? WHERE bill_id = ?");
        if (!$stmt) throw new Exception($con->error);
        $stmt->bind_param("issdi", $customer_id, $date, $bill_no, $total, $bill_id);
        if (!$stmt->execute()) throw new Exception($stmt->error);

        // Clear old details
        $del = $con->prepare("DELETE FROM bill_detail WHERE bill_id = ?");
        if (!$del) throw new Exception($con->error);
        $del->bind_param("i", $bill_id);
        if (!$del->execute()) throw new Exception($del->error);
        $action = "Updated";
    } else {
        $stmt = $con->prepare("INSERT INTO bill_summary (customer_id, date, bill_no, amount, status) VALUES (?, ?, ?, ?, 1)");
        if (!$stmt) throw new Exception($con->error);
        $stmt->bind_param("issd", $customer_id, $date, $bill_no, $total);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $bill_id = $stmt->insert_id;
        $action = "Created";
    }

    $detail = $con->prepare("INSERT INTO bill_detail (bill_id, p_id, quantity, price, value, status) VALUES (?, ?, ?, ?, ?, 1)");
    if (!$detail) throw new Exception($con->error);
    foreach ($cleanItems as $it) {
        $detail->bind_param("iiddi", $bill_id, $it['p_id'], $it['quantity'], $it['price'], $it['value']);
        if (!$detail->execute()) throw new Exception($detail->error);
    }

    mysqli_commit($con);
    UserLog("Credit Sales", $action, "Bill ID: $bill_id");
    respond(true, "Bill {$action} successfully.");
} catch (Exception $e) {
    mysqli_rollback($con);
    respond(false, 'Database error: ' . $e->getMessage());
}
