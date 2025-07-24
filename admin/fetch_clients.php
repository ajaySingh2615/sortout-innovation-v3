<?php
// Comment out or remove display_errors for production
error_reporting(E_ALL);
ini_set('display_errors', 0); // Change to 0 to prevent displaying errors in output

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure proper authentication
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require '../includes/db_connect.php';  // Database connection

// Set headers before any output
header('Content-Type: application/json'); // Ensure JSON output
header('Cache-Control: no-cache, must-revalidate'); // Prevent caching
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

// Wrap everything in a try-catch to handle any errors
try {
    // Debug: Check if database connection is successful
    if ($conn->connect_error) {
        echo json_encode([
            "status" => "error",
            "message" => "Database connection failed"
        ]);
        exit;
    }

    // Get parameters
    $status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
    $page = max(1, intval($_GET['page'] ?? 1));
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $professional = mysqli_real_escape_string($conn, $_GET['professional'] ?? '');
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');

    $limit = 30; // Show 30 profiles per page (increased from 10)
$offset = ($page - 1) * $limit;

    // Build the base query
    $query = "SELECT * FROM clients WHERE approval_status = '$status'";

    // Add search condition if search term is provided
    if ($search) {
        $query .= " AND (
            name LIKE '%$search%' OR 
            city LIKE '%$search%' OR 
            phone LIKE '%$search%' OR 
            email LIKE '%$search%' OR 
            category LIKE '%$search%' OR 
            role LIKE '%$search%'
        )";
    }

    // Add professional filter if provided
    if ($professional) {
        $query .= " AND professional = '$professional'";
    }

    // Add category filter if provided
    if ($category) {
        $query .= " AND category = '$category'";
    }

    // Get total count for pagination
    $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as count", $query);
    $totalResult = mysqli_query($conn, $countQuery);
    $total = mysqli_fetch_assoc($totalResult)['count'];

    // Add pagination
    $query .= " LIMIT $offset, $limit";

    // Execute the final query
    $result = mysqli_query($conn, $query);

    // Prepare the response
    $clients = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Clean sensitive data
        unset($row['password']);
        $clients[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'clients' => $clients,
        'total' => $total,
        'page' => $page,
        'totalPages' => ceil($total / $limit)
    ]);

} catch (Exception $e) {
    // Log the error to a file instead of displaying it
    error_log("Error in fetch_clients.php: " . $e->getMessage());
    
    // Return a clean JSON error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Error loading clients. Please try again.'
    ]);
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?>
