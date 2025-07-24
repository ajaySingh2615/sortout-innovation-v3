<?php
// Start session if not already started
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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid JSON data'
        ]);
        exit();
    }
    
    $action = $input['action'] ?? '';
    $clientIds = $input['client_ids'] ?? [];
    
    // Validate input
    if (!in_array($action, ['approve', 'reject'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action. Must be "approve" or "reject"'
        ]);
        exit();
    }
    
    if (empty($clientIds) || !is_array($clientIds)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No clients selected'
        ]);
        exit();
    }
    
    // Sanitize client IDs
    $clientIds = array_map('intval', $clientIds);
    $clientIds = array_filter($clientIds, function($id) { return $id > 0; });
    
    if (empty($clientIds)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid client IDs'
        ]);
        exit();
    }
    
    $adminId = $_SESSION['user_id'] ?? $_SESSION['username'];
    $placeholders = str_repeat('?,', count($clientIds) - 1) . '?';
    
    // Start transaction
    $conn->autocommit(false);
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    if ($action === 'approve') {
        // Approve selected clients
        $query = "UPDATE clients SET 
                    approval_status = 'approved', 
                    approved_by = ?, 
                    rejected_by = NULL, 
                    rejected_at = NULL 
                  WHERE id IN ($placeholders) AND approval_status = 'pending'";
        
        $stmt = $conn->prepare($query);
        $params = array_merge([$adminId], $clientIds);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        
        if ($stmt->execute()) {
            $successCount = $stmt->affected_rows;
            
            // Log the bulk approval
            $logQuery = "INSERT INTO admin_actions_log (admin_id, action_type, client_ids, action_timestamp) 
                        VALUES (?, 'bulk_approve', ?, NOW())";
            $logStmt = $conn->prepare($logQuery);
            $clientIdsJson = json_encode($clientIds);
            $logStmt->bind_param("ss", $adminId, $clientIdsJson);
            $logStmt->execute();
        }
        
    } else if ($action === 'reject') {
        // Reject selected clients
        $query = "UPDATE clients SET 
                    approval_status = 'rejected', 
                    rejected_by = ?, 
                    rejected_at = NOW(), 
                    approved_by = NULL 
                  WHERE id IN ($placeholders) AND approval_status = 'pending'";
        
        $stmt = $conn->prepare($query);
        $params = array_merge([$adminId], $clientIds);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        
        if ($stmt->execute()) {
            $successCount = $stmt->affected_rows;
            
            // Log the bulk rejection
            $logQuery = "INSERT INTO admin_actions_log (admin_id, action_type, client_ids, action_timestamp) 
                        VALUES (?, 'bulk_reject', ?, NOW())";
            $logStmt = $conn->prepare($logQuery);
            $clientIdsJson = json_encode($clientIds);
            $logStmt->bind_param("ss", $adminId, $clientIdsJson);
            $logStmt->execute();
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Prepare response
    $totalSelected = count($clientIds);
    $skippedCount = $totalSelected - $successCount;
    
    $message = ucfirst($action) . "d $successCount registration(s) successfully";
    if ($skippedCount > 0) {
        $message .= ". $skippedCount registration(s) were skipped (already processed or not found)";
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'processed' => $successCount,
        'skipped' => $skippedCount,
        'total' => $totalSelected
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Bulk action error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred. Please try again.'
    ]);
} finally {
    // Restore autocommit
    $conn->autocommit(true);
    
    // Close database connection
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?> 