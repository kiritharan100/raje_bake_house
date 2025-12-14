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

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-29 days'));
$to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

// basic date validation (YYYY-MM-DD)
foreach (['from' => $from, 'to' => $to] as $label => $val) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
        respond(false, "Invalid $label date.");
    }
}

$dates = [];
$dateSql = "(SELECT DISTINCT date FROM production_daily_production WHERE date BETWEEN ? AND ?) 
            UNION 
            (SELECT DISTINCT date FROM production_daily_material_usage WHERE date BETWEEN ? AND ?)";
$dstmt = $con->prepare($dateSql);
$dstmt->bind_param("ssss", $from, $to, $from, $to);
if (!$dstmt->execute()) {
    respond(false, 'Database error: ' . $dstmt->error);
}
$dres = $dstmt->get_result();
while ($row = $dres->fetch_assoc()) {
    if (!empty($row['date'])) {
        $dates[] = $row['date'];
    }
}

$data = [];
foreach ($dates as $d) {
    // Material value
    $matSql = $con->prepare("SELECT SUM(material_price * quantity_used) AS total FROM production_daily_material_usage WHERE date = ?");
    $matSql->bind_param("s", $d);
    $matSql->execute();
    $matRes = $matSql->get_result();
    $matRow = $matRes->fetch_assoc();
    $material_value = $matRow['total'] ?? 0;

    // Sales value
    $prodSql = $con->prepare("SELECT 
                                    SUM(sales_price * (quantity - COALESCE(return_qty,0))) AS net_total,
                                    SUM(quantity) AS gross_qty
                               FROM production_daily_production 
                               WHERE date = ?");
    $prodSql->bind_param("s", $d);
    $prodSql->execute();
    $prodRes = $prodSql->get_result();
    $prodRow = $prodRes->fetch_assoc();
    $sales_value = $prodRow['net_total'] ?? 0; // daily sale based on (qty - return_qty)
    $total_qty = $prodRow['gross_qty'] ?? 0;   // use gross production for overhead

    // Overhead rate (latest effective on/before date)
    $ovSql = $con->prepare("SELECT over_head FROM production_overhead WHERE status = 1 AND effective_from <= ? ORDER BY effective_from DESC LIMIT 1");
    $ovSql->bind_param("s", $d);
    $ovSql->execute();
    $ovRes = $ovSql->get_result();
    $ovRow = $ovRes->fetch_assoc();
    $over_head_rate = $ovRow['over_head'] ?? 0;

    $overhead_value = $total_qty * $over_head_rate;

    $total_cost = $material_value + $overhead_value;
    $profit = $sales_value - $total_cost;
    $profit_pct = ($total_cost > 0) ? ($profit / $total_cost) * 100 : 0;

    $data[] = [
        'date' => $d,
        'material_value' => number_format((float)$material_value, 2, '.', ''),
        'sales_value' => number_format((float)$sales_value, 2, '.', ''),
        'overhead_value' => number_format((float)$overhead_value, 2, '.', ''),
        'total_cost' => number_format((float)$total_cost, 2, '.', ''),
        'profit' => number_format((float)$profit, 2, '.', ''),
        'profit_pct' => number_format((float)$profit_pct, 2, '.', '')
    ];
}

usort($data, function($a, $b) {
    return strcmp($b['date'], $a['date']);
});

respond(true, 'Loaded', $data);
