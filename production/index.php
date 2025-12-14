<?php
include 'header.php';

$from = date('Y-m-d', strtotime('-29 days'));
$to = date('Y-m-d');

// Material cost per day
$matSql = $con->prepare("SELECT date, SUM(material_price * quantity_used) AS mat_cost FROM production_daily_material_usage WHERE date BETWEEN ? AND ? GROUP BY date");
$matSql->bind_param("ss", $from, $to);
$matSql->execute();
$matRes = $matSql->get_result();
$materialMap = [];
while ($r = $matRes->fetch_assoc()) {
    $materialMap[$r['date']] = floatval($r['mat_cost']);
}

// Sales and qty per day (net qty)
$salesSql = $con->prepare("SELECT date, SUM((quantity - COALESCE(return_qty,0)) * sales_price) AS sales_value, SUM(quantity) AS qty_total FROM production_daily_production WHERE date BETWEEN ? AND ? GROUP BY date");
$salesSql->bind_param("ss", $from, $to);
$salesSql->execute();
$salesRes = $salesSql->get_result();
$salesMap = [];
$qtyMap = [];
while ($r = $salesRes->fetch_assoc()) {
    $salesMap[$r['date']] = floatval($r['sales_value']);
    $qtyMap[$r['date']] = floatval($r['qty_total']);
}

// Overhead rate lookup function
function getOverheadRate($con, $date) {
    $stmt = $con->prepare("SELECT over_head FROM production_overhead WHERE status = 1 AND effective_from <= ? ORDER BY effective_from DESC LIMIT 1");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    return $row ? floatval($row['over_head']) : 0;
}

$dates = [];
$chartSales = [];
$chartMaterial = [];
$chartOverhead = [];
$chartProfit = [];
$totalSales = $totalMaterial = $totalOverhead = $totalProfit = 0;

$cursor = strtotime($from);
$end = strtotime($to);
while ($cursor <= $end) {
    $d = date('Y-m-d', $cursor);
    $dates[] = $d;
    $sales = $salesMap[$d] ?? 0;
    $material = $materialMap[$d] ?? 0;
    $qtyTotal = $qtyMap[$d] ?? 0;
    $ohRate = getOverheadRate($con, $d);
    $overhead = $qtyTotal * $ohRate;
    $profit = $sales - $material - $overhead;

    $chartSales[] = $sales;
    $chartMaterial[] = $material;
    $chartOverhead[] = $overhead;
    $chartProfit[] = $profit;

    $totalSales += $sales;
    $totalMaterial += $material;
    $totalOverhead += $overhead;
    $totalProfit += $profit;

    $cursor = strtotime('+1 day', $cursor);
}

$profitPct = ($totalMaterial + $totalOverhead) > 0 ? ($totalProfit / ($totalMaterial + $totalOverhead)) * 100 : 0;

// Top 10 return items last 30 days
$retSql = $con->prepare("SELECT p.product_name, SUM(COALESCE(dp.return_qty,0)) AS ret_qty
                         FROM production_daily_production dp
                         LEFT JOIN production_product p ON p.p_id = dp.product_id
                         WHERE dp.date BETWEEN ? AND ?
                         GROUP BY dp.product_id, p.product_name
                         ORDER BY ret_qty DESC
                         LIMIT 10");
$retSql->bind_param("ss", $from, $to);
$retSql->execute();
$retRes = $retSql->get_result();
$retCategories = [];
$retData = [];
while ($r = $retRes->fetch_assoc()) {
    $retCategories[] = $r['product_name'] ?: 'Unknown';
    $retData[] = floatval($r['ret_qty']);
}
?>
<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Dashboard</h4>
                    <small>Last 30 days (<?php echo $from; ?> to <?php echo $to; ?>)</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h5>Cost / Profit Mix</h5></div>
                    <div class="card-block">
                        <div id="pie-cost"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Sales (Last 30 Days)</h5></div>
                    <div class="card-block">
                        <div id="line-sales"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h5>Top 10 Return Items (Qty)</h5></div>
                    <div class="card-block">
                        <div id="bar-returns"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Highcharts.chart('pie-cost', {
        chart: { type: 'pie' },
        title: { text: null },
        credits: { enabled: false },
        tooltip: { pointFormat: '{series.name}: <b>{point.y:.2f}</b>' },
        plotOptions: {
            pie: {
                dataLabels: { enabled: true, format: '{point.name}: {point.percentage:.1f}%' }
            }
        },
        series: [{
            name: 'Value',
            data: [
                { name: 'Material', y: <?php echo $totalMaterial; ?> },
                { name: 'Overhead', y: <?php echo $totalOverhead; ?> },
                { name: 'Profit', y: <?php echo $totalProfit; ?> }
            ]
        }]
    });

    Highcharts.chart('line-sales', {
        chart: { type: 'line' },
        title: { text: null },
        credits: { enabled: false },
        xAxis: { categories: <?php echo json_encode(array_map(function($d){ return date('d/m', strtotime($d)); }, $dates)); ?> },
        yAxis: { title: { text: 'Sales Value' } },
        series: [{
            name: 'Sales',
            data: <?php echo json_encode(array_map('floatval', $chartSales)); ?>
        }]
    });

    Highcharts.chart('bar-returns', {
        chart: { type: 'column' },
        title: { text: null },
        credits: { enabled: false },
        xAxis: { categories: <?php echo json_encode($retCategories); ?>, crosshair: true },
        yAxis: { min: 0, title: { text: 'Return Qty' } },
        series: [{
            name: 'Returns',
            data: <?php echo json_encode($retData); ?>
        }]
    });
});
</script>
