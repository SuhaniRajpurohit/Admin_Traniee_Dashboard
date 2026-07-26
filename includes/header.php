<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Trainee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
    
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light navbar-custom mb-4">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1 text-dark">
                        <i class="fa-solid fa-circle-user me-2 text-success"></i>Admin Trainee Panel
                    </span>
                    <div class="d-flex align-items-center">
                        <span class="me-3 text-muted"><i class="fa-regular fa-user me-1"></i>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </nav>
            <div class="container-fluid">
