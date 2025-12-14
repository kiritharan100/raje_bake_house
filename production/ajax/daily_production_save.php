<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.');
}

$date = isset($_POST['date']) ? $_POST['date'] : '';
$is_edit = isset($_POST['is_edit']) ? intval($_POST['is_edit']) : 0;
if (empty($date)) {
    respond(false, 'Date is required.');
}

// Allow past dates; validation handled in duplicate check below

$products_json = isset($_POST['products']) ? $_POST['products'] : '[]';
$materials_json = isset($_POST['materials']) ? $_POST['materials'] : '[]';

$products = json_decode($products_json, true);
$materials = json_decode($materials_json, true);

if (!is_array($products)) {
    respond(false, 'Invalid products payload.');
}
if (!is_array($materials)) {
    respond(false, 'Invalid materials payload.');
}
if (count($products) === 0) {
    respond(false, 'Enter at least one product quantity.');
}

// Prevent creating duplicate dates
if (!$is_edit) {
    $chk = $con->prepare("
        SELECT 
            (SELECT COUNT(*) FROM production_daily_production WHERE date = ?) +
            (SELECT COUNT(*) FROM production_daily_material_usage WHERE date = ?) AS cnt
    ");
    $chk->bind_param("ss", $date, $date);
    if ($chk->execute()) {
        $r = $chk->get_result()->fetch_assoc();
        if (($r['cnt'] ?? 0) > 0) {
            respond(false, 'A record already exists for this date. Please edit that entry.');
        }
    } else {
        respond(false, 'Database error: ' . $chk->error);
    }
}

$con->begin_transaction();
try {
    // Delete existing records for the date (idempotent save)
    $delProd = $con->prepare("DELETE FROM production_daily_production WHERE date = ?");
    $delProd->bind_param("s", $date);
    if (!$delProd->execute()) {
        throw new Exception($delProd->error);
    }

    $delMat = $con->prepare("DELETE FROM production_daily_material_usage WHERE date = ?");
    $delMat->bind_param("s", $date);
    if (!$delMat->execute()) {
        throw new Exception($delMat->error);
    }

    // Insert products
$prodStmt = $con->prepare("INSERT INTO production_daily_production (date, product_id, sales_price, quantity, return_qty) VALUES (?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $pid = intval($p['product_id'] ?? 0);
        $price = floatval($p['sales_price'] ?? 0);
        $qty = floatval($p['quantity'] ?? 0);
        $ret = floatval($p['return_qty'] ?? 0);
        if ($pid > 0 && $qty > 0) {
            $prodStmt->bind_param("sidii", $date, $pid, $price, $qty, $ret);
            if (!$prodStmt->execute()) {
                throw new Exception($prodStmt->error);
            }
        }
    }

    // Insert materials
    $matStmt = $con->prepare("INSERT INTO production_daily_material_usage (date, material_id, material_price, quantity_used) VALUES (?, ?, ?, ?)");
    foreach ($materials as $m) {
        $mid = intval($m['material_id'] ?? 0);
        $price = floatval($m['material_price'] ?? 0);
        $qty = floatval($m['quantity_used'] ?? 0);
        if ($mid > 0 && $qty > 0) {
            $matStmt->bind_param("sidd", $date, $mid, $price, $qty);
            if (!$matStmt->execute()) {
                throw new Exception($matStmt->error);
            }
        }
    }

    $con->commit();
    UserLog("Production Daily", "Save", "Daily production saved for $date");
    respond(true, 'Saved successfully.');
} catch (Exception $e) {
    $con->rollback();
    respond(false, 'Database error: ' . $e->getMessage());
}
