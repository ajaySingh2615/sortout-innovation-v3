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
    // Get professional type statistics
    $query = "SELECT professional, COUNT(*) as count FROM clients GROUP BY professional ORDER BY count DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    $labels = [];
    $values = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['professional'];
        $values[] = (int)$row['count'];
    }

    // Send response
    echo json_encode([
        'status' => 'success',
        'labels' => $labels,
        'values' => $values
    ]);

} catch (Exception $e) {
    error_log("Error in get_professional_stats.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error loading statistics. Please try again.'
    ]);
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 