<?php
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

try {
    // Check if user is logged in and is admin
    session_start();
    if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
        throw new Exception("Unauthorized access");
    }

    // Check if ID is provided
    if (!isset($_GET['id'])) {
        throw new Exception("No client ID provided");
    }

    $client_id = intval($_GET['id']);
    $admin_id = $_SESSION['user_id'];

    // Update client status
    $query = "UPDATE clients SET approval_status = 'rejected', rejected_by = ?, rejected_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $client_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if (mysqli_affected_rows($conn) === 0) {
        throw new Exception("No client found with ID: " . $client_id);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Client rejected successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
