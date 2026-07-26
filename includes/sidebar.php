<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar">
    <div class="sidebar-header">
        <h4 class="m-0"><i class="fa-solid fa-graduation-cap me-2"></i>Trainee Portal</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class="fa-solid fa-gauge me-2"></i>Dashboard
            </a>
        </li>
        <li class="<?php echo ($current_page == 'trainees.php' || $current_page == 'add_trainee.php' || $current_page == 'edit_trainee.php') ? 'active' : ''; ?>">
            <a href="trainees.php">
                <i class="fa-solid fa-users me-2"></i>Manage Trainees
            </a>
        </li>
        <li class="<?php echo ($current_page == 'assign_task.php') ? 'active' : ''; ?>">
            <a href="assign_task.php">
                <i class="fa-solid fa-list-check me-2"></i>Assign Tasks
            </a>
        </li>
        <li class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
            <a href="attendance.php">
                <i class="fa-solid fa-calendar-days me-2"></i>Attendance
            </a>
        </li>
        <hr class="mx-3 my-2 text-secondary">
        <li>
            <a href="logout.php" class="text-danger">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
        </li>
    </ul>
</nav>
