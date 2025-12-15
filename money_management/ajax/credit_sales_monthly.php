<?php
require('../../db.php');
require('../../auth.php');

header('Content-Type: application/json');

// Collect all years from bills and payments
$years = [];
$yearSql = "
    SELECT DISTINCT YEAR(date) as y FROM bill_summary
    UNION
    SELECT DISTINCT YEAR(payment_date) as y FROM bill_payment
    ORDER BY y DESC
";
$yearRes = mysqli_query($con, $yearSql);
if ($yearRes) {
    while ($r = mysqli_fetch_assoc($yearRes)) {
        if (!empty($r['y'])) {
            $years[] = (int)$r['y'];
        }
    }
}

$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
if ($year === 0 && !empty($years)) {
    $year = $years[0]; // default latest year
}

function monthArray() {
    return array_fill(1, 12, 0.0);
}

$sales = monthArray();
$pay = monthArray();

if ($year > 0) {
    // Credit sales per month (only active)
    $sStmt = $con->prepare("SELECT MONTH(date) m, SUM(amount) total FROM bill_summary WHERE status = 1 AND YEAR(date) = ? GROUP BY MONTH(date)");
    if ($sStmt) {
        $sStmt->bind_param("i", $year);
        if ($sStmt->execute()) {
            $res = $sStmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $m = (int)$row['m'];
                $sales[$m] = (float)$row['total'];
            }
        }
    }

    // Payments per month (only active)
    $pStmt = $con->prepare("SELECT MONTH(payment_date) m, SUM(amount) total FROM bill_payment WHERE status = 1 AND YEAR(payment_date) = ? GROUP BY MONTH(payment_date)");
    if ($pStmt) {
        $pStmt->bind_param("i", $year);
        if ($pStmt->execute()) {
            $res = $pStmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $m = (int)$row['m'];
                $pay[$m] = (float)$row['total'];
            }
        }
    }
}

// Prepare series arrays in Jan..Dec order (0-based index)
$salesSeries = [];
$paySeries = [];
for ($i = 1; $i <= 12; $i++) {
    $salesSeries[] = $sales[$i];
    $paySeries[] = $pay[$i];
}

echo json_encode([
    'success' => true,
    'year' => $year,
    'years' => $years,
    'sales' => $salesSeries,
    'payments' => $paySeries
]);
