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
    // Get parameters
    $page = max(1, intval($_GET['page'] ?? 1));
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
    $professional = mysqli_real_escape_string($conn, $_GET['professional'] ?? '');
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');
    $city = mysqli_real_escape_string($conn, $_GET['city'] ?? '');
    $dateRange = mysqli_real_escape_string($conn, $_GET['date_range'] ?? '');
    $influencerCategory = mysqli_real_escape_string($conn, $_GET['influencer_category'] ?? '');
    $influencerType = mysqli_real_escape_string($conn, $_GET['influencer_type'] ?? '');
    $workType = mysqli_real_escape_string($conn, $_GET['work_type'] ?? '');
    $paymentRange = mysqli_real_escape_string($conn, $_GET['payment_range'] ?? '');

    $limit = 30; // Show 30 profiles per page (increased from 10)
    $offset = ($page - 1) * $limit;

    // Build the base query
    $query = "SELECT * FROM clients WHERE 1=1";
    $conditions = [];

    // Add search condition
    if ($search) {
        $conditions[] = "(
            name LIKE '%$search%' OR 
            phone LIKE '%$search%' OR 
            email LIKE '%$search%' OR 
            city LIKE '%$search%' OR
            category LIKE '%$search%' OR 
            role LIKE '%$search%'
        )";
    }

    // Add status filter
    if ($status) {
        $conditions[] = "approval_status = '$status'";
    }

    // Add professional filter
    if ($professional) {
        $conditions[] = "professional = '$professional'";
    }

    // Add category filter
    if ($category) {
        $conditions[] = "category = '$category'";
    }

    // Add city filter
    if ($city) {
        $conditions[] = "city = '$city'";
    }

    // Add date range filter
    if ($dateRange) {
        switch ($dateRange) {
            case 'since_last_visit':
                // Get admin's last seen timestamp
                $admin_id = $_SESSION['user_id'] ?? $_SESSION['username'];
                $lastSeenQuery = "SELECT last_seen_timestamp FROM admin_alert_tracking WHERE admin_id = ?";
                $lastSeenStmt = $conn->prepare($lastSeenQuery);
                $lastSeenStmt->bind_param("s", $admin_id);
                $lastSeenStmt->execute();
                $lastSeenResult = $lastSeenStmt->get_result();
                $lastSeen = $lastSeenResult->fetch_assoc();
                
                if ($lastSeen) {
                    $conditions[] = "created_at > '" . $lastSeen['last_seen_timestamp'] . "'";
                } else {
                    // If no tracking record, show registrations from last 24 hours
                    $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                }
                break;
            case 'today':
                $conditions[] = "DATE(created_at) = CURDATE()";
                break;
            case 'yesterday':
                $conditions[] = "DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'last_24h':
                $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                break;
            case 'last_7_days':
                $conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last_30_days':
                $conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'this_month':
                $conditions[] = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
                break;
            case 'last_month':
                $conditions[] = "YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
                break;
        }
    }

    // Add influencer category filter
    if ($influencerCategory) {
        $conditions[] = "influencer_category = '$influencerCategory'";
    }

    // Add influencer type filter
    if ($influencerType) {
        $conditions[] = "influencer_type = '$influencerType'";
    }

    // Add work type filter
    if ($workType) {
        $conditions[] = "work_type_preference = '$workType'";
    }

    // Add payment range filter
    if ($paymentRange) {
        $conditions[] = "expected_payment = '$paymentRange'";
    }

    // Combine conditions
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // Get total count for pagination
    $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as count", $query);
    $totalResult = mysqli_query($conn, $countQuery);
    $total = mysqli_fetch_assoc($totalResult)['count'];

    // Add ordering and pagination
    $query .= " ORDER BY created_at DESC LIMIT $offset, $limit";

    // Execute the final query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    // Prepare the response
    $clients = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Clean sensitive data
        unset($row['password']);
        
        // Ensure is_visible has a default value
        if (!isset($row['is_visible'])) {
            $row['is_visible'] = 1;
        } else {
            $row['is_visible'] = (int)$row['is_visible'];
        }
        
        $clients[] = $row;
    }

    // Send response
    echo json_encode([
        'status' => 'success',
        'clients' => $clients,
        'total' => (int)$total,
        'page' => $page,
        'totalPages' => ceil($total / $limit)
    ]);

} catch (Exception $e) {
    error_log("Error in fetch_registrations.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error loading registrations. Please try again.'
    ]);
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 