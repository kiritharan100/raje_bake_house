<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

function respond($success, $message, $data = null) {
    $resp = ['success' => $success, 'message' => $message];
    if ($data !== null) { $resp['data'] = $data; }
    echo json_encode($resp);
    exit;
}

$date = isset($_GET['date']) ? $_GET['date'] : '';
if (empty($date)) {
    respond(false, 'Date is required.');
}

$products = [];
$ps = $con->prepare("SELECT dp.product_id, dp.sales_price, dp.quantity, dp.return_qty, COALESCE(pp.batch_quantity, 0) AS batch_quantity
                     FROM production_daily_production dp
                     LEFT JOIN production_product pp ON pp.p_id = dp.product_id
                     WHERE dp.date = ?");
$ps->bind_param("s", $date);
if ($ps->execute()) {
    $res = $ps->get_result();
    while ($row = $res->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    respond(false, 'Database error: ' . $ps->error);
}

$materials = [];
$ms = $con->prepare("SELECT material_id, material_price, quantity_used FROM production_daily_material_usage WHERE date = ?");
$ms->bind_param("s", $date);
if ($ms->execute()) {
    $res = $ms->get_result();
    while ($row = $res->fetch_assoc()) {
        $materials[] = $row;
    }
} else {
    respond(false, 'Database error: ' . $ms->error);
}

// Send back also batch quantities for products (to recalc system usage correctly in case product archived)
$batchLookup = [];
$batchSql = "SELECT p_id, batch_quantity FROM production_product";
$bres = mysqli_query($con, $batchSql);
if ($bres) {
    while ($r = mysqli_fetch_assoc($bres)) {
        $batchLookup[$r['p_id']] = $r['batch_quantity'];
    }
}
foreach ($products as &$p) {
    if (!isset($p['batch_quantity']) && isset($batchLookup[$p['product_id']])) {
        $p['batch_quantity'] = $batchLookup[$p['product_id']];
    }
}
unset($p);

respond(true, 'Loaded', [
    'products' => $products,
    'materials' => $materials
]);
