<?php
require '../includes/db_connect.php';
require '../auth/auth.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Blog ID is required'
    ]);
    exit;
}

$blog_id = intval($_POST['id']);

// Start transaction
$conn->begin_transaction();

try {
    // Check if the blog exists
    $checkStmt = $conn->prepare("SELECT id FROM blogs WHERE id = ?");
    $checkStmt->bind_param("i", $blog_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        // Blog not found
        echo json_encode([
            'success' => false,
            'error' => 'Blog not found'
        ]);
        exit;
    }
    
    // Delete the blog
    $deleteStmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $deleteStmt->bind_param("i", $blog_id);
    $deleteStmt->execute();
    
    // Check if deletion was successful
    if ($deleteStmt->affected_rows > 0) {
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Blog deleted successfully'
        ]);
    } else {
        // No rows affected - something went wrong
        $conn->rollback();
        
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete blog'
        ]);
    }
} catch (Exception $e) {
    // Roll back transaction on error
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]);
}

// Close connection
$conn->close();
?>
