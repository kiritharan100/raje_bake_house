<?php include 'header.php'; ?>
<?php
// Manage credit sales: bill_summary (bill_id, customer_id, date, bill_no, amount, status) + bill_detail.
$from_date = date('Y-m-d', strtotime('-30 days'));
$to_date = date('Y-m-d');
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Credit Sales</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <form class="form-inline" id="billFilterForm">
                                <div class="form-group mr-2">
                                    <label class="mr-1">From</label>
                                    <input type="date" class="form-control" id="filterFrom"
                                        value="<?php echo $from_date; ?>">
                                </div>
                                <div class="form-group mr-2">
                                    <label class="mr-1">To</label>
                                    <input type="date" class="form-control" id="filterTo"
                                        value="<?php echo $to_date; ?>">
                                </div>
                                <button type="submit" class="btn btn-success btn-sm mr-2"><i
                                        class="fa fa-search"></i></button>
                                <button type="button" class="btn btn-secondary btn-sm mr-2"
                                    id="billFilterReset">Reset</button>
                                <button class="btn btn-primary btn-sm" id="addBillBtn">
                                    <i class="fa fa-plus"></i> Add Credit Sale
                                </button>
                                <button class="btn btn-primary btn-sm ml-2" id="addCustomerBtn">
                                    <i class="fa fa-user-plus"></i> Add Customer
                                </button>
                                <button class="btn btn-info btn-sm ml-2" id="creditAnalysisBtn">
                                    <i class="fa fa-chart-pie"></i> Credit Analysis
                                </button>
                                <button class="btn btn-secondary btn-sm ml-2" id="paymentListBtn">
                                    <i class="fa fa-print"></i> Payment List
                                </button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table id="bill-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="20">#</th>
                                        <th width="80">Date</th>
                                        <th width="60">Bill No</th>
                                        <th>Customer</th>
                                        <th width="100">Amount</th>
                                        <th width="100">Paid</th>
                                        <th width="100">Balance</th>
                                        <th width="60">Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bill Modal -->
<div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-labelledby="billModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="billForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="billModalLabel">Add Credit Sale</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bill_id" id="billId">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="billCustomer">Customer</label>
                            <select class="form-control" id="billCustomer" name="customer_id" required></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="billNo">Bill No</label>
                            <input type="text" class="form-control" id="billNo" name="bill_no" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="billDate">Date</label>
                            <input type="date" class="form-control" id="billDate" name="date" required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="bill-items-table">
                            <thead>
                                <tr>
                                    <th style="width:32%">Item</th>
                                    <th style="width:12%">Qty</th>
                                    <th style="width:18%">Price</th>
                                    <th style="width:18%">Value</th>
                                    <th style="width:10%">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="addRowBtn"><i class="fa fa-plus"></i> Add
                        Item</button>

                    <div class="text-right mt-3">
                        <h5>Total: <span id="billTotal">0.00</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline Add Customer Modal -->
<div class="modal fade" id="inlineCustomerModal" tabindex="-1" role="dialog" aria-labelledby="inlineCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="inlineCustomerForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="inlineCustomerLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="inlineCustomerName" class="col-sm-4 col-form-label text-right">Customer Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inlineCustomerName" name="customer_name"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inlineCustomerNumber" class="col-sm-4 col-form-label text-right">Contact
                            Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inlineCustomerNumber" name="contact_number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inlineCustomerStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="inlineCustomerStatus" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="paymentForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div><strong>Customer:</strong> <span id="payCustomer"></span></div>
                        <div><strong>Bill No:</strong> <span id="payBillNo"></span></div>
                        <div><strong>Bill Amount:</strong> <span id="payBillAmount"></span></div>
                        <div><strong>Payable:</strong> <span id="payBalance"></span></div>
                    </div>
                    <input type="hidden" id="payBillId">
                    <div class="form-group">
                        <label for="payDate">Payment Date</label>
                        <input type="date" class="form-control" id="payDate" name="payment_date" required>
                    </div>
                    <div class="form-group">
                        <label for="payMode">Payment Mode</label>
                        <select class="form-control" id="payMode" name="payment_mode" required>
                            <option value="">Select</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Bank Deposit">Bank Deposit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payAmount">Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="payAmount" name="amount"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Payment Modal -->
<div class="modal fade" id="managePaymentModal" tabindex="-1" role="dialog" aria-labelledby="managePaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="managePaymentModalLabel">Manage Payments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div><strong>Customer:</strong> <span id="mpCustomer"></span></div>
                    <div><strong>Bill No:</strong> <span id="mpBillNo"></span></div>
                    <div><strong>Bill Amount:</strong> <span id="mpBillAmount"></span></div>
                    <div><strong>Paid:</strong> <span id="mpPaidAmount"></span></div>
                    <div><strong>Balance:</strong> <span id="mpBalance"></span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="mpTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mode</th>
                                <th class="text-right">Amount</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Credit Analysis Modal -->
<div class="modal fade" id="creditAnalysisModal" tabindex="-1" role="dialog" aria-labelledby="creditAnalysisLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditAnalysisLabel">Credit Analysis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-2">
                    <div></div>
                    <button class="btn btn-secondary btn-sm" id="creditAnalysisPrint"><i class="fa fa-print"></i> Print</button>
                </div>
                <div class="table-responsive" id="creditAnalysisTableWrap">
                    <table class="table table-bordered table-striped" id="creditAnalysisTable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Customer</th>
                                <th class="text-right" width="120">Total Outstanding</th>
                                <th class="text-right" width="110">0-30 Days</th>
                                <th class="text-right" width="110">31-90 Days</th>
                                <th class="text-right" width="110">91-365 Days</th>
                                <th class="text-right" width="110">> 365 Days</th>
                                <th width="140">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="2">Total</td>
                                <td class="text-right" id="caTotalOutstanding">0.00</td>
                                <td class="text-right" id="caTotal30">0.00</td>
                                <td class="text-right" id="caTotal90">0.00</td>
                                <td class="text-right" id="caTotal365">0.00</td>
                                <td class="text-right" id="caTotalOver">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
let billTable = null;
let customers = [];
let items = [];
let isEditing = false;
let filterFrom = '<?php echo $from_date; ?>';
let filterTo = '<?php echo $to_date; ?>';
let currentPayBalance = 0;
let currentManageBillId = null;

function statusBadge(status, paid, balance) {
    if (status === 0 || status === '0') {
        return '<span class="badge badge-secondary">Inactive</span>';
    }
    const bal = parseFloat(balance) || 0;
    const paidAmt = parseFloat(paid) || 0;
    if (bal <= 0) {
        return '<span class="badge badge-success">Paid</span>';
    }
    if (paidAmt > 0) {
        return '<span class="badge badge-warning">Part Paid</span>';
    }
    return '<span class="badge badge-primary">Active</span>';
}

function actionMenu(id, status, balance, paid) {
    const nextStatus = (status === 1 || status === '1') ? 0 : 1;
    const actionLabel = nextStatus === 0 ? 'Deactivate' : 'Activate';
    const actionIcon = nextStatus === 0 ? 'fa-ban text-danger' : 'fa-check text-success';
    const showPay = (status === 1 || status === '1') && (parseFloat(balance) || 0) > 0;
    const hasPayment = (parseFloat(paid) || 0) > 0;
    return `
        <div class="btn-group">
            ${showPay ? `<button class="btn btn-sm btn-success action-pay" type="button" data-id="${id}"><i class="fa fa-money-bill"></i> Pay</button>` : ''}
            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                ${hasPayment ? '' : `<a class="dropdown-item action-edit" href="#" data-id="${id}"><i class="fa fa-edit text-primary"></i> Edit</a>`}
                ${hasPayment ? '' : `<a class="dropdown-item action-toggle" href="#" data-id="${id}" data-status="${nextStatus}"><i class="fa ${actionIcon}"></i> ${actionLabel}</a>`}
                ${hasPayment ? `<a class="dropdown-item action-manage-pay" href="#" data-id="${id}"><i class="fa fa-list text-info"></i> Manage Payment</a>` : ''}
            </div>
        </div>
    `;
}

function formatAmount(val) {
    const num = parseFloat(val || 0);
    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function loadCustomers() {
    return $.getJSON('ajax/customer_get.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load customers.', 'error');
            return;
        }
        customers = (response.data || []).filter(c => c.status === '1' || c.status === 1);
        const sel = $('#billCustomer');
        sel.empty().append('<option value="">Select Customer</option>');
        customers.forEach(c => sel.append(`<option value="${c.cus_id}">${c.customer_name}</option>`));
        sel.select2({
            dropdownParent: $('#billModal'),
            width: '100%'
        });
    });
}

function loadItems() {
    return $.getJSON('ajax/bill_item_get.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load items.', 'error');
            return;
        }
        items = (response.data || []).filter(it => it.status === '1' || it.status === 1);
    });
}

function addRow(rowData = {}) {
    const rowId = Date.now() + Math.floor(Math.random() * 1000);
    const tbody = $('#bill-items-table tbody');
    const tr = $(`
        <tr data-row="${rowId}">
            <td>
                <select class="form-control item-select" name="item[${rowId}][p_id]"></select>
            </td>
            <td><input type="number" step="0.01" min="0" class="form-control qty-input" name="item[${rowId}][quantity]" value="${rowData.quantity || ''}"></td>
            <td><input type="number" step="0.01" min="0" class="form-control price-input" name="item[${rowId}][price]" value="${rowData.price || ''}"></td>
            <td><input type="number" step="0.01" min="0" class="form-control value-input" name="item[${rowId}][value]" value="${rowData.value || ''}" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
        </tr>
    `);
    tbody.append(tr);

    const select = tr.find('.item-select');
    select.append('<option value="">Select Item</option>');
    items.forEach(it => select.append(
        `<option value="${it.p_id}" data-price="${it.current_price}">${it.product_name}</option>`));
    select.val(rowData.p_id || '').select2({
        dropdownParent: $('#billModal'),
        width: '100%'
    });
    recalcRow(tr);
    recalcTotal();
}

function recalcRow(tr) {
    const qty = parseFloat(tr.find('.qty-input').val()) || 0;
    const price = parseFloat(tr.find('.price-input').val()) || 0;
    const val = qty * price;
    tr.find('.value-input').val(val.toFixed(2));
}

function recalcTotal() {
    let total = 0;
    $('#bill-items-table tbody tr').each(function() {
        const val = parseFloat($(this).find('.value-input').val()) || 0;
        total += val;
    });
    $('#billTotal').text(formatAmount(total));
}

function resetBillForm() {
    $('#billId').val('');
    $('#billForm')[0].reset();
    $('#billForm').removeClass('was-validated');
    $('#billModalLabel').text('Add Credit Sale');
    $('#billNo').val('');
    $('#bill-items-table tbody').empty();
    addRow();
    const today = new Date().toISOString().split('T')[0];
    $('#billDate').val(today);
    isEditing = false;
}

function loadBills() {
    $.getJSON('ajax/credit_sales_get.php', {
        from_date: filterFrom,
        to_date: filterTo
    }, function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load credit sales.', 'error');
            return;
        }
        if (billTable) billTable.destroy();
        const tbody = $('#bill-table tbody');
        tbody.empty();
        response.data.forEach((row, idx) => {
            const tr = $('<tr>');
            tr.attr('data-id', row.bill_id);
            tr.attr('data-date', row.date);
            tr.attr('data-customer', row.customer_id);
            tr.attr('data-billno', row.bill_no);
            tr.attr('data-amount', row.amount);
            tr.attr('data-paid', row.paid_amount);
            tr.attr('data-balance', row.balance_amount);
            tr.attr('data-status', row.status);
            tr.append(`<td>${idx + 1}</td>`);
            tr.append(`<td>${row.date}</td>`);
            tr.append(`<td>${row.bill_no || ''}</td>`);
            tr.append(`<td>${row.customer_name || ''}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.amount)}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.paid_amount)}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.balance_amount)}</td>`);
            tr.append(`<td>${statusBadge(row.status, row.paid_amount, row.balance_amount)}</td>`);
            tr.append(
                `<td>${actionMenu(row.bill_id, row.status, row.balance_amount, row.paid_amount)}</td>`
            );
            tbody.append(tr);
        });
        billTable = $('#bill-table').DataTable({
            order: [
                [1, 'desc'],
                [2, 'desc']
            ],
            pageLength: 100
        });
    }).fail(() => Swal.fire('Error', 'Failed to fetch credit sales.', 'error'));
}

function loadBillDetails(billId) {
    return $.getJSON('ajax/credit_sales_detail.php', {
        bill_id: billId
    });
}

$(document).ready(function() {
    $.when(loadCustomers(), loadItems()).then(() => {
        loadBills();
    });

    $('#billFilterForm').on('submit', function(e) {
        e.preventDefault();
        filterFrom = $('#filterFrom').val();
        filterTo = $('#filterTo').val();
        loadBills();
    });

    $('#billFilterReset').on('click', function() {
        filterFrom = '<?php echo $from_date; ?>';
        filterTo = '<?php echo $to_date; ?>';
        $('#filterFrom').val(filterFrom);
        $('#filterTo').val(filterTo);
        loadBills();
    });

    $('#addBillBtn').on('click', function() {
        resetBillForm();
        $('#billModal').modal('show');
    });

    $('#addRowBtn').on('click', function() {
        addRow();
    });

    function resetInlineCustomerForm() {
        $('#inlineCustomerForm')[0].reset();
        $('#inlineCustomerStatus').val('1');
        $('#inlineCustomerForm').removeClass('was-validated');
    }

    $('#addCustomerBtn').on('click', function() {
        resetInlineCustomerForm();
        $('#inlineCustomerModal').modal('show');
    });

    $('#inlineCustomerForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }
        const formData = $(form).serialize();
        $.post('ajax/customer_save.php', formData, function(response) {
            if (response.success) {
                $('#inlineCustomerModal').modal('hide');
                Swal.fire('Success', response.message || 'Customer saved.', 'success');
                loadCustomers();
            } else {
                Swal.fire('Error', response.message || 'Unable to save customer.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#bill-items-table tbody tr').length === 0) {
            addRow();
        }
        recalcTotal();
    });

    $(document).on('change', '.item-select', function() {
        const price = $(this).find(':selected').data('price');
        const tr = $(this).closest('tr');
        if (price !== undefined) {
            tr.find('.price-input').val(price);
        }
        recalcRow(tr);
        recalcTotal();
        tr.find('.qty-input').focus();
    });

    $(document).on('input', '.qty-input, .price-input', function() {
        const tr = $(this).closest('tr');
        recalcRow(tr);
        recalcTotal();
        if (!isEditing) {
            const hasBlank = $('#bill-items-table tbody tr').toArray().some(r => {
                const sel = $(r).find('.item-select').val();
                return !sel;
            });
            if (!hasBlank) {
                addRow();
            }
        }
    });

    $(document).on('click', '.action-pay', function(e) {
        e.preventDefault();
        const tr = $(this).closest('tr');
        const billId = tr.data('id');
        const customer = tr.find('td').eq(3).text();
        const billNo = tr.find('td').eq(2).text();
        const amount = parseFloat(tr.data('amount')) || 0;
        const balance = parseFloat(tr.data('balance')) || 0;
        currentPayBalance = balance;
        $('#payBillId').val(billId);
        $('#payCustomer').text(customer);
        $('#payBillNo').text(billNo);
        $('#payBillAmount').text(formatAmount(amount));
        $('#payBalance').text(formatAmount(balance));
        const today = new Date().toISOString().split('T')[0];
        $('#payDate').val(today);
        $('#payMode').val('');
        $('#payAmount').val(balance.toFixed(2));
        $('#paymentModal').modal('show');
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }
        const payAmount = parseFloat($('#payAmount').val()) || 0;
        if (payAmount <= 0) {
            Swal.fire('Error', 'Payment amount must be greater than zero.', 'error');
            return;
        }
        if (payAmount - currentPayBalance > 0.0001) {
            Swal.fire('Error', 'Payment exceeds outstanding balance.', 'error');
            return;
        }
        const payload = {
            bill_id: $('#payBillId').val(),
            payment_mode: $('#payMode').val(),
            amount: payAmount,
            payment_date: $('#payDate').val()
        };
        $.post('ajax/credit_sales_payment.php', payload, function(response) {
            if (response.success) {
                $('#paymentModal').modal('hide');
                Swal.fire('Success', response.message || 'Payment recorded.', 'success');
                loadBills();
            } else {
                Swal.fire('Error', response.message || 'Unable to record payment.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });

    $('#creditAnalysisBtn').on('click', function() {
        loadCreditAnalysis();
    });

    $('#creditAnalysisPrint').on('click', function() {
        const contents = document.getElementById('creditAnalysisTableWrap').innerHTML;
        const win = window.open('', '', 'width=800,height=600');
        win.document.write('<html><head><title>Credit Analysis</title>');
        win.document.write('<link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">');
        win.document.write('</head><body>');
        win.document.write(contents);
        win.document.write('</body></html>');
        win.document.close();
        win.focus();
        win.print();
        win.close();
    });

    $('#paymentListBtn').on('click', function() {
        const from = $('#filterFrom').val() || '';
        const to = $('#filterTo').val() || '';
        const url = `print_payment_list.php?from_date=${encodeURIComponent(from)}&to_date=${encodeURIComponent(to)}`;
        window.open(url, '_blank');
    });

    function loadCreditAnalysis() {
        $.getJSON('ajax/credit_sales_analysis.php', function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load analysis.', 'error');
                return;
            }
            const tbody = $('#creditAnalysisTable tbody');
            tbody.empty();
            response.data.forEach(function(row, idx) {
                const tr = $('<tr>');
                tr.append(`<td>${idx + 1}</td>`);
                tr.append(`<td>${row.customer_name || ''}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.total)}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.d30)}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.d90)}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.d365)}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.dOver)}</td>`);
                tr.append(`<td><button class="btn btn-sm btn-outline-primary credit-print" data-cust="${row.customer_id}"><i class="fa fa-print"></i> Print Statement</button></td>`);
                tbody.append(tr);
            });
            $('#caTotalOutstanding').text(formatAmount(response.total.total));
            $('#caTotal30').text(formatAmount(response.total.d30));
            $('#caTotal90').text(formatAmount(response.total.d90));
            $('#caTotal365').text(formatAmount(response.total.d365));
            $('#caTotalOver').text(formatAmount(response.total.dOver));
            $('#creditAnalysisModal').modal('show');
        }).fail(function() {
            Swal.fire('Error', 'Failed to load analysis.', 'error');
        });
    }

    $(document).on('click', '.action-manage-pay', function(e) {
        e.preventDefault();
        const tr = $(this).closest('tr');
        currentManageBillId = tr.data('id');
        $('#mpCustomer').text(tr.find('td').eq(3).text());
        $('#mpBillNo').text(tr.find('td').eq(2).text());
        $('#mpBillAmount').text(formatAmount(tr.data('amount')));
        $('#mpPaidAmount').text(formatAmount(tr.data('paid')));
        $('#mpBalance').text(formatAmount(tr.data('balance')));
        loadPaymentsList(currentManageBillId);
        $('#managePaymentModal').modal('show');
    });

    function loadPaymentsList(billId) {
        $.getJSON('ajax/credit_sales_payment_list.php', {
            bill_id: billId
        }, function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load payments.', 'error');
                return;
            }
            const tbody = $('#mpTable tbody');
            tbody.empty();
            response.data.forEach(function(row) {
                const tr = $('<tr>');
                tr.append(`<td>${row.payment_date}</td>`);
                tr.append(`<td>${row.payment_mode}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.amount)}</td>`);
                tr.append(
                    `<td class="text-center"><button class="btn btn-sm btn-danger payment-delete" data-id="${row.pay_id}"><i class="fa fa-trash"></i></button></td>`
                );
                tbody.append(tr);
            });
        }).fail(function() {
            Swal.fire('Error', 'Failed to load payments.', 'error');
        });
    }

    $(document).on('click', '.payment-delete', function() {
        const payId = $(this).data('id');
        Swal.fire({
            title: 'Delete payment?',
            text: 'This will remove the payment from the bill.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/credit_sales_payment_delete.php', {
                    pay_id: payId
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Deleted', response.message || 'Payment deleted.',
                            'success');
                        if (currentManageBillId) {
                            loadPaymentsList(currentManageBillId);
                            loadBills();
                        }
                    } else {
                        Swal.fire('Error', response.message ||
                            'Unable to delete payment.', 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Request failed. Please try again.', 'error');
                });
            }
        });
    });

    $(document).on('click', '.action-edit', function(e) {
        e.preventDefault();
        const billId = $(this).data('id');
        const tr = $(this).closest('tr');
        $('#billId').val(billId);
        $('#billCustomer').val(tr.data('customer')).trigger('change');
        $('#billDate').val(tr.data('date'));
        $('#billNo').val(tr.data('billno'));
        $('#billModalLabel').text('Edit Credit Sale');
        $('#bill-items-table tbody').empty();
        isEditing = true;
        loadBillDetails(billId).done(function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load details.', 'error');
                return;
            }
            const rows = response.data || [];
            if (rows.length === 0) {
                addRow();
            } else {
                rows.forEach(r => addRow({
                    p_id: r.p_id,
                    quantity: r.quantity,
                    price: r.price,
                    value: r.value
                }));
            }
            recalcTotal();
            $('#billModal').modal('show');
        }).fail(() => Swal.fire('Error', 'Failed to load details.', 'error'));
    });

    $(document).on('click', '.action-toggle', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 1 || status === '1' ? 'activate' : 'deactivate';
        Swal.fire({
            title: `Confirm ${actionText}`,
            text: `Are you sure you want to ${actionText} this bill?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/credit_sales_delete.php', {
                    id: id,
                    status: status
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Updated', response.message || 'Status updated.',
                            'success');
                        loadBills();
                    } else {
                        Swal.fire('Error', response.message ||
                            'Unable to update status.', 'error');
                    }
                }, 'json').fail(() => Swal.fire('Error',
                    'Request failed. Please try again.', 'error'));
            }
        });
    });

    $('#billForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }
        const itemsPayload = [];
        let errorMsg = '';
        $('#bill-items-table tbody tr').each(function() {
            const p_id = $(this).find('.item-select').val();
            const qty = parseFloat($(this).find('.qty-input').val()) || 0;
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            const val = qty * price;

            if (qty > 0 && !p_id) {
                errorMsg = 'Please select an item for the entered quantity.';
                return false;
            }
            if (p_id && qty <= 0) {
                errorMsg = 'Please enter quantity for the selected item.';
                return false;
            }
            if (p_id && qty > 0) {
                itemsPayload.push({
                    p_id,
                    quantity: qty,
                    price: price,
                    value: val.toFixed(2)
                });
            }
            return true;
        });
        if (errorMsg) {
            Swal.fire('Error', errorMsg, 'error');
            return;
        }
        if (itemsPayload.length === 0) {
            Swal.fire('Error', 'Please add at least one item with quantity.', 'error');
            return;
        }

        const payload = {
            bill_id: $('#billId').val(),
            customer_id: $('#billCustomer').val(),
            bill_no: $('#billNo').val(),
            date: $('#billDate').val(),
            items: JSON.stringify(itemsPayload)
        };

        $.post('ajax/credit_sales_save.php', payload, function(response) {
            if (response.success) {
                $('#billModal').modal('hide');
                Swal.fire('Success', response.message || 'Saved.', 'success');
                loadBills();
            } else {
                Swal.fire('Error', response.message || 'Unable to save.', 'error');
            }
        }, 'json').fail(() => Swal.fire('Error', 'Request failed. Please try again.', 'error'));
    });

    $(document).on('click', '.credit-print', function() {
        const custId = $(this).data('cust');
        if (custId) {
            window.open(`print_customer_statement.php?customer_id=${custId}`, '_blank');
        }
    });
});
</script>
