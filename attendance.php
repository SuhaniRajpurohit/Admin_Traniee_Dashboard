<?php
include_once 'includes/header.php';

$success_msg = '';
$error_msg = '';
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_attendance'])) {
    $selected_date = $_POST['date'];
    $attendance_records = isset($_POST['attendance']) ? $_POST['attendance'] : [];
    $trainees_sql = "SELECT id FROM trainees WHERE status = 'Active'";
    $trainees_res = mysqli_query($conn, $trainees_sql);
    
    $success = true;
    
    while ($row = mysqli_fetch_assoc($trainees_res)) {
        $trainee_id = $row['id'];
        $status = isset($attendance_records[$trainee_id]) ? $attendance_records[$trainee_id] : 'Absent';
        $check_sql = "SELECT id FROM attendance WHERE trainee_id = ? AND date = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "is", $trainee_id, $selected_date);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        $num_rows = mysqli_stmt_num_rows($check_stmt);
        mysqli_stmt_close($check_stmt);
        
        if ($num_rows > 0) {
            $update_sql = "UPDATE attendance SET status = ? WHERE trainee_id = ? AND date = ?";
            if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sis", $status, $trainee_id, $selected_date);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            } else {
                $success = false;
            }
        } else {
            $insert_sql = "INSERT INTO attendance (trainee_id, date, status) VALUES (?, ?, ?)";
            if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                mysqli_stmt_bind_param($insert_stmt, "iss", $trainee_id, $selected_date, $status);
                mysqli_stmt_execute($insert_stmt);
                mysqli_stmt_close($insert_stmt);
            } else {
                $success = false;
            }
        }
    }
    if ($success) {
        $_SESSION['success'] = "Attendance records updated for " . date('M d, Y', strtotime($selected_date)) . ".";
        header("Location: attendance.php?date=" . $selected_date);
        exit();
    } else {
        $error_msg = "Error updating database records.";
    }
}

$list_sql = "SELECT t.id, t.full_name, t.department, a.status AS att_status 
             FROM trainees t 
             LEFT JOIN attendance a ON t.id = a.trainee_id AND a.date = ? 
             WHERE t.status = 'Active' 
             ORDER BY t.full_name ASC";
$stmt = mysqli_prepare($conn, $list_sql);
mysqli_stmt_bind_param($stmt, "s", $selected_date);
mysqli_stmt_execute($stmt);
$trainees_att_result = mysqli_stmt_get_result($stmt);
$history_sql = "SELECT a.date, t.full_name, t.department, a.status 
                FROM attendance a 
                JOIN trainees t ON a.trainee_id = t.id 
                ORDER BY a.date DESC, t.full_name ASC 
                LIMIT 30";
$history_result = mysqli_query($conn, $history_sql);
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark font-weight-bold mb-1">Attendance Tracker</h2>
        <p class="text-muted mb-0">Record daily check-ins and view attendance logs.</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-1"></i><?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 text-dark font-weight-semibold">Register for Date</h5>
                <form action="attendance.php" method="GET" class="d-flex align-items-center gap-2">
                    <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">
                </form>
            </div>
            <div class="card-body">
                <form action="attendance.php" method="POST">
                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th style="width: 150px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($trainees_att_result) > 0): ?>
                                    <?php while ($trainee = mysqli_fetch_assoc($trainees_att_result)): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($trainee['full_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($trainee['department']); ?></td>
                                            <td>
                                                <select name="attendance[<?php echo $trainee['id']; ?>]" class="form-select form-select-sm">
                                                    <option value="Present" <?php echo ($trainee['att_status'] == 'Present' || is_null($trainee['att_status'])) ? 'selected' : ''; ?>>Present</option>
                                                    <option value="Absent" <?php echo ($trainee['att_status'] == 'Absent') ? 'selected' : ''; ?>>Absent</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No active trainees found to register attendance.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (mysqli_num_rows($trainees_att_result) > 0): ?>
                        <div class="text-end mt-3">
                            <button type="submit" name="save_attendance" class="btn btn-primary-custom px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Save Attendance
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-dark font-weight-semibold">Attendance Log History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Date</th>
                                <th>Trainee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($history_result) > 0): ?>
                                <?php while ($log = mysqli_fetch_assoc($history_result)): ?>
                                    <tr>
                                        <td><small class="fw-semibold"><?php echo date('M d, Y', strtotime($log['date'])); ?></small></td>
                                        <td>
                                            <?php echo htmlspecialchars($log['full_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($log['department']); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($log['status'] == 'Present'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2">Present</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">Absent</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No attendance logs available.</td>
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
