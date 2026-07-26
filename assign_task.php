<?php
include_once 'includes/header.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_task'])) {
    $trainee_id = intval($_POST['trainee_id']);
    $task_title = trim($_POST['task_title']);
    $task_description = trim($_POST['task_description']);
    $assigned_date = $_POST['assigned_date'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    
    if (empty($trainee_id) || empty($task_title) || empty($assigned_date) || empty($due_date) || empty($priority) || empty($status)) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($due_date) < strtotime($assigned_date)) {
        $error = 'Due Date cannot be before Assigned Date.';
    } else { 
        $sql = "INSERT INTO tasks (trainee_id, task_title, task_description, assigned_date, due_date, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "issssss", $trainee_id, $task_title, $task_description, $assigned_date, $due_date, $priority, $status);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'Task assigned successfully.';
                header("Location: assign_task.php");
                exit();
            } else {
                $error = 'Failed to insert task. Please try again.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Query preparation failed. Contact admin.';
        }
    }
}

$search_title = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$priority_filter = isset($_GET['priority']) ? trim($_GET['priority']) : '';
$tasks_sql = "SELECT t.*, tr.full_name FROM tasks t JOIN trainees tr ON t.trainee_id = tr.id WHERE 1=1";
if ($search_title != '') {
    $tasks_sql .= " AND t.task_title LIKE ?";
}
if ($status_filter != '') {
    $tasks_sql .= " AND t.status = ?";
}
if ($priority_filter != '') {
    $tasks_sql .= " AND t.priority = ?";
}
$tasks_sql .= " ORDER BY t.id DESC";
$stmt = mysqli_prepare($conn, $tasks_sql);

if ($search_title != '' && $status_filter != '' && $priority_filter != '') {
    $search_param = "%$search_title%";
    mysqli_stmt_bind_param($stmt, "sss", $search_param, $status_filter, $priority_filter);
} elseif ($search_title != '' && $status_filter != '') {
    $search_param = "%$search_title%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $status_filter);
} elseif ($search_title != '' && $priority_filter != '') {
    $search_param = "%$search_title%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $priority_filter);
} elseif ($status_filter != '' && $priority_filter != '') {
    mysqli_stmt_bind_param($stmt, "ss", $status_filter, $priority_filter);
} elseif ($search_title != '') {
    $search_param = "%$search_title%";
    mysqli_stmt_bind_param($stmt, "s", $search_param);
} elseif ($status_filter != '') {
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
} elseif ($priority_filter != '') {
    mysqli_stmt_bind_param($stmt, "s", $priority_filter);
}

mysqli_stmt_execute($stmt);
$tasks_result = mysqli_stmt_get_result($stmt);
$trainees_query = "SELECT id, full_name, status FROM trainees ORDER BY full_name ASC";
$trainees_result = mysqli_query($conn, $trainees_query);
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark font-weight-bold mb-1">Task Assignment</h2>
        <p class="text-muted mb-0">Assign tasks to trainees and monitor task completion status.</p>
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

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-dark font-weight-semibold">Assign Work Form</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form id="taskForm" action="assign_task.php" method="POST">
                    <div class="mb-3">
                        <label for="trainee_id" class="form-label">Trainee <span class="text-danger">*</span></label>
                        <select name="trainee_id" id="trainee_id" class="form-select" required>
                            <option value="">Select Trainee</option>
                            <?php if (mysqli_num_rows($trainees_result) > 0): ?>
                                <?php while ($trainee = mysqli_fetch_assoc($trainees_result)): ?>
                                    <option value="<?php echo $trainee['id']; ?>">
                                        <?php echo htmlspecialchars($trainee['full_name']); ?> 
                                        (<?php echo htmlspecialchars($trainee['status']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" name="task_title" id="task_title" class="form-control" placeholder="Enter task title" required>
                    </div>

                    <div class="mb-3">
                        <label for="task_description" class="form-label">Task Description</label>
                        <textarea name="task_description" id="task_description" class="form-control" rows="3" placeholder="Enter details..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assigned Date <span class="text-danger">*</span></label>
                        <input type="date" name="assigned_date" id="assigned_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" id="due_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Pending" selected>Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <button type="submit" name="assign_task" class="btn btn-primary-custom w-100 py-2">
                        <i class="fa-solid fa-paper-plane me-1"></i>Assign Task
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="assign_task.php" method="GET" class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="search_title" class="form-control" placeholder="Search by task title..." value="<?php echo htmlspecialchars($search_title); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Status</option>
                            <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($status_filter == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo ($status_filter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="priority" class="form-select">
                            <option value="">Priority</option>
                            <option value="Low" <?php echo ($priority_filter == 'Low') ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo ($priority_filter == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo ($priority_filter == 'High') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary-custom px-2"><i class="fa-solid fa-filter"></i></button>
                        <a href="assign_task.php" class="btn btn-secondary-custom px-2"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-dark font-weight-semibold">Assigned Tasks</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Task Info</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($tasks_result) > 0): ?>
                                <?php while ($task = mysqli_fetch_assoc($tasks_result)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <strong><?php echo htmlspecialchars($task['task_title']); ?></strong>
                                            <?php if (!empty($task['task_description'])): ?>
                                                <br><small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?php echo htmlspecialchars($task['task_description']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($task['full_name']); ?></td>
                                        <td>
                                            <?php if ($task['priority'] == 'High'): ?>
                                                <span class="badge bg-danger">High</span>
                                            <?php elseif ($task['priority'] == 'Medium'): ?>
                                                <span class="badge bg-warning text-dark">Medium</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Low</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($task['due_date'])); ?></td>
                                        <td>
                                            <?php if ($task['status'] == 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($task['status'] == 'In Progress'): ?>
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-secondary-custom">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" onclick="return confirmDeleteTask()" class="btn btn-sm btn-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No tasks assigned matching filters.</td>
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
mysqli_stmt_close($stmt);
include_once 'includes/footer.php';
?>
