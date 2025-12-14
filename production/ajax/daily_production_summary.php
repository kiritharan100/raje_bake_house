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

$dates = [];
$dateSql = "(SELECT DISTINCT date FROM production_daily_production) 
            UNION 
            (SELECT DISTINCT date FROM production_daily_material_usage)";
$dres = mysqli_query($con, $dateSql);
if (!$dres) {
    respond(false, 'Database error: ' . mysqli_error($con));
}
while ($row = mysqli_fetch_assoc($dres)) {
    if (!empty($row['date'])) {
        $dates[] = $row['date'];
    }
}

$data = [];
foreach ($dates as $d) {
    $matSql = $con->prepare("SELECT SUM(material_price * quantity_used) AS total FROM production_daily_material_usage WHERE date = ?");
    $matSql->bind_param("s", $d);
    $matSql->execute();
    $matRes = $matSql->get_result();
    $matRow = $matRes->fetch_assoc();
    $material_value = $matRow['total'] ?? 0;

    $prodSql = $con->prepare("SELECT SUM(sales_price * quantity) AS total FROM production_daily_production WHERE date = ?");
    $prodSql->bind_param("s", $d);
    $prodSql->execute();
    $prodRes = $prodSql->get_result();
    $prodRow = $prodRes->fetch_assoc();
    $sales_value = $prodRow['total'] ?? 0;

    $data[] = [
        'date' => $d,
        'material_value' => number_format((float)$material_value, 2, '.', ''),
        'sales_value' => number_format((float)$sales_value, 2, '.', '')
    ];
}

usort($data, function($a, $b) {
    return strcmp($b['date'], $a['date']);
});

respond(true, 'Loaded', $data);
