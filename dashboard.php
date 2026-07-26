<?php
include_once 'includes/header.php';

$query = "SELECT COUNT(*) AS count FROM trainees";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_trainees = $row['count'];

$query = "SELECT COUNT(*) AS count FROM trainees WHERE status = 'Active'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$active_trainees = $row['count'];

$query = "SELECT COUNT(*) AS count FROM trainees WHERE status = 'Completed'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$completed_trainees = $row['count'];

$query = "SELECT COUNT(*) AS count FROM tasks";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_tasks = $row['count'];

$query = "SELECT COUNT(*) AS count FROM tasks WHERE status = 'Pending'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$pending_tasks = $row['count'];

$query = "SELECT COUNT(*) AS count FROM tasks WHERE status = 'Completed'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$completed_tasks = $row['count'];

$latest_trainees_query = "SELECT * FROM trainees ORDER BY id DESC LIMIT 5";
$latest_trainees_result = mysqli_query($conn, $latest_trainees_query);

$latest_tasks_query = "SELECT t.*, tr.full_name FROM tasks t JOIN trainees tr ON t.trainee_id = tr.id ORDER BY t.id DESC LIMIT 5";
$latest_tasks_result = mysqli_query($conn, $latest_tasks_query);
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark font-weight-bold mb-1">Dashboard</h2>
        <p class="text-muted">Quick overview of system metrics and recent activities.</p>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-total-trainees h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Total Trainees</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $total_trainees; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-active-trainees h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Active Trainees</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $active_trainees; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-completed-trainees h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Completed Trainees</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $completed_trainees; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-total-tasks h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Total Assigned Tasks</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $total_tasks; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-pending-tasks h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Pending Tasks</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $pending_tasks; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="card card-stat bg-completed-tasks h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 text-uppercase mb-1">Completed Tasks</h6>
                    <h3 class="mb-0 text-white font-weight-bold"><?php echo $completed_tasks; ?></h3>
                </div>
                <div class="fs-1 text-white-50">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark font-weight-semibold">Latest Trainees</h5>
                <a href="trainees.php" class="btn btn-sm btn-secondary-custom">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($latest_trainees_result) > 0): ?>
                                <?php while ($trainee = mysqli_fetch_assoc($latest_trainees_result)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($trainee['full_name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($trainee['email']); ?></small></td>
                                        <td><?php echo htmlspecialchars($trainee['department']); ?></td>
                                        <td>
                                            <?php if ($trainee['status'] == 'Active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No trainees found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark font-weight-semibold">Latest Assigned Tasks</h5>
                <a href="assign_task.php" class="btn btn-sm btn-secondary-custom">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task</th>
                                <th>Trainee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($latest_tasks_result) > 0): ?>
                                <?php while ($task = mysqli_fetch_assoc($latest_tasks_result)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($task['task_title']); ?></strong><br><small class="text-muted">Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?></small></td>
                                        <td><?php echo htmlspecialchars($task['full_name']); ?></td>
                                        <td>
                                            <?php if ($task['status'] == 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($task['status'] == 'In Progress'): ?>
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No tasks assigned.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
