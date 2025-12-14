<?php
$message = '';
$message_type = 'success';
$reload_after_save = false;

require('header.php');
// Manage production overheads (non-AJAX)

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $effective_from = isset($_POST['effective_from']) ? $_POST['effective_from'] : '';
    $over_head      = isset($_POST['over_head']) ? trim($_POST['over_head']) : '';
    $status         = isset($_POST['status']) ? intval($_POST['status']) : 1;

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $effective_from)) {
        $message = 'Please select a valid date.';
        $message_type = 'error';
    } elseif (!is_numeric($over_head)) {
        $message = 'Please enter a valid overhead amount.';
        $message_type = 'error';
    } else {
        $over_head = floatval($over_head);
        if ($id > 0) {
            $stmt = $con->prepare("UPDATE production_overhead SET effective_from = ?, over_head = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sdii", $effective_from, $over_head, $status, $id);
            $action = 'Updated';
        } else {
            $stmt = $con->prepare("INSERT INTO production_overhead (effective_from, over_head, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $effective_from, $over_head, $status);
            $action = 'Created';
        }

        if ($stmt->execute()) {
            UserLog("Production Overhead", $action, "Overhead ID: " . ($id > 0 ? $id : $stmt->insert_id));
            $message = "Overhead {$action} successfully.";
            $message_type = 'success';
            $reload_after_save = true;
        } else {
            $message = 'Database error: ' . $stmt->error;
            $message_type = 'error';
        }
    }
}

// Fetch for edit if needed
$editRow = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $con->prepare("SELECT id, effective_from, over_head, status FROM production_overhead WHERE id = ?");
    $stmt->bind_param("i", $editId);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $editRow = $res->fetch_assoc();
    }
}

// Fetch list
$rows = [];
$result = mysqli_query($con, "SELECT id, effective_from, over_head, status FROM production_overhead ORDER BY effective_from DESC");
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}

?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Manage Overhead</h4>
                    <button class="btn btn-primary btn-sm" id="addOverheadBtn"><i class="fa fa-plus"></i> Add Overhead</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="m-0">Overhead List</h5>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="overhead-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="60">#</th>
                                        <th width="140">Effective From</th>
                                        <th width="140">Overhead</th>
                                        <th width="100">Status</th>
                                        <th width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $idx => $row) { ?>
                                    <tr>
                                        <td><?php echo $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['effective_from']); ?></td>
                                        <td align="right"><?php echo number_format($row['over_head'], 2); ?></td>
                                        <td class="text-center">
                                            <?php echo $row['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>'; ?>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="manage_overhead.php?edit=<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="overheadModal" tabindex="-1" role="dialog" aria-labelledby="overheadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="overheadForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="overheadModalLabel">Add Overhead</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="ovId" value="">
                    <div class="form-group">
                        <label for="effective_from">Effective From</label>
                        <input type="date" class="form-control" name="effective_from" id="effective_from" required>
                    </div>
                    <div class="form-group">
                        <label for="over_head">Overhead Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="over_head" id="over_head" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
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
    $(document).ready(function() {
        $('#overhead-table').DataTable({
            pageLength: 100,
            order: [[1, 'desc']]
        });

        $('#addOverheadBtn').on('click', function() {
            $('#overheadModalLabel').text('Add Overhead');
            $('#ovId').val('');
            $('#effective_from').val('');
            $('#over_head').val('');
            $('#status').val('1');
            $('#overheadModal').modal('show');
        });

        <?php if ($editRow) { ?>
        // Prefill edit
        $('#overheadModalLabel').text('Edit Overhead');
        $('#ovId').val('<?php echo $editRow['id']; ?>');
        $('#effective_from').val('<?php echo $editRow['effective_from'] ? date('Y-m-d', strtotime($editRow['effective_from'])) : ''; ?>');
        $('#over_head').val('<?php echo $editRow['over_head']; ?>');
        $('#status').val('<?php echo $editRow['status']; ?>');
        $('#overheadModal').modal('show');
        <?php } ?>

        <?php if (!empty($message)) { ?>
            Swal.fire('<?php echo $message_type === 'success' ? 'Success' : 'Error'; ?>', '<?php echo addslashes($message); ?>', '<?php echo $message_type; ?>').then(() => {
                <?php if ($reload_after_save && $message_type === 'success') { ?>
                window.location = 'manage_overhead.php';
                <?php } ?>
            });
        <?php } ?>
    });
</script>
