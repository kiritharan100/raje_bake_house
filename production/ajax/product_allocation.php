<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

function respond($success, $message, $data = null)
{
    $resp = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $resp['data'] = $data;
    }
    echo json_encode($resp);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
    if ($product_id <= 0) {
        respond(false, 'Invalid product id.');
    }

    $stmt = $con->prepare("SELECT m.id, m.material_name, m.mesurement, IFNULL(pa.unit, '') AS unit
                           FROM production_material m
                           LEFT JOIN production_material_allocation pa ON pa.material_id = m.id AND pa.product_id = ?
                           WHERE m.status IN ('0','1')
                           ORDER BY m.material_name ASC");
    $stmt->bind_param("i", $product_id);

    if (!$stmt->execute()) {
        respond(false, 'Database error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    respond(true, 'Loaded', $rows);
}

if ($method === 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if ($product_id <= 0) {
        respond(false, 'Invalid product id.');
    }

    $allocations_json = isset($_POST['allocations']) ? $_POST['allocations'] : '[]';
    $allocations = json_decode($allocations_json, true);
    if (!is_array($allocations)) {
        respond(false, 'Invalid allocations payload.');
    }

    $con->begin_transaction();
    try {
        $del = $con->prepare("DELETE FROM production_material_allocation WHERE product_id = ?");
        $del->bind_param("i", $product_id);
        if (!$del->execute()) {
            throw new Exception($del->error);
        }

        $ins = $con->prepare("INSERT INTO production_material_allocation (product_id, material_id, unit) VALUES (?, ?, ?)");
        $count = 0;
        foreach ($allocations as $row) {
            $material_id = isset($row['material_id']) ? intval($row['material_id']) : 0;
            $unit = isset($row['unit']) ? floatval($row['unit']) : 0;
            if ($material_id > 0 && $unit > 0) {
                $ins->bind_param("iid", $product_id, $material_id, $unit);
                if (!$ins->execute()) {
                    throw new Exception($ins->error);
                }
                $count++;
            }
        }

        $con->commit();
        UserLog("Production Product", "Material Allocation", "Product ID: $product_id, Allocated materials: $count");
        respond(true, "Allocation saved ($count material(s)).");
    } catch (Exception $e) {
        $con->rollback();
        respond(false, 'Database error: ' . $e->getMessage());
    }
}

respond(false, 'Unsupported request.');
