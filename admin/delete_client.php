<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Client ID is required'
    ]);
    exit();
}

$client_id = intval($_GET['id']);

try {
    // First, check if the client exists
    $checkQuery = "SELECT id, name, image_url, resume_url FROM clients WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $client_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Client not found'
        ]);
        exit();
    }
    
    $client = $result->fetch_assoc();
    
    // Delete the client record
    $deleteQuery = "DELETE FROM clients WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $client_id);
    
    if ($deleteStmt->execute()) {
        // Optionally delete associated files (be careful with file paths)
        if (!empty($client['image_url']) && file_exists("../" . $client['image_url'])) {
            // Only delete if it's not a default image
            if (strpos($client['image_url'], 'default') === false) {
                @unlink("../" . $client['image_url']);
            }
        }
        
        if (!empty($client['resume_url']) && file_exists("../" . $client['resume_url'])) {
            @unlink("../" . $client['resume_url']);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Client deleted successfully',
            'client_name' => $client['name']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete client: ' . $conn->error
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

// Close database connection
$conn->close();
