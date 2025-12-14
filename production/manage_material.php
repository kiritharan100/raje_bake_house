<?php include 'header.php'; ?>
<?php
// This page manages Production Materials via AJAX endpoints.
// Data table structure reference: SELECT id, material_name, mesurement, current_price, status FROM production_material WHERE 1
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header">
                    <h4>Manage Materials</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Materials</h5>
                        <button class="btn btn-primary btn-sm" id="addMaterialBtn">
                            <i class="fa fa-plus"></i> Add Material
                        </button>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="material-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Material Name</th>
                                        <th width="120">Measurement</th>
                                        <th width="120">Current Price</th>
                                        <th width="100">Status</th>
                                        <th width="80">Actions</th>
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

<!-- Material Modal -->
<div class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="materialForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Add Material</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="materialId">
                    <div class="form-group row">
                        <label for="materialName" class="col-sm-4 col-form-label text-right">Material Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="materialName" name="material_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="measurement" class="col-sm-4 col-form-label text-right">Measurement</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="measurement" name="mesurement" required>
                                <option value="">Select</option>
                                <option value="Kg">Kg</option>
                                <option value="g">g</option>
                                <option value="L">L</option>
                                <option value="Ml">Ml</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="currentPrice" class="col-sm-4 col-form-label text-right">Current Price</label>
                        <div class="col-sm-8">
                            <input type="number" step="0.01" min="0" class="form-control" id="currentPrice"
                                name="current_price" required>
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

<?php include 'footer.php'; ?>

<script>
    let materialTable = null;

    function badge(status) {
        return status === '1' || status === 1
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
    }

    function renderActions(id) {
        return `
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item action-edit" href="#" data-id="${id}"><i class="fa fa-edit text-primary"></i> Edit</a>
                    <a class="dropdown-item action-inactive" href="#" data-id="${id}"><i class="fa fa-ban text-danger"></i> Inactive</a>
                </div>
            </div>
        `;
    }

    function loadMaterials() {
        $.getJSON('ajax/material_get.php', function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load materials.', 'error');
                return;
            }

            if (materialTable) {
                materialTable.destroy();
            }

            const tbody = $('#material-table tbody');
            tbody.empty();

            response.data.forEach(function (row, index) {
                const tr = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${row.material_name}</td>
                        <td>${row.mesurement}</td>
                        <td>${parseFloat(row.current_price).toFixed(2)}</td>
                        <td>${badge(row.status)}</td>
                        <td>${renderActions(row.id)}</td>
                    </tr>
                `;
                tbody.append(tr);
            });

            materialTable = $('#material-table').DataTable({
                order: [[0, 'asc']],
                pageLength: 100
            });
        }).fail(function () {
            Swal.fire('Error', 'Failed to fetch materials.', 'error');
        });
    }

    function resetForm() {
        $('#materialId').val('');
        $('#materialForm')[0].reset();
        $('#status').val('1');
        $('#materialForm').removeClass('was-validated');
        $('#materialModalLabel').text('Add Material');
    }

    $(document).ready(function () {
        loadMaterials();

        $('#addMaterialBtn').on('click', function () {
            resetForm();
            $('#materialModal').modal('show');
        });

        $(document).on('click', '.action-edit', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const rowData = $(this).closest('tr').children('td');

            $('#materialId').val(id);
            $('#materialName').val(rowData.eq(1).text());
            $('#measurement').val(rowData.eq(2).text());
            $('#currentPrice').val(rowData.eq(3).text());
            const isActive = rowData.eq(4).text().trim() === 'Active' ? '1' : '0';
            $('#status').val(isActive);

            $('#materialModalLabel').text('Edit Material');
            $('#materialModal').modal('show');
        });

        $(document).on('click', '.action-inactive', function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: 'Mark as Inactive?',
                text: 'This will deactivate the material.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, inactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax/material_delete.php', { id: id }, function (response) {
                        if (response.success) {
                            Swal.fire('Updated', response.message || 'Material marked inactive.', 'success');
                            loadMaterials();
                        } else {
                            Swal.fire('Error', response.message || 'Unable to update material.', 'error');
                        }
                    }, 'json').fail(function () {
                        Swal.fire('Error', 'Request failed. Please try again.', 'error');
                    });
                }
            });
        });

        $('#materialForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                e.stopPropagation();
                $(form).addClass('was-validated');
                return;
            }

            const formData = $(form).serialize();

            $.post('ajax/material_save.php', formData, function (response) {
                if (response.success) {
                    $('#materialModal').modal('hide');
                    Swal.fire('Success', response.message || 'Material saved.', 'success');
                    loadMaterials();
                } else {
                    Swal.fire('Error', response.message || 'Unable to save material.', 'error');
                }
            }, 'json').fail(function () {
                Swal.fire('Error', 'Request failed. Please try again.', 'error');
            });
        });
    });
</script>
