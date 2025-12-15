<?php
require('../db.php');
require('../auth.php');

$default_from = date('Y-m-d', strtotime('-30 days'));
$default_to = date('Y-m-d');
$from = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : $default_from;
$to = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : $default_to;

$conditions = [];
$params = [];
$types = '';

if ($from) {
    $conditions[] = "bp.payment_date >= ?";
    $params[] = $from;
    $types .= "s";
}
if ($to) {
    $conditions[] = "bp.payment_date <= ?";
    $params[] = $to;
    $types .= "s";
}

$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT 
        bp.payment_date,
        b.bill_no,
        bp.payment_mode,
        bp.amount,
        c.customer_name
    FROM bill_payment bp
    LEFT JOIN bill_summary b ON b.bill_id = bp.bill_id
    LEFT JOIN manage_customers c ON c.cus_id = b.customer_id
    $where
    AND bp.status = 1
    ORDER BY bp.payment_date DESC, bp.pay_id DESC
";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die("DB error: " . $con->error);
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
$total = 0;
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
    $total += (float)$row['amount'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Payment List</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <style>
    table {
        border: 1px solid #20232a;
    }

    th,
    td {
        border: 1px solid #20232a !important;
        padding: 4px 6px !important;
        line-height: 1.2;
    }

    th {
        background: #f7f7f7;
    }

    .text-right {
        text-align: right !important;
    }
    </style>
</head>

<body onload="window.print()">
    <div class="container-fluid mt-3">
        <h4>Payment List</h4>
        <div><strong>From:</strong> <?php echo htmlspecialchars($from); ?> | <strong>To:</strong>
            <?php echo htmlspecialchars($to); ?></div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width='100'>Date</th>
                        <th width='80'>Bill No</th>
                        <th>Customer</th>
                        <th width='120'>Mode</th>
                        <th width='100' class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['payment_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['bill_no']); ?></td>
                        <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['payment_mode']); ?></td>
                        <td class="text-right"><?php echo number_format($r['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="4">Total</td>
                        <td class="text-right"><?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>