<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once 'includes/db.php';

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM trainees WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Trainee record deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete trainee record.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Query preparation failed.";
    }
} else {
    $_SESSION['error'] = "No trainee ID specified.";
}

header("Location: trainees.php");
exit();
?>
