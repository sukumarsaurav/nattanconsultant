<?php
// Start or resume the session
session_start();

// Log logout activity (optional)
if (isset($_SESSION['consultant_id'])) {
    // You could log the logout activity here
    $consultant_id = $_SESSION['consultant_id'];
    $consultant_name = $_SESSION['consultant_name'] ?? 'Unknown';
    
    // Log to a file or database if needed
    // logActivity($consultant_id, 'Logged out', 'consultant');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: consultant-login.php');
exit;
?> 