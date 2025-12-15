<?php include 'header.php'; ?>
<?php
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-t');
$show_future = isset($_GET['show_future']) ? (int)$_GET['show_future'] : 1;
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Manage Cheque Payment</h4>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <form class="form-inline" method="GET">
                            <div class="form-group mr-2">
                                <label class="mr-1">From</label>
                                <input type="date" class="form-control" name="from_date"
                                    value="<?php echo $from_date; ?>">
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-1">To</label>
                                <input type="date" class="form-control" name="to_date" value="<?php echo $to_date; ?>">
                            </div>

                            <button type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i></button>
                            <a href="manage_cheque_payment.php" class="btn btn-secondary mr-2">Reset</a>

                            <button type="button" class="btn btn-primary btn-sm" id="addChequeBtn"> <i
                                    class="fa fa-plus"></i> Add Issued Cheque
                            </button>


                            <button type="button" class="btn btn-info mr-2" id="chequeSummaryBtn">Cheque Payable
                                Summary</button>
                            <button type="button" class="btn btn-warning" id="colorCodeBtn">Color Code</button>
                            <button type="button" class="btn btn-primary ml-2" id="addPayeeBtn"><i
                                    class="fa fa-user-plus"></i> Add Payee</button>
                        </form>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="cheque-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>

                                        <th width="120">Cheque Date</th>
                                        <th width="120">Issue Date</th>
                                        <th>Payee</th>
                                        <th width="140">Cheque No</th>
                                        <th width="120">Amount</th>
                                        <th width="100">Status</th>
                                        <th width="130">Actions</th>
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

<!-- Cheque Modal -->
<div class="modal fade" id="chequeModal" tabindex="-1" role="dialog" aria-labelledby="chequeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="chequeForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="chequeModalLabel">Add Issued Cheque</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="chq_id" id="chqId">

                    <div class="form-group row">
                        <label for="contactId" class="col-sm-4 col-form-label text-right">Payee</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="contactId" name="contact_id" required>
                                <option value="">Select payee</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="chequeDate" class="col-sm-4 col-form-label text-right">Cheque Date</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="chequeDate" name="cheque_date" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="issueDate" class="col-sm-4 col-form-label text-right">Issue Date</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="issueDate" name="issue_date" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="chequeNo" class="col-sm-4 col-form-label text-right">Cheque No</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="chequeNo" name="cheque_no" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="amount" class="col-sm-4 col-form-label text-right">Amount</label>
                        <div class="col-sm-8">
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="status" name="status">
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

<!-- Cheque Date Modal -->
<div class="modal fade" id="chequeDateModal" tabindex="-1" role="dialog" aria-labelledby="chequeDateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="chequeDateForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="chequeDateModalLabel">Change Cheque Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="dateChqId" name="chq_id">
                    <div class="form-group">
                        <label for="newChequeDate">New Cheque Date</label>
                        <input type="date" class="form-control" id="newChequeDate" name="cheque_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cheque Summary Modal -->
<div class="modal fade" id="chequeSummaryModal" tabindex="-1" role="dialog" aria-labelledby="chequeSummaryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chequeSummaryModalLabel">Cheque Payable Summary (Future Cheques)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="summary-table">
                        <thead>
                            <tr>
                                <th>Payee</th>
                                <th width="90">Cheques</th>
                                <th width="100">within 7 days</th>
                                <th width="100">Total Payable</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td id="summary-total-count" class="text-right">0</td>
                                <td id="summary-total-seven" class="text-right">0.00</td>
                                <td id="summary-total-payable" class="text-right">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Color Code Modal -->
<div class="modal fade" id="colorCodeModal" tabindex="-1" role="dialog" aria-labelledby="colorCodeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colorCodeModalLabel">Cheque Date Color Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th width="180">Color</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background-color:#C2F2C7;"></td>
                            <td>Date passed</td>
                        </tr>
                        <tr>
                            <td style="background-color:#58E867;"></td>
                            <td>Today</td>
                        </tr>
                        <tr>
                            <td style="background-color:#F06090;"></td>
                            <td>Tomorrow</td>
                        </tr>
                        <tr>
                            <td style="background-color:#F29BB8;"></td>
                            <td>Day after tomorrow</td>
                        </tr>
                        <tr>
                            <td style="background-color:#F0CED9;"></td>
                            <td>Next 7 days after day after tomorrow</td>
                        </tr>
                        <tr>
                            <td style="background-color:#F5F0F1;"></td>
                            <td>Other future dates</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Payee Modal -->
<div class="modal fade" id="payeeModal" tabindex="-1" role="dialog" aria-labelledby="payeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="payeeForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="payeeModalLabel">Add Payee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="payeeName" class="col-sm-4 col-form-label text-right">Payee Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="payeeName" name="contact_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="payeeNumber" class="col-sm-4 col-form-label text-right">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="payeeNumber" name="contact_number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="payeeStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="payeeStatus" name="status">
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

<?php include 'footer.php'; ?>

<script>
const filterFrom = '<?php echo $from_date; ?>';
const filterTo = '<?php echo $to_date; ?>';
const showFuture = '<?php echo $show_future; ?>';
let chequeTable = null;
let contactsCache = [];

function statusBadge(status) {
    return (status === 1 || status === '1') ?
        '<span class="badge badge-success">Active</span>' :
        '<span class="badge badge-secondary">Inactive</span>';
}

function formatAmount(val) {
    const num = parseFloat(val || 0);
    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function actionMenu(id, status, chequeDate) {
    const nextStatus = (status === 1 || status === '1') ? 0 : 1;
    const actionLabel = nextStatus === 0 ? 'Deactivate' : 'Activate';
    const actionIcon = nextStatus === 0 ? 'fa-ban text-danger' : 'fa-check text-success';
    return `
            <div class="btn-group">
                <button class="btn btn-sm btn-info action-date" type="button" data-id="${id}" data-date="${chequeDate}" title="Change date">
                    <i class="fa fa-calendar"></i>
                </button>
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item action-edit" href="#" data-id="${id}"><i class="fa fa-edit text-primary"></i> Edit</a>
                    <a class="dropdown-item action-toggle" href="#" data-id="${id}" data-status="${nextStatus}"><i class="fa ${actionIcon}"></i> ${actionLabel}</a>
                </div>
            </div>
        `;
}

function rowColor(chequeDateStr) {
    const chequeDate = new Date(chequeDateStr);
    const today = new Date();
    const startToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const startCheque = new Date(chequeDate.getFullYear(), chequeDate.getMonth(), chequeDate.getDate());
    const diffDays = Math.round((startCheque - startToday) / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return '#C2F2C7'; // date passed
    if (diffDays === 0) return '#58E867'; // today
    if (diffDays === 1) return '#F06090'; // tomorrow
    if (diffDays === 2) return '#F29BB8'; // day after tomorrow
    if (diffDays >= 3 && diffDays <= 9) return '#F0CED9'; // next 7 days after day after tomorrow
    return '#F5F0F1'; // other future
}

function loadContacts() {
    $.getJSON('ajax/contact_get.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load contacts.', 'error');
            return;
        }
        contactsCache = response.data || [];
        const select = $('#contactId');
        select.empty();
        select.append('<option value="">Select payee</option>');
        contactsCache.forEach(function(c) {
            select.append(`<option value="${c.contact_id}">${c.contact_name}</option>`);
        });
        if (!select.hasClass('select2-hidden-accessible')) {
            select.select2({
                dropdownParent: $('#chequeModal'),
                width: '100%'
            });
        } else {
            select.trigger('change.select2');
        }
    });
}

function resetPayeeForm() {
    $('#payeeForm')[0].reset();
    $('#payeeStatus').val('1');
    $('#payeeForm').removeClass('was-validated');
}

function loadCheques() {
    $.getJSON('ajax/cheque_get.php', {
        from_date: filterFrom,
        to_date: filterTo,
        show_future: showFuture
    }, function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load cheques.', 'error');
            return;
        }

        if (chequeTable) {
            chequeTable.destroy();
        }

        const tbody = $('#cheque-table tbody');
        tbody.empty();

        response.data.forEach(function(row, index) {
            const tr = $('<tr>');
            tr.css('background-color', rowColor(row.cheque_date));
            tr.attr('data-id', row.chq_id);
            tr.attr('data-cheque_no', row.cheque_no);
            tr.attr('data-contact_id', row.contact_id);
            tr.attr('data-issue_date', row.issue_date);
            tr.attr('data-cheque_date', row.cheque_date);
            tr.attr('data-amount', row.amount);
            tr.attr('data-status', row.status);
            tr.append(`<td>${index + 1}</td>`);
            tr.append(`<td class="col-contact">${row.contact_name || ''}</td>`);
            tr.append(`<td class="col-chequedate">${row.cheque_date}</td>`);
            tr.append(`<td class="col-issue">${row.issue_date}</td>`);
            tr.append(`<td class="col-cheque">${row.cheque_no}</td>`);
            tr.append(`<td class="col-amount text-right">${formatAmount(row.amount)}</td>`);
            tr.append(`<td>${statusBadge(row.status)}</td>`);
            tr.append(`<td>${actionMenu(row.chq_id, row.status, row.cheque_date)}</td>`);
            tbody.append(tr);
        });

        chequeTable = $('#cheque-table').DataTable({
            order: [
                [2, 'desc']
            ],
            pageLength: 100
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to fetch cheques.', 'error');
    });
}

function resetForm() {
    $('#chqId').val('');
    $('#chequeForm')[0].reset();
    $('#status').val('1');
    $('#chequeForm').removeClass('was-validated');
    $('#chequeModalLabel').text('Add Issued Cheque');
    const today = new Date().toISOString().split('T')[0];
    $('#issueDate').val(today);
    $('#chequeDate').val(today);
}

$(document).ready(function() {
    loadContacts();
    loadCheques();

    $('#addChequeBtn').on('click', function() {
        resetForm();
        $('#chequeModal').modal('show');
    });

    $(document).on('click', '.action-edit', function(e) {
        e.preventDefault();
        const tr = $(this).closest('tr');
        $('#chqId').val(tr.data('id'));
        $('#chequeNo').val(tr.data('cheque_no'));
        $('#contactId').val(tr.data('contact_id'));
        $('#issueDate').val(tr.data('issue_date'));
        $('#chequeDate').val(tr.data('cheque_date'));
        $('#amount').val(tr.data('amount'));
        $('#status').val(String(tr.data('status')));
        $('#chequeModalLabel').text('Edit Cheque');
        $('#chequeModal').modal('show');
    });

    $(document).on('click', '.action-date', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const dateVal = $(this).data('date');
        $('#dateChqId').val(id);
        $('#newChequeDate').val(dateVal);
        $('#chequeDateModal').modal('show');
    });

    $(document).on('click', '.action-toggle', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 1 || status === '1' ? 'activate' : 'deactivate';

        Swal.fire({
            title: `Confirm ${actionText}`,
            text: `Are you sure you want to ${actionText} this cheque?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/cheque_delete.php', {
                    id: id,
                    status: status
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Updated', response.message || 'Status updated.',
                            'success');
                        loadCheques();
                    } else {
                        Swal.fire('Error', response.message ||
                            'Unable to update status.', 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Request failed. Please try again.', 'error');
                });
            }
        });
    });

    $('#chequeForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }

        const formData = $(form).serialize();

        $.post('ajax/cheque_save.php', formData, function(response) {
            if (response.success) {
                $('#chequeModal').modal('hide');
                Swal.fire('Success', response.message || 'Cheque saved.', 'success');
                loadCheques();
            } else {
                Swal.fire('Error', response.message || 'Unable to save cheque.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });

    $('#chequeDateForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }
        const formData = $(form).serialize();
        $.post('ajax/cheque_date_update.php', formData, function(response) {
            if (response.success) {
                $('#chequeDateModal').modal('hide');
                Swal.fire('Success', response.message || 'Cheque date updated.', 'success');
                loadCheques();
            } else {
                Swal.fire('Error', response.message || 'Unable to update cheque date.',
                    'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });

    function loadChequeSummary() {
        $.getJSON('ajax/cheque_summary.php', function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load summary.', 'error');
                return;
            }
            const tbody = $('#summary-table tbody');
            tbody.empty();
            response.data.forEach(function(row) {
                const tr = $('<tr>');
                tr.append(`<td>${row.contact_name}</td>`);
                tr.append(`<td class="text-right">${row.cheque_count}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.payable_7_days)}</td>`);
                tr.append(`<td class="text-right">${formatAmount(row.total_payable)}</td>`);
                tbody.append(tr);
            });
            $('#summary-total-count').text(response.total.count);
            $('#summary-total-seven').text(formatAmount(response.total.seven_days));
            $('#summary-total-payable').text(formatAmount(response.total.total_payable));
        }).fail(function() {
            Swal.fire('Error', 'Failed to load summary.', 'error');
        });
    }

    $('#chequeSummaryBtn').on('click', function() {
        loadChequeSummary();
        $('#chequeSummaryModal').modal('show');
    });

    $('#colorCodeBtn').on('click', function() {
        $('#colorCodeModal').modal('show');
    });

    $('#addPayeeBtn').on('click', function() {
        resetPayeeForm();
        $('#payeeModal').modal('show');
    });

    $('#payeeForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }
        const formData = $(form).serialize();
        $.post('ajax/contact_save.php', formData, function(response) {
            if (response.success) {
                $('#payeeModal').modal('hide');
                Swal.fire('Success', response.message || 'Payee added.', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', response.message || 'Unable to save payee.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });
});
</script>