<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role']);
}

// Redirect to login page if not logged in
if (!isLoggedIn()) {
    // Store the current URL to redirect back after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header("Location: /auth/login.php");
    exit();
}

// Allow both Admin & Super Admin
if (!in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    echo "Access Denied! Only admins and super admins can access this page.";
    exit();
}
?>
