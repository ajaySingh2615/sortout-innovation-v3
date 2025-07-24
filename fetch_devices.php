<?php
require 'includes/db_connect.php';

header('Content-Type: application/json');

// ✅ Get Filters from AJAX Request
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$price_range = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$start = ($page - 1) * $limit;

// ✅ Build Query with Filters
$query = "SELECT * FROM devices WHERE 1=1";
$params = [];
$types = "";

// ✅ Apply Search Filter
if (!empty($search)) {
    $query .= " AND device_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// ✅ Apply Category Filter
if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// ✅ Apply Price Range Filter
if (!empty($price_range)) {
    switch ($price_range) {
        case 'below_10000':
            $query .= " AND price < 10000";
            break;
        case '10000_30000':
            $query .= " AND price BETWEEN 10000 AND 30000";
            break;
        case '30000_50000':
            $query .= " AND price BETWEEN 30000 AND 50000";
            break;
        case 'above_50000':
            $query .= " AND price > 50000";
            break;
    }
}

// ✅ Apply Pagination
$query .= " ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= "ii";

// ✅ Debugging: Log the final SQL Query (Remove in production)
error_log("Final SQL Query: " . $query);
error_log("Parameters: " . json_encode($params));

// ✅ Prepare & Execute Query
$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$devices = [];
while ($row = $result->fetch_assoc()) {
    $devices[] = $row;
}

// ✅ Get Total Count of Filtered Devices
$countQuery = "SELECT COUNT(*) as total FROM devices WHERE 1=1";
$countParams = [];
$countTypes = "";

// Apply the same filters as above
if (!empty($search)) {
    $countQuery .= " AND device_name LIKE ?";
    $countParams[] = "%$search%";
    $countTypes .= "s";
}
if (!empty($category)) {
    $countQuery .= " AND category = ?";
    $countParams[] = $category;
    $countTypes .= "s";
}
if (!empty($price_range)) {
    switch ($price_range) {
        case 'below_10000':
            $countQuery .= " AND price < 10000";
            break;
        case '10000_30000':
            $countQuery .= " AND price BETWEEN 10000 AND 30000";
            break;
        case '30000_50000':
            $countQuery .= " AND price BETWEEN 30000 AND 50000";
            break;
        case 'above_50000':
            $countQuery .= " AND price > 50000";
            break;
    }
}

// ✅ Prepare & Execute Count Query
$countStmt = $conn->prepare($countQuery);
if (!empty($countTypes)) {
    $countStmt->bind_param($countTypes, ...$countParams);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalDevices = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalDevices / $limit);

// ✅ Debugging: Log the total count query
error_log("Total Count Query: " . $countQuery);
error_log("Total Devices Found: " . $totalDevices);

// ✅ Send JSON Response
echo json_encode([
    'devices' => $devices,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
?>
