<?php
require('../auth.php');
require('../db.php');

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to   = isset($_GET['to']) ? $_GET['to'] : '';
$validDate = function($d) { return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d); };

if (!$validDate($from) || !$validDate($to)) {
    echo "<h4 style='padding:20px;'>Invalid date range.</h4>";
    exit;
}

$daysDiff = (strtotime($to) - strtotime($from)) / 86400;
if ($daysDiff > 35) {
    echo "<h4 style='padding:20px;'>Date range too large (max 35 days).</h4>";
    exit;
}

// Build date list
$dates = [];
$cursor = strtotime($from);
$end = strtotime($to);
while ($cursor <= $end) {
    $dates[] = date('Y-m-d', $cursor);
    $cursor = strtotime('+1 day', $cursor);
}

// Materials ordered by id
$materials = [];
$matRes = mysqli_query($con, "SELECT id, material_name, current_price FROM production_material ORDER BY id ASC");
while ($m = mysqli_fetch_assoc($matRes)) {
    $materials[$m['id']] = [
        'name' => $m['material_name'],
        'price' => floatval($m['current_price']),
        'daily' => array_fill_keys($dates, 0),
        'total' => 0,
        'value' => 0
    ];
}

// Fetch usage
$stmt = $con->prepare("SELECT date, material_id, material_price, quantity_used FROM production_daily_material_usage WHERE date BETWEEN ? AND ?");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $mid = $row['material_id'];
    if (!isset($materials[$mid])) continue;
    $qty = floatval($row['quantity_used']);
    $d = $row['date'];
    if (isset($materials[$mid]['daily'][$d])) {
        $materials[$mid]['daily'][$d] += $qty;
        $materials[$mid]['total'] += $qty;
    }
    // Use current master price for value
    $materials[$mid]['value'] = $materials[$mid]['total'] * $materials[$mid]['price'];
}

// Totals
$grandTotal = 0;
$grandValue = 0;
foreach ($materials as $mid => $m) {
    $grandTotal += $m['total'];
    $grandValue += $m['value'];
}

function fmt($n) { return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Material Supply</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: center; }
        th { background: #f5f5f5; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <h3>Material Supply</h3>
    <p>Date range: <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?></p>

    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th class="text-left">Material</th>
                <?php foreach ($dates as $d): ?>
                    <th><?php echo htmlspecialchars(date('d/m/Y', strtotime($d))); ?></th>
                <?php endforeach; ?>
                <th>Total</th>
                <th>Rate</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php $sn = 1; foreach ($materials as $mid => $m): ?>
                <?php if ($m['total'] == 0) continue; ?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($m['name']); ?></td>
                    <?php foreach ($dates as $d): ?>
                        <td><?php echo $m['daily'][$d] > 0 ? fmt($m['daily'][$d]) : ''; ?></td>
                    <?php endforeach; ?>
                    <td><?php echo fmt($m['total']); ?></td>
                    <td><?php echo fmt($m['price']); ?></td>
                    <td class="text-right"><?php echo fmt($m['value']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="2" class="text-left">Total</td>
                <?php foreach ($dates as $d): 
                    $colTotal = 0;
                    foreach ($materials as $m) { $colTotal += $m['daily'][$d]; }
                ?>
                    <td><?php echo $colTotal > 0 ? fmt($colTotal) : ''; ?></td>
                <?php endforeach; ?>
                <td><?php echo fmt($grandTotal); ?></td>
                <td></td>
                <td class="text-right"><?php echo fmt($grandValue); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
