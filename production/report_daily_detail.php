<?php
require('../auth.php');
require('../db.php');
if (!isset($_GET['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    echo "<h4 style='padding:20px;'>Invalid date.</h4>";
    exit;
}
$date = $_GET['date'];

// overhead rate for the date (latest active before/on date)
$ovStmt = $con->prepare("SELECT over_head FROM production_overhead WHERE status = 1 AND effective_from <= ? ORDER BY effective_from DESC LIMIT 1");
$ovStmt->bind_param("s", $date);
$ovStmt->execute();
$ovRes = $ovStmt->get_result();
$ovRow = $ovRes->fetch_assoc();
$overhead_rate = $ovRow['over_head'] ?? 0;

// fetch products for date
$prodSql = $con->prepare("SELECT dp.product_id, dp.sales_price, dp.quantity, COALESCE(dp.return_qty,0) as return_qty, 
                                 p.product_name, p.batch_quantity
                          FROM production_daily_production dp
                          LEFT JOIN production_product p ON p.p_id = dp.product_id
                          WHERE dp.date = ?
                          ORDER BY p.order_no ASC, p.product_name ASC");
$prodSql->bind_param("s", $date);
$prodSql->execute();
$prodRes = $prodSql->get_result();
$products = $prodRes->fetch_all(MYSQLI_ASSOC);

// materials ordered by id
$matRes = mysqli_query($con, "SELECT id, material_name, mesurement, current_price FROM production_material WHERE status IN ('0','1') ORDER BY id ASC");
$materials = [];
while ($m = mysqli_fetch_assoc($matRes)) {
    $materials[$m['id']] = $m;
}

// Actual usage from daily material usage table
$actualUsageTotals = [];
$actStmt = $con->prepare("SELECT material_id, SUM(quantity_used) AS qty_used FROM production_daily_material_usage WHERE date = ? GROUP BY material_id");
$actStmt->bind_param("s", $date);
if ($actStmt->execute()) {
    $actRes = $actStmt->get_result();
    while ($ar = $actRes->fetch_assoc()) {
        $actualUsageTotals[$ar['material_id']] = floatval($ar['qty_used']);
    }
}

// allocations map product_id => list
$allocRes = mysqli_query($con, "SELECT product_id, material_id, unit FROM production_material_allocation");
$allocMap = [];
while ($a = mysqli_fetch_assoc($allocRes)) {
    $allocMap[$a['product_id']][] = $a;
}

// compute usage and costs
$rows = [];
$materialTotals = [];
$materialCostTotals = [];
$overheadTotal = 0;
$totalSalesValue = 0;
$totalProfit = 0;
foreach ($products as $p) {
    $pid = $p['product_id'];
    $qty = floatval($p['quantity']);
    $ret = floatval($p['return_qty']);
    $batch = floatval($p['batch_quantity']) ?: 0;
    $sales_qty = $qty - $ret;
    $calc_qty = $qty; // system calculation per Daily Production uses production qty (not reduced by return)
    $usage = [];
    $matCost = 0;
    if ($batch > 0 && !empty($allocMap[$pid])) {
        foreach ($allocMap[$pid] as $a) {
            $mid = $a['material_id'];
            $perItem = floatval($a['unit']) / $batch;
            $totalUse = $calc_qty * $perItem; // system calculation based on production qty
            $usage[$mid] = $totalUse;
            $price = isset($materials[$mid]) ? floatval($materials[$mid]['current_price']) : 0;
            $cost = $totalUse * $price;
            $matCost += $cost;
            $materialTotals[$mid] = ($materialTotals[$mid] ?? 0) + $totalUse;
            $materialCostTotals[$mid] = ($materialCostTotals[$mid] ?? 0) + $cost;
        }
    }
    $sales_value = $sales_qty * floatval($p['sales_price']);
    $overhead_cost = $overhead_rate * $qty; // per unit production
    $overheadTotal += $overhead_cost;
    $profit = $sales_value - $matCost - $overhead_cost;
    $totalSalesValue += $sales_value;
    $totalProfit += $profit;
    $rows[] = [
        'product_name' => $p['product_name'] ?: ('Product #' . $pid),
        'sales_price' => $p['sales_price'],
        'qty' => $qty,
        'return_qty' => $ret,
        'sales_qty' => $sales_qty,
        'usage' => $usage,
        'mat_cost' => $matCost,
        'overhead_cost' => $overhead_cost,
        'sales_value' => $sales_value,
        'profit' => $profit,
    ];
}

function fmt($n) { return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Material Usage and Sales - <?php echo htmlspecialchars($date); ?></title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 4px;
    }

    th {
        background: #f5f5f5;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .btn-print {
        padding: 6px 10px;
        background: #666;
        color: #fff;
        text-decoration: none;
        border-radius: 3px;
    }

    .rotate {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
        width: 60px;
    }

    .prod-col {
        width: 180px;
    }

    @media print {
        .btn-print {
            display: none;
        }
    }
    </style>
</head>

<body>

    <div class="header">
        <h3>Daily Material Usage and Sales - <?php echo htmlspecialchars($date); ?></h3>
        <a class="btn-print" href="#" onclick="window.print();return false;">Print</a>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="prod-col">Product</th>
                <?php foreach ($materials as $m): ?>
                <th class="text-center rotate">
                    <?php echo htmlspecialchars($m['material_name']); ?><br><small>Rs.
                        <?php echo fmt($m['current_price']); ?></small>
                </th>
                <?php endforeach; ?>
                <th class="text-center rotate">Sale Price</th>
                <th class="text-center rotate">Production Qty</th>
                <th class="text-center rotate">Return Qty</th>
                <th class="text-center rotate">Sales Qty</th>
                <th class="text-center rotate">Material Cost</th>
                <th class="text-center rotate">Overhead</th>
                <th class="text-center rotate">Sales Value</th>
                <th class="text-center rotate">Profit / Loss</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['product_name']); ?></td>
                <?php foreach ($materials as $mid => $m): ?>
                <?php $val = $r['usage'][$mid] ?? 0; ?>
                <td class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></td>
                <?php endforeach; ?>
                <td class="text-right"><?php echo fmt($r['sales_price']); ?></td>
                <td class="text-right"><?php echo fmt($r['qty']); ?></td>
                <td class="text-right"><?php echo fmt($r['return_qty']); ?></td>
                <td class="text-right"><?php echo fmt($r['sales_qty']); ?></td>
                <td class="text-right"><?php echo fmt($r['mat_cost']); ?></td>
                <td class="text-right"><?php echo fmt($r['overhead_cost']); ?></td>
                <td class="text-right"><?php echo fmt($r['sales_value']); ?></td>
                <td class="text-right"><?php echo fmt($r['profit']); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th>Estimated Qty</th>
                <?php foreach ($materials as $mid => $m): ?>
                <?php $val = $materialTotals[$mid] ?? 0; ?>
                <th class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></th>
                <?php endforeach; ?>
                <th colspan="4"></th>
                <th class="text-right"><?php echo fmt(array_sum($materialCostTotals)); ?></th>
                <th class="text-right"><?php echo fmt($overheadTotal); ?></th>
                <th class="text-right"><?php echo fmt($totalSalesValue); ?></th>
                <th class="text-right"><?php echo fmt($totalProfit); ?></th>
            </tr>

            <tr>
                <th>Estimated Cost</th>
                <?php foreach ($materials as $mid => $m): $cost = $materialCostTotals[$mid] ?? 0; ?>
                <?php $val = $cost; ?>
                <td class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></td>
                <?php endforeach; ?>
                <td colspan="8"></td>
            </tr>
            <tr>
                <th>Actual Material</th>
                <?php foreach ($materials as $mid => $m): $actual = $actualUsageTotals[$mid] ?? 0; ?>
                <?php $val = $actual; ?>
                <td class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></td>
                <?php endforeach; ?>
                <td colspan="8"></td>
            </tr>
            <tr>
                <th>Difference Qty</th>
                <?php foreach ($materials as $mid => $m): 
                $calc = $materialTotals[$mid] ?? 0;
                $actual = $actualUsageTotals[$mid] ?? 0;
                $diff = $calc - $actual;
            ?>
                <?php $val = $diff; ?>
                <td class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></td>
                <?php endforeach; ?>
                <td colspan="8"></td>
            </tr>
            <tr>
                <th>Difference Value</th>
                <?php foreach ($materials as $mid => $m): 
                $calc = $materialTotals[$mid] ?? 0;
                $actual = $actualUsageTotals[$mid] ?? 0;
                $diff = $calc - $actual;
                $price = isset($materials[$mid]) ? floatval($materials[$mid]['current_price']) : 0;
                $diffVal = $diff * $price;
            ?>
                <?php $val = $diffVal; ?>
                <td class="text-right"><?php echo ($val == 0.0) ? '-' : fmt($val); ?></td>
                <?php endforeach; ?>
                <td colspan="8"></td>
            </tr>
        </tbody>
    </table>

</body>

</html>