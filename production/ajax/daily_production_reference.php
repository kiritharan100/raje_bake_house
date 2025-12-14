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

// Products (include active + any referenced in daily production for edit scenarios)
$products = [];
$sql = "SELECT DISTINCT p.p_id, p.product_name, p.batch_quantity, p.current_price AS sales_price 
        FROM production_product p
        WHERE p.status IN ('1')
           OR p.p_id IN (SELECT DISTINCT product_id FROM production_daily_production)
        ORDER BY p.order_no ASC, p.product_name ASC";
$res = mysqli_query($con, $sql);
if (!$res) {
    respond(false, 'Database error: ' . mysqli_error($con));
}
while ($row = mysqli_fetch_assoc($res)) {
    $products[] = $row;
}

// Materials (include active/inactive and any referenced in daily usage for edit scenarios)
$materials = [];
$matsql = "SELECT DISTINCT m.id, m.material_name, m.mesurement, m.current_price 
           FROM production_material m
           WHERE m.status IN ('0','1')
              OR m.id IN (SELECT DISTINCT material_id FROM production_daily_material_usage)
           ORDER BY m.id ASC";
$matres = mysqli_query($con, $matsql);
if (!$matres) {
    respond(false, 'Database error: ' . mysqli_error($con));
}
while ($row = mysqli_fetch_assoc($matres)) {
    $materials[] = $row;
}

// Allocations
$allocations = [];
$allocsql = "SELECT product_id, material_id, unit FROM production_material_allocation WHERE unit > 0";
$allocres = mysqli_query($con, $allocsql);
if (!$allocres) {
    respond(false, 'Database error: ' . mysqli_error($con));
}
while ($row = mysqli_fetch_assoc($allocres)) {
    $allocations[] = $row;
}

respond(true, 'Loaded', [
    'products' => $products,
    'materials' => $materials,
    'allocations' => $allocations
]);
