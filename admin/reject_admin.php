<?php
require '../auth/auth.php'; // Ensure the user is authenticated
require '../includes/db_connect.php';

// ✅ Ensure only super admins can reject admins
if ($_SESSION['role'] !== 'super_admin') {
    die("❌ Unauthorized action.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $adminId = intval($_POST['id']);

    // ✅ Delete the admin request from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'admin' AND status = 'pending'");
    $stmt->bind_param("i", $adminId);

    if ($stmt->execute()) {
        echo "❌ Admin request rejected.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>
