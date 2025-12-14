<?php include 'header.php'; ?>
<?php
// Product management page. Data source: SELECT p_id, product_name, current_price, product_category, status FROM production_product WHERE 1
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header">
                    <h4>Manage Products</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Products</h5>
                        <button class="btn btn-primary btn-sm" id="addProductBtn">
                            <i class="fa fa-plus"></i> Add Product
                        </button>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="product-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Product Name</th>
                                        <th width="150" class="text-center">Category</th>
                                        <th width="200" class="text-center">Material</th>
                                        <th width="130" class="text-center">Batch Quantity</th>
                                        <th width="120">Price</th>
                                        <th width="100" class="text-center">Status</th>
                                        <th width="80" class="text-center">Actions</th>
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="productForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="p_id" id="productId">
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
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="productPrice" class="col-sm-4 col-form-label text-right">Current Price</label>
                        <div class="col-sm-8">
                            <input type="number" step="0.01" min="0" class="form-control" id="productPrice"
                                name="current_price" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="batchQuantity" class="col-sm-4 col-form-label text-right">Batch Quantity</label>
                        <div class="col-sm-8">
                            <input type="number" min="0" step="1" class="form-control" id="batchQuantity"
                                name="batch_quantity" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="productStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="productStatus" name="status">
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

<!-- Material Allocation Modal -->
<div class="modal fade" id="materialAllocModal" tabindex="-1" role="dialog" aria-labelledby="materialAllocLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form id="allocationForm" novalidate>
                <div class="modal-header flex-column align-items-start">
                    <div class="d-flex w-100 align-items-center">
                        <h5 class="modal-title" id="materialAllocLabel">Material Allocation</h5>
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="mt-2">
                        <strong>Product:</strong> <span id="allocProductName"></span> &nbsp; | &nbsp;
                        <strong>Batch Qty:</strong> <span id="allocBatchQty"></span>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="allocProductId" name="product_id">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Material Name</th>
                                    <th width="120">Measurement</th>
                                    <th width="150">Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="materialAllocBody">
                                <tr>
                                    <td colspan="3" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Batch quantity = units per batch; material quantity = units of material
                        used per batch.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Save Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let productTable = null;

function badge(status) {
    return status === '1' || status === 1 ?
        '<span class="badge badge-success">Active</span>' :
        '<span class="badge badge-secondary">Inactive</span>';
}

function renderActions(id) {
    return `
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item action-edit" href="#" data-id="${id}"><i class="fa fa-edit text-primary"></i> Edit</a>
                    <a class="dropdown-item action-alloc" href="#" data-id="${id}"><i class="fa fa-sitemap text-success"></i> Material Allocation</a>
                    <a class="dropdown-item action-inactive" href="#" data-id="${id}"><i class="fa fa-ban text-danger"></i> Inactive</a>
                </div>
            </div>
        `;
}

function loadProducts() {
    $.getJSON('ajax/product_get.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load products.', 'error');
            return;
        }

        if (productTable) {
            productTable.destroy();
        }

        const tbody = $('#product-table tbody');
        tbody.empty();

        response.data.forEach(function(row, index) {
            const unitCost = (row.unit_cost !== undefined && row.unit_cost !== null && row.unit_cost !==
                    '') ?
                parseFloat(row.unit_cost).toFixed(2) :
                (parseFloat(row.batch_quantity) > 0 ? (parseFloat(row.material_cost || 0) / parseFloat(
                    row.batch_quantity)).toFixed(2) : '0.00');

            const tr = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${row.product_name}</td>
                        <td class="text-center">${row.product_category}</td>
                        <td class="text-center">(${row.material_count} items @ Rs. ${unitCost})</td>
                        <td class="text-center">${row.batch_quantity}</td>
                        <td align="right">${parseFloat(row.current_price).toFixed(2)}</td>
                        <td class="text-center">${badge(row.status)}</td>
                        <td class="text-center">${renderActions(row.p_id)}</td>
                    </tr>
                `;
            tbody.append(tr);
        });

        productTable = $('#product-table').DataTable({
            order: [
                [0, 'asc']
            ],
            pageLength: 100
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to fetch products.', 'error');
    });
}

function resetForm() {
    $('#productId').val('');
    $('#productForm')[0].reset();
    $('#productStatus').val('1');
    $('#productForm').removeClass('was-validated');
    $('#productModalLabel').text('Add Product');
}

function loadAllocation(productId, productName, batchQty) {
    $('#allocProductId').val(productId);
    $('#allocProductName').text(productName);
    $('#allocBatchQty').text(batchQty);
    $('#materialAllocBody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
    $('#materialAllocModal').modal('show');

    $.getJSON('ajax/product_allocation.php', {
        product_id: productId
    }, function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load materials.', 'error');
            $('#materialAllocModal').modal('hide');
            return;
        }

        const tbody = $('#materialAllocBody');
        tbody.empty();

        if (!response.data || response.data.length === 0) {
            tbody.append('<tr><td colspan="3" class="text-center">No materials found.</td></tr>');
            return;
        }

        response.data.forEach(function(mat) {
            const qty = mat.unit !== null && mat.unit !== '' ? mat.unit : '';
            tbody.append(`
                <tr>
                    <td>${mat.material_name}</td>
                    <td>${mat.mesurement}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm alloc-qty" data-material="${mat.id}" min="0" step="0.01" value="${qty}">
                    </td>
                </tr>
            `);
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to load materials.', 'error');
        $('#materialAllocModal').modal('hide');
    });
}

$(document).ready(function() {
    loadProducts();

    $('#addProductBtn').on('click', function() {
        resetForm();
        $('#productModal').modal('show');
    });

    $(document).on('click', '.action-edit', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const rowData = $(this).closest('tr').children('td');

        $('#productId').val(id);
        $('#productName').val(rowData.eq(1).text());
        $('#productCategory').val(rowData.eq(2).text());
        $('#batchQuantity').val(rowData.eq(4).text());
        $('#productPrice').val(rowData.eq(5).text());
        const isActive = rowData.eq(6).text().trim() === 'Active' ? '1' : '0';
        $('#productStatus').val(isActive);

        $('#productModalLabel').text('Edit Product');
        $('#productModal').modal('show');
    });

    $(document).on('click', '.action-inactive', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
            title: 'Mark as Inactive?',
            text: 'This will deactivate the product.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, inactivate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax/product_delete.php', {
                    p_id: id
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Updated', response.message ||
                            'Product marked inactive.', 'success');
                        loadProducts();
                    } else {
                        Swal.fire('Error', response.message ||
                            'Unable to update product.', 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Request failed. Please try again.', 'error');
                });
            }
        });
    });

    $(document).on('click', '.action-alloc', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const rowData = $(this).closest('tr').children('td');
        const productName = rowData.eq(1).text();
        const batchQty = rowData.eq(4).text();
        loadAllocation(id, productName, batchQty);
    });

    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        if (!form.checkValidity()) {
            e.stopPropagation();
            $(form).addClass('was-validated');
            return;
        }

        const formData = $(form).serialize();

        $.post('ajax/product_save.php', formData, function(response) {
            if (response.success) {
                $('#productModal').modal('hide');
                Swal.fire('Success', response.message || 'Product saved.', 'success');
                loadProducts();
            } else {
                Swal.fire('Error', response.message || 'Unable to save product.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });

    $('#allocationForm').on('submit', function(e) {
        e.preventDefault();
        const productId = $('#allocProductId').val();
        const allocations = [];

        $('.alloc-qty').each(function() {
            const qty = parseFloat($(this).val());
            if (!isNaN(qty) && qty > 0) {
                allocations.push({
                    material_id: $(this).data('material'),
                    unit: qty
                });
            }
        });

        $.post('ajax/product_allocation.php', {
            product_id: productId,
            allocations: JSON.stringify(allocations)
        }, function(response) {
            if (response.success) {
                $('#materialAllocModal').modal('hide');
                Swal.fire('Success', response.message || 'Allocation saved.', 'success');
                loadProducts();
            } else {
                Swal.fire('Error', response.message || 'Unable to save allocation.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });
});
</script>