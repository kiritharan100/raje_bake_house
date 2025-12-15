<?php
require('../db.php');
require('../auth.php');

// Only outstanding invoices for a customer
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
if ($customer_id <= 0) {
    echo "Invalid customer.";
    exit;
}

$cust_sql = $con->prepare("SELECT customer_name FROM manage_customers WHERE cus_id = ?");
$cust_sql->bind_param("i", $customer_id);
$cust_sql->execute();
$cust_res = $cust_sql->get_result();
$cust_row = $cust_res->fetch_assoc();
$customer_name = $cust_row ? $cust_row['customer_name'] : 'Unknown';

$sql = "
    SELECT 
        b.bill_id,
        b.bill_no,
        b.date,
        b.amount,
        COALESCE(p.paid_amount,0) AS paid_amount,
        (b.amount - COALESCE(p.paid_amount,0)) AS balance
    FROM bill_summary b
    LEFT JOIN (
        SELECT bill_id, SUM(amount) AS paid_amount
        FROM bill_payment
        WHERE status = 1
        GROUP BY bill_id
    ) p ON p.bill_id = b.bill_id
    WHERE b.customer_id = ?
      AND b.status = 1
      AND (b.amount - COALESCE(p.paid_amount,0)) > 0
    ORDER BY b.date DESC, b.bill_id DESC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $total += $row['balance'];
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Statement</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <style>
    table {
        border: 1px solid #20232a;
    }

    table th,
    table td {
        border: 1px solid #20232a !important;
        padding: 3px 6px !important;
        line-height: 1.2;
    }

    table th {
        background: #f7f7f7;
    }

    .text-right {
        text-align: right !important;
    }
    </style>

</head>

<body onload="window.print()">
    <div class="container mt-4">
        <div align='center'>
            <h4><?php echo htmlspecialchars($_SESSION['company']); ?></h4>
            <h5>Customer Statement</h5>
        </div>
        <div><strong>Customer:</strong> <?php echo htmlspecialchars($customer_name); ?></div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Bill Number</th>
                        <th class="text-right">Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['date']); ?></td>
                        <td><?php echo htmlspecialchars($r['bill_no']); ?></td>
                        <td class="text-right"><?php echo number_format($r['balance'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="2">Total</td>
                        <td class="text-right"><?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>