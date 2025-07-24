<?php
require_once '../../auth/auth.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage apps and categories.";
    exit();
}

// Redirect to the artist dashboard
header("Location: artist_dashboard.php");
exit;
?> 