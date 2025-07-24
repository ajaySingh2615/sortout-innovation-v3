<?php
require_once '../../../auth/auth.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage talent registrations.";
    exit();
}

// Redirect to the talent dashboard
header("Location: dashboard.php");
exit;
?> 