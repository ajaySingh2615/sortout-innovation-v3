<?php
require '../auth/auth.php'; // Ensure the user is authenticated
require '../includes/db_connect.php';

// ✅ Ensure only super admins can approve admins
if ($_SESSION['role'] !== 'super_admin') {
    die("❌ Unauthorized action.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $adminId = intval($_POST['id']);

    // ✅ Update admin status to "approved"
    $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $adminId);
    
    if ($stmt->execute()) {
        echo "✅ Admin approved successfully.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>
