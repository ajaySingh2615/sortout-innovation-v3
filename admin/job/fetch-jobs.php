<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Initialize response
$response = [
    'jobs' => [],
    'pagination' => [],
    'stats' => []
];

// Get pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// Build the base query
$countQuery = "SELECT COUNT(*) as total FROM jobs";
$query = "SELECT * FROM jobs";

// Initialize the WHERE clause
$where = [];
$params = [];
$types = "";

// Apply search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $where[] = "(title LIKE ? OR company_name LIKE ? OR location LIKE ? OR description LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= "ssss";
}

// Apply job type filter
if (isset($_GET['job_type']) && !empty($_GET['job_type'])) {
    $where[] = "job_type = ?";
    $params[] = $_GET['job_type'];
    $types .= "s";
}

// Apply verification status filter
if (isset($_GET['verified']) && $_GET['verified'] !== '') {
    $where[] = "is_verified = ?";
    $params[] = intval($_GET['verified']);
    $types .= "i";
}

// Combine WHERE clauses if any exist
if (!empty($where)) {
    $countQuery .= " WHERE " . implode(" AND ", $where);
    $query .= " WHERE " . implode(" AND ", $where);
}

// Add order by clause
$query .= " ORDER BY created_at DESC";

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Prepare and execute the count query
$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    // Remove the last two parameters (limit and offset) for the count query
    $countParams = array_slice($params, 0, -2);
    $countTypes = substr($types, 0, -2);
    
    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Prepare and execute the main query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch jobs data
$jobs = [];
while ($row = $result->fetch_assoc()) {
    // Format the creation date
    $createdDate = new DateTime($row['created_at']);
    $row['formatted_date'] = $createdDate->format('M d, Y');
    
    // Debugging: Log the raw job_type value
    error_log("Job ID: " . $row['id'] . ", Job Type: " . $row['job_type']);
    
    $jobs[] = $row;
}

// Get stats for the dashboard
$verifiedCount = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE is_verified = 1")->fetch_assoc()['count'];
$highDemandCount = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE high_demand = 1")->fetch_assoc()['count'];

// Build the response
$response['jobs'] = $jobs;
$response['pagination'] = [
    'total_rows' => $totalRows,
    'total_pages' => $totalPages,
    'current_page' => $page,
    'limit' => $limit,
];
$response['stats'] = [
    'verified' => $verifiedCount,
    'high_demand' => $highDemandCount
];

// Close statements
$countStmt->close();
$stmt->close();

// Return the JSON response
echo json_encode($response);
?> 