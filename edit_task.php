<?php
include_once 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    $_SESSION['error'] = 'Invalid request. No task ID specified.';
    header("Location: assign_task.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM tasks WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$task = mysqli_fetch_assoc($result)) {
        $_SESSION['error'] = 'Task not found.';
        header("Location: assign_task.php");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = 'Database error fetching task details.';
    header("Location: assign_task.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $update_sql = "UPDATE tasks SET trainee_id = ?, task_title = ?, task_description = ?, assigned_date = ?, due_date = ?, priority = ?, status = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($stmt, "issssssi", $trainee_id, $task_title, $task_description, $assigned_date, $due_date, $priority, $status, $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'Task details updated successfully.';
                header("Location: assign_task.php");
                exit();
            } else {
                $error = 'Database update failed. Please try again.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Query preparation failed. Contact admin.';
        }
    }
}

$trainees_query = "SELECT id, full_name, status FROM trainees ORDER BY full_name ASC";
$trainees_result = mysqli_query($conn, $trainees_query);
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark font-weight-bold mb-1">Edit Task</h2>
        <p class="text-muted">Modify assigned task properties.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-dark font-weight-semibold">Edit Task Details</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form id="taskForm" action="edit_task.php?id=<?php echo $id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="trainee_id" class="form-label">Trainee <span class="text-danger">*</span></label>
                        <select name="trainee_id" id="trainee_id" class="form-select" required>
                            <option value="">Select Trainee</option>
                            <?php if (mysqli_num_rows($trainees_result) > 0): ?>
                                <?php while ($trainee = mysqli_fetch_assoc($trainees_result)): ?>
                                    <option value="<?php echo $trainee['id']; ?>" <?php echo ($task['trainee_id'] == $trainee['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($trainee['full_name']); ?>
                                        (<?php echo htmlspecialchars($trainee['status']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" name="task_title" id="task_title" class="form-control" placeholder="Enter task title" value="<?php echo htmlspecialchars($task['task_title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="task_description" class="form-label">Task Description</label>
                        <textarea name="task_description" id="task_description" class="form-control" rows="3" placeholder="Enter details..."><?php echo htmlspecialchars($task['task_description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Assigned Date <span class="text-danger">*</span></label>
                        <input type="date" name="assigned_date" id="assigned_date" class="form-control" value="<?php echo htmlspecialchars($task['assigned_date']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" id="due_date" class="form-control" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="Low" <?php echo ($task['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo ($task['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo ($task['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($task['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="assign_task.php" class="btn btn-secondary-custom">Cancel</a>
                        <button type="submit" class="btn btn-primary-custom">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
