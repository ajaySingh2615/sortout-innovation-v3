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

try {
    $admin_id = $_SESSION['user_id'] ?? $_SESSION['username'];
    
    // Get the latest registration timestamp that the admin has seen
    $latestSeenQuery = "SELECT MAX(created_at) as latest_seen FROM clients WHERE created_at <= NOW()";
    $latestSeenResult = $conn->query($latestSeenQuery);
    $latestSeen = $latestSeenResult->fetch_assoc()['latest_seen'];
    
    // Store or update the admin's last seen timestamp
    $updateQuery = "INSERT INTO admin_alert_tracking (admin_id, last_seen_timestamp) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    last_seen_timestamp = VALUES(last_seen_timestamp)";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ss", $admin_id, $latestSeen);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Alert marked as seen'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to mark alert as seen'
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
?> 