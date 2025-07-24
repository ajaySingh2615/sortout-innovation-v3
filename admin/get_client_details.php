<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Check if user is authorized
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Get client ID
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception("Invalid client ID");
    }

    // Fetch client details
    $query = "SELECT * FROM clients WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    $client = mysqli_fetch_assoc($result);

    if (!$client) {
        throw new Exception("Client not found");
    }

    // Clean sensitive data
    unset($client['password']);
    
    // Ensure is_visible has a default value
    if (!isset($client['is_visible'])) {
        $client['is_visible'] = 1;
    } else {
        $client['is_visible'] = (int)$client['is_visible'];
    }

    // Send response
    echo json_encode([
        'status' => 'success',
        'client' => $client
    ]);

} catch (Exception $e) {
    error_log("Error in get_client_details.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error loading client details. Please try again.'
    ]);
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 