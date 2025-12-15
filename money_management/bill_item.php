<?php include 'header.php'; ?>
<?php
// Manage billing items. Source: bill_items (p_id, product_name, current_price, product_category, status, order_no).
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Billing Items</h4>
                    <button class="btn btn-primary btn-sm" id="addItemBtn">
                        <i class="fa fa-plus"></i> Add Item
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="item-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Product Name</th>
                                        <th width="120">Category</th>
                                        <th width="120">Current Price</th>
                                        <th width="80">Order No</th>
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

<!-- Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="itemForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="p_id" id="itemId">
                    <div class="form-group row">
                        <label for="productName" class="col-sm-4 col-form-label text-right">Product Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="productName" name="product_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="productCategory" class="col-sm-4 col-form-label text-right">Category</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="productCategory" name="product_category" required>
                                <option value="">Select</option>
                                <option value="Bakery Items">Bakery Items</option>
                                <option value="Short dish">Short dish</option>
                                <option value="Shop Items">Shop Items</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="currentPrice" class="col-sm-4 col-form-label text-right">Current Price</label>
                        <div class="col-sm-8">
                            <input type="number" step="0.01" min="0" class="form-control" id="currentPrice" name="current_price" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="orderNo" class="col-sm-4 col-form-label text-right">Order No</label>
                        <div class="col-sm-8">
                            <input type="number" min="0" class="form-control" id="orderNo" name="order_no" value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="itemStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="itemStatus" name="status">
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
let itemTable = null;

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

function loadItems() {
    $.getJSON('ajax/bill_item_get_all.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load items.', 'error');
            return;
        }

        if (itemTable) {
            itemTable.destroy();
        }

        const tbody = $('#item-table tbody');
        tbody.empty();

        response.data.forEach(function(row, index) {
            const tr = $('<tr>');
            tr.attr('data-id', row.p_id);
            tr.attr('data-name', row.product_name);
            tr.attr('data-price', row.current_price);
            tr.attr('data-category', row.product_category);
            tr.attr('data-order', row.order_no);
            tr.attr('data-status', row.status);
            tr.append(`<td>${index + 1}</td>`);
            tr.append(`<td class="col-name">${row.product_name}</td>`);
            tr.append(`<td class="col-category">${row.product_category}</td>`);
            tr.append(`<td class="col-price text-right">${parseFloat(row.current_price).toFixed(2)}</td>`);
            tr.append(`<td class="col-order text-center">${row.order_no}</td>`);
            tr.append(`<td>${statusBadge(row.status)}</td>`);
            tr.append(`<td>${actionMenu(row.p_id, row.status)}</td>`);
            tbody.append(tr);
        });

        itemTable = $('#item-table').DataTable({
            order: [[0, 'asc']],
            pageLength: 100
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to fetch items.', 'error');
    });
}

function resetItemForm() {
    $('#itemId').val('');
    $('#itemForm')[0].reset();
    $('#itemStatus').val('1');
    $('#productCategory').val('');
    $('#orderNo').val('0');
    $('#itemForm').removeClass('was-validated');
    $('#itemModalLabel').text('Add Item');
}

$(document).ready(function() {
    loadItems();

    $('#addItemBtn').on('click', function() {
        resetItemForm();
        $('#itemModal').modal('show');
    });

    $(document).on('click', '.action-edit', function(e) {
        e.preventDefault();
        const tr = $(this).closest('tr');
        $('#itemId').val(tr.data('id'));
        $('#productName').val(tr.data('name'));
        $('#productCategory').val(tr.data('category'));
        $('#currentPrice').val(tr.data('price'));
        $('#orderNo').val(tr.data('order'));
        $('#itemStatus').val(String(tr.data('status')));
        $('#itemModalLabel').text('Edit Item');
        $('#itemModal').modal('show');
    });

    $(document).on('click', '.action-toggle', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 1 || status === '1' ? 'activate' : 'deactivate';

        Swal.fire({
            title: `Confirm ${actionText}`,
            text: `Are you sure you want to ${actionText} this item?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/bill_item_delete.php', { id: id, status: status }, function(response) {
                    if (response.success) {
                        Swal.fire('Updated', response.message || 'Status updated.', 'success');
                        loadItems();
                    } else {
                        Swal.fire('Error', response.message || 'Unable to update status.', 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Request failed. Please try again.', 'error');
                });
            }
        });
    });

    $('#itemForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            form.reportValidity();
            return;
        }

        const formData = $(form).serialize();

        $.post('ajax/bill_item_save.php', formData, function(response) {
            if (response.success) {
                $('#itemModal').modal('hide');
                Swal.fire('Success', response.message || 'Item saved.', 'success');
                loadItems();
            } else {
                Swal.fire('Error', response.message || 'Unable to save item.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });
});
</script>
