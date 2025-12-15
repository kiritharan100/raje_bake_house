<?php include 'header.php'; ?>
<?php
// Manage customers. Source: manage_customers table (cus_id, customer_name, contact_number, status).
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Manage Customers</h4>
                    <button class="btn btn-primary btn-sm" id="addCustomerBtn">
                        <i class="fa fa-plus"></i> Add Customer
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="customer-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Customer Name</th>
                                        <th width="150">Contact Number</th>
                                        <th width="100">Status</th>
                                        <th width="100">Actions</th>
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

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="customerForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cus_id" id="cusId">
                    <div class="form-group row">
                        <label for="customerName" class="col-sm-4 col-form-label text-right">Customer Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="customerName" name="customer_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="customerNumber" class="col-sm-4 col-form-label text-right">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="customerNumber" name="contact_number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="customerStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="customerStatus" name="status">
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
let customerTable = null;

function statusBadge(status) {
    return (status === 1 || status === '1') ?
        '<span class="badge badge-success">Active</span>' :
        '<span class="badge badge-secondary">Inactive</span>';
}

function actionMenu(id, status) {
    const nextStatus = (status === 1 || status === '1') ? 0 : 1;
    const actionLabel = nextStatus === 0 ? 'Deactivate' : 'Activate';
    const actionIcon = nextStatus === 0 ? 'fa-ban text-danger' : 'fa-check text-success';
    return `
            <div class="dropdown">
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

function loadCustomers() {
        $.getJSON('ajax/customer_get_all.php', function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load customers.', 'error');
                return;
            }

        if (customerTable) {
            customerTable.destroy();
        }

        const tbody = $('#customer-table tbody');
        tbody.empty();

        response.data.forEach(function(row, index) {
            const tr = $('<tr>');
            tr.attr('data-id', row.cus_id);
            tr.attr('data-name', row.customer_name);
            tr.attr('data-number', row.contact_number);
            tr.attr('data-status', row.status);
            tr.append(`<td>${index + 1}</td>`);
            tr.append(`<td class="col-name">${row.customer_name}</td>`);
            tr.append(`<td class="col-number">${row.contact_number || ''}</td>`);
            tr.append(`<td>${statusBadge(row.status)}</td>`);
            tr.append(`<td>${actionMenu(row.cus_id, row.status)}</td>`);
            tbody.append(tr);
        });

        customerTable = $('#customer-table').DataTable({
            order: [[0, 'asc']],
            pageLength: 100
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to fetch customers.', 'error');
    });
}

function resetCustomerForm() {
    $('#cusId').val('');
    $('#customerForm')[0].reset();
    $('#customerStatus').val('1');
    $('#customerForm').removeClass('was-validated');
    $('#customerModalLabel').text('Add Customer');
}

$(document).ready(function() {
    loadCustomers();

    $('#addCustomerBtn').on('click', function() {
        resetCustomerForm();
        $('#customerModal').modal('show');
    });

    $(document).on('click', '.action-edit', function(e) {
        e.preventDefault();
        const tr = $(this).closest('tr');
        $('#cusId').val(tr.data('id'));
        $('#customerName').val(tr.data('name'));
        $('#customerNumber').val(tr.data('number'));
        $('#customerStatus').val(String(tr.data('status')));
        $('#customerModalLabel').text('Edit Customer');
        $('#customerModal').modal('show');
    });

    $(document).on('click', '.action-toggle', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 1 || status === '1' ? 'activate' : 'deactivate';

        Swal.fire({
            title: `Confirm ${actionText}`,
            text: `Are you sure you want to ${actionText} this customer?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/customer_delete.php', { id: id, status: status }, function(response) {
                    if (response.success) {
                        Swal.fire('Updated', response.message || 'Status updated.', 'success');
                        loadCustomers();
                    } else {
                        Swal.fire('Error', response.message || 'Unable to update status.', 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Request failed. Please try again.', 'error');
                });
            }
        });
    });

    $('#customerForm').on('submit', function(e) {
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
                $('#customerModal').modal('hide');
                Swal.fire('Success', response.message || 'Customer saved.', 'success');
                loadCustomers();
            } else {
                Swal.fire('Error', response.message || 'Unable to save customer.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });
});
</script>
