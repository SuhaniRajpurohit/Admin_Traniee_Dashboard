<?php
include_once 'includes/header.php';

$error = '';
$success = '';
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    $_SESSION['error'] = 'Invalid request. No trainee ID provided.';
    header("Location: trainees.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM trainees WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$trainee = mysqli_fetch_assoc($result)) {
        $_SESSION['error'] = 'Trainee not found.';
        header("Location: trainees.php");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = 'Database error fetching details.';
    header("Location: trainees.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $batch = trim($_POST['batch']);
    $department = trim($_POST['department']);
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];
    
    if (empty($full_name) || empty($email) || empty($phone) || empty($batch) || empty($department) || empty($joining_date) || empty($status)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $check_sql = "SELECT id FROM trainees WHERE email = ? AND id != ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "si", $email, $id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = 'Email address is already in use by another trainee.';
            }
            mysqli_stmt_close($check_stmt);
        }
        
        if (empty($error)) {
            $update_sql = "UPDATE trainees SET full_name = ?, email = ?, phone = ?, batch = ?, department = ?, joining_date = ?, status = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($stmt, "sssssssi", $full_name, $email, $phone, $batch, $department, $joining_date, $status, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success'] = 'Trainee details updated successfully.';
                    header("Location: trainees.php");
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
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark font-weight-bold mb-1">Edit Trainee</h2>
        <p class="text-muted">Modify registered trainee profile info.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-dark font-weight-semibold">Edit Trainee Details</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form id="traineeForm" action="edit_trainee.php?id=<?php echo $id; ?>" method="POST">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Enter full name" value="<?php echo htmlspecialchars($trainee['full_name']); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com" value="<?php echo htmlspecialchars($trainee['email']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter contact number" value="<?php echo htmlspecialchars($trainee['phone']); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="batch" class="form-label">Batch <span class="text-danger">*</span></label>
                            <input type="text" name="batch" id="batch" class="form-control" placeholder="e.g. Batch-A (2026)" value="<?php echo htmlspecialchars($trainee['batch']); ?>" required>
                        </div>
    
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" name="department" id="department" class="form-control" placeholder="e.g. Frontend Development" value="<?php echo htmlspecialchars($trainee['department']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" name="joining_date" id="joining_date" class="form-control" value="<?php echo htmlspecialchars($trainee['joining_date']); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Active" <?php echo ($trainee['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Completed" <?php echo ($trainee['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="trainees.php" class="btn btn-secondary-custom">Cancel</a>
                        <button type="submit" class="btn btn-primary-custom">Update Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'includes/footer.php';
?>
