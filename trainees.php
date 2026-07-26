<?php
include_once 'includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['GET'] ?? $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT * FROM trainees WHERE 1=1";

if ($search != '') {
    $query .= " AND full_name LIKE ?";
}
if ($status_filter != '') {
    $query .= " AND status = ?";
}

$query .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $query);

if ($search != '' && $status_filter != '') {
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $status_filter);
} elseif ($search != '') {
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "s", $search_param);
} elseif ($status_filter != '') {
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h2 class="text-dark font-weight-bold mb-1">Trainee Management</h2>
        <p class="text-muted mb-0">View, search, filter, and manage program trainees.</p>
    </div>
    <div class="mt-2 mt-sm-0">
        <a href="add_trainee.php" class="btn btn-primary-custom">
            <i class="fa-solid fa-plus me-1"></i>Add New Trainee
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="trainees.php" method="GET" class="row g-3">
            <div class="col-md-5 col-sm-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            
            <div class="col-md-3 col-sm-4">
                <select name="status" class="form-select">
                    <option value="">Filter by Status</option>
                    <option value="Active" <?php echo ($status_filter == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Completed" <?php echo ($status_filter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            
            <div class="col-md-4 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom w-100">Filter</button>
                <a href="trainees.php" class="btn btn-secondary-custom w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email & Phone</th>
                        <th>Batch</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($trainee = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4">
                                    <strong><?php echo htmlspecialchars($trainee['full_name']); ?></strong><br>
                                    <small class="text-muted">Joined: <?php echo date('M d, Y', strtotime($trainee['joining_date'])); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($trainee['email']); ?><br>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($trainee['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($trainee['batch']); ?></td>
                                <td><?php echo htmlspecialchars($trainee['department']); ?></td>
                                <td>
                                    <?php if ($trainee['status'] == 'Active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="edit_trainee.php?id=<?php echo $trainee['id']; ?>" class="btn btn-sm btn-secondary-custom">
                                            <i class="fa-solid fa-pencil"></i> Edit
                                        </a>
                                        <a href="delete_trainee.php?id=<?php echo $trainee['id']; ?>" onclick="return confirmDeleteTrainee('<?php echo htmlspecialchars(addslashes($trainee['full_name'])); ?>')" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No trainees found matching the criteria.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
include_once 'includes/footer.php';
?>
