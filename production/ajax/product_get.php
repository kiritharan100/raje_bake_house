<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

$sql = "SELECT p.p_id, p.product_name, p.current_price, p.product_category, p.batch_quantity, p.status,
               (SELECT COUNT(*) FROM production_material_allocation pa WHERE pa.product_id = p.p_id AND pa.unit > 0) AS material_count,
               (SELECT IFNULL(SUM(pm.current_price * pa.unit),0) 
                  FROM production_material_allocation pa 
                  JOIN production_material pm ON pm.id = pa.material_id 
                 WHERE pa.product_id = p.p_id) AS material_cost
        FROM production_product p
        WHERE p.status IN ('0','1') 
        ORDER BY p.product_name ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($con)
    ]);
    exit;
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $batch_qty = floatval($row['batch_quantity']);
    $mat_cost = floatval($row['material_cost']);
    $unit_cost = ($batch_qty > 0) ? $mat_cost / $batch_qty : 0;

    $row['unit_cost'] = number_format($unit_cost, 2, '.', '');
    $products[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $products
]);
