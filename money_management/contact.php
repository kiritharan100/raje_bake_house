<?php include 'header.php'; ?>
<?php
// Manage bank contacts (payees) via AJAX.
// Data source: SELECT contact_id, contact_name, contact_number, status FROM bank_contact WHERE 1
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header">
                    <h4>Suppliers (Payee)</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Contacts</h5>
                        <button class="btn btn-primary btn-sm" id="addContactBtn">
                            <i class="fa fa-plus"></i> Add Contact
                        </button>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="contact-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Contact Name</th>
                                        <th width="180">Contact Number</th>
                                        <th width="100">Status</th>
                                        <th width="90">Actions</th>
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

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="contactForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Add Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="contact_id" id="contactId">
                    <div class="form-group row">
                        <label for="contactName" class="col-sm-4 col-form-label text-right">Contact Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contactName" name="contact_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="contactNumber" class="col-sm-4 col-form-label text-right">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="contactNumber" name="contact_number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="contactStatus" class="col-sm-4 col-form-label text-right">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="contactStatus" name="status">
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
    let contactTable = null;

    function statusBadge(status) {
        return (status === 1 || status === '1')
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
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

    function resetForm() {
        $('#contactId').val('');
        $('#contactForm')[0].reset();
        $('#contactStatus').val('1');
        $('#contactForm').removeClass('was-validated');
        $('#contactModalLabel').text('Add Contact');
    }

    function loadContacts() {
        $.getJSON('ajax/contact_get.php', function (response) {
            if (!response.success) {
                Swal.fire('Error', response.message || 'Unable to load contacts.', 'error');
                return;
            }

            if (contactTable) {
                contactTable.destroy();
            }

            const tbody = $('#contact-table tbody');
            tbody.empty();

            response.data.forEach(function (row, index) {
                const tr = $('<tr>');
                tr.append(`<td>${index + 1}</td>`);
                tr.append($('<td>').addClass('col-name').text(row.contact_name));
                tr.append($('<td>').addClass('col-number').text(row.contact_number));
                tr.append($('<td>').html(statusBadge(row.status)));
                tr.append($('<td>').html(actionMenu(row.contact_id, row.status)));
                tbody.append(tr);
            });

            contactTable = $('#contact-table').DataTable({
                order: [[0, 'asc']],
                pageLength: 100
            });
        }).fail(function () {
            Swal.fire('Error', 'Failed to fetch contacts.', 'error');
        });
    }

    $(document).ready(function () {
        loadContacts();

        $('#addContactBtn').on('click', function () {
            resetForm();
            $('#contactModal').modal('show');
        });

        $(document).on('click', '.action-edit', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const id = $(this).data('id');
            $('#contactId').val(id);
            $('#contactName').val(row.find('.col-name').text());
            $('#contactNumber').val(row.find('.col-number').text());
            const statusText = row.find('td').eq(3).text().trim() === 'Active' ? '1' : '0';
            $('#contactStatus').val(statusText);
            $('#contactModalLabel').text('Edit Contact');
            $('#contactModal').modal('show');
        });

        $(document).on('click', '.action-toggle', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const status = $(this).data('status');
            const actionText = status === 1 || status === '1' ? 'activate' : 'deactivate';

            Swal.fire({
                title: `Confirm ${actionText}`,
                text: `Are you sure you want to ${actionText} this contact?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax/contact_delete.php', { id: id, status: status }, function (response) {
                        if (response.success) {
                            Swal.fire('Updated', response.message || 'Status updated.', 'success');
                            loadContacts();
                        } else {
                            Swal.fire('Error', response.message || 'Unable to update status.', 'error');
                        }
                    }, 'json').fail(function () {
                        Swal.fire('Error', 'Request failed. Please try again.', 'error');
                    });
                }
            });
        });

        $('#contactForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                e.stopPropagation();
                $(form).addClass('was-validated');
                form.reportValidity();
                return;
            }

            const formData = $(form).serialize();

            $.post('ajax/contact_save.php', formData, function (response) {
                if (response.success) {
                    $('#contactModal').modal('hide');
                    Swal.fire('Success', response.message || 'Contact saved.', 'success');
                    loadContacts();
                } else {
                    Swal.fire('Error', response.message || 'Unable to save contact.', 'error');
                }
            }, 'json').fail(function () {
                Swal.fire('Error', 'Request failed. Please try again.', 'error');
            });
        });
    });
</script>
