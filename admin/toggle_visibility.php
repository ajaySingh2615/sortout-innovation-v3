<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in response
ini_set('log_errors', 1);

// Ensure proper authentication
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid client ID']);
    exit();
}

$client_id = (int)$_GET['id'];

// Get current visibility status (allow for all clients, not just approved)
$query = "SELECT is_visible, approval_status FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Client not found']);
    exit();
}

$row = $result->fetch_assoc();
$current_visibility = (int)$row['is_visible'];
$new_status = $current_visibility ? 0 : 1; // Toggle the value

// Log the toggle operation for debugging
error_log("Toggle visibility for client ID: $client_id, Current: $current_visibility, New: $new_status");

// Update visibility status
$update_query = "UPDATE clients SET is_visible = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param('ii', $new_status, $client_id);

if ($update_stmt->execute()) {
    // Check if any rows were actually affected
    if ($update_stmt->affected_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Visibility updated successfully', 
            'is_visible' => (int)$new_status,
            'previous_status' => $current_visibility
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'No changes made to visibility status']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Failed to update visibility status: ' . $conn->error]);
}

$update_stmt->close();
$stmt->close();
$conn->close();
?> 