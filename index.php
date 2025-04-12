<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['consultant_id'])) {
    // Redirect to dashboard if logged in
    header("Location: consultant-dashboard.php");
    exit();
} else {
    // Redirect to login page if not logged in
    header("Location: consultant-login.php");
    exit();
}
?> 