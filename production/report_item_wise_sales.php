<?php
require('../auth.php');
require('../db.php');

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

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

// Build date columns
$dates = [];
$cursor = strtotime($from);
$end = strtotime($to);
while ($cursor <= $end) {
    $dates[] = date('Y-m-d', $cursor);
    $cursor = strtotime('+1 day', $cursor);
}

// Fetch products ordered by order_no/name
$products = [];
$prodRes = mysqli_query($con, "SELECT p_id, product_name, current_price FROM production_product ORDER BY order_no ASC, product_name ASC");
while ($p = mysqli_fetch_assoc($prodRes)) {
    $products[$p['p_id']] = [
        'name' => $p['product_name'],
        'price' => floatval($p['current_price']),
        'daily' => array_fill_keys($dates, 0),
        'total' => 0,
        'value' => 0
    ];
}

// Fetch production within range
$stmt = $con->prepare("SELECT date, product_id, sales_price, quantity, return_qty FROM production_daily_production WHERE date BETWEEN ? AND ?");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $pid = $row['product_id'];
    if (!isset($products[$pid])) continue;
    $qty = floatval($row['quantity']) - floatval($row['return_qty']);
    $d = $row['date'];
    if (isset($products[$pid]['daily'][$d])) {
        $products[$pid]['daily'][$d] += $qty;
        $products[$pid]['total'] += $qty;
    }
}

// Compute values
$grandTotal = 0;
$grandValue = 0;
foreach ($products as $pid => &$p) {
    $p['value'] = $p['total'] * $p['price'];
    $grandTotal += $p['total'];
    $grandValue += $p['value'];
}
unset($p);

function fmt($n) { return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Item Wise Sales</title>
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
    <h3>Item Wise Sales</h3>
    <p>Date range: <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?></p>

    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th class="text-left">Product</th>
                <?php foreach ($dates as $d): ?>
                    <th><?php echo htmlspecialchars(date('d/m/Y', strtotime($d))); ?></th>
                <?php endforeach; ?>
                <th>Total</th>
                <th>Sales Price</th>
                <th>Sales Value</th>
            </tr>
        </thead>
        <tbody>
            <?php $sn = 1; foreach ($products as $pid => $p): ?>
                <?php if ($p['total'] == 0) continue; ?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($p['name']); ?></td>
                    <?php foreach ($dates as $d): ?>
                        <td><?php echo $p['daily'][$d] > 0 ? fmt($p['daily'][$d]) : ''; ?></td>
                    <?php endforeach; ?>
                    <td><?php echo fmt($p['total']); ?></td>
                    <td><?php echo fmt($p['price']); ?></td>
                    <td class="text-right"><?php echo fmt($p['value']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="2" class="text-left">Total</td>
                <?php foreach ($dates as $d): 
                    $colTotal = 0;
                    foreach ($products as $p) { $colTotal += $p['daily'][$d]; }
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
