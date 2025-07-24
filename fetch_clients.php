<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Include database connection
    require_once 'includes/db_connect.php';
    
    // Check if connection is successful
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Get query parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $professional = isset($_GET['professional']) ? $_GET['professional'] : 'Artist';
    $limit = 50; // Display 50 profiles per page
    $offset = ($page - 1) * $limit;

    // Build the base query - Only select fields used in cards and filters
    $query = "SELECT 
        id,
        name, 
        professional, 
        image_url, 
        city, 
        gender, 
        age, 
        language,
        category,
        role,
        experience,
        current_salary,
        followers,
        resume_url,
        created_at
    FROM clients WHERE professional = ? AND approval_status = 'approved' AND is_visible = 1";
    $params = [$professional];
    $types = "s";

    // Add filters based on professional type
    if ($professional === 'Artist') {
        if (!empty($_GET['category'])) {
            $query .= " AND category = ?";
            $params[] = $_GET['category'];
            $types .= "s";
        }
        if (!empty($_GET['influencer_category'])) {
            $query .= " AND influencer_category = ?";
            $params[] = $_GET['influencer_category'];
            $types .= "s";
        }
        if (!empty($_GET['influencer_type'])) {
            $query .= " AND influencer_type = ?";
            $params[] = $_GET['influencer_type'];
            $types .= "s";
        }
        if (!empty($_GET['expected_payment'])) {
            $query .= " AND expected_payment = ?";
            $params[] = $_GET['expected_payment'];
            $types .= "s";
        }
        if (!empty($_GET['followers'])) {
            // Debug log for followers filter
            error_log("Followers filter value: " . $_GET['followers']);
            
            $followers = explode('-', $_GET['followers']);
            error_log("Followers array after split: " . print_r($followers, true));
            
            if (count($followers) === 2) {
                if ($followers[1] === '+') {
                    // For "500K+" case
                    $minValue = rtrim($followers[0], 'K'); // Remove K suffix
                    $query .= " AND (
                        CASE 
                            WHEN followers LIKE '%M%' THEN CAST(REPLACE(followers, 'M', '') AS DECIMAL(10,1)) * 1000000
                            WHEN followers LIKE '%K%' THEN CAST(REPLACE(followers, 'K', '') AS DECIMAL(10,1)) * 1000
                            ELSE CAST(followers AS DECIMAL(10,1))
                        END
                    ) >= ?";
                    $params[] = floatval($minValue) * 1000; // Convert to actual number (500K = 500000)
                    $types .= "d";
                    error_log("Filtering followers >= " . ($minValue * 1000));
                } else {
                    // For range cases like "10K-50K"
                    $min = rtrim($followers[0], 'K'); // Remove K suffix
                    $max = rtrim($followers[1], 'K'); // Remove K suffix
                    $query .= " AND (
                        CASE 
                            WHEN followers LIKE '%M%' THEN CAST(REPLACE(followers, 'M', '') AS DECIMAL(10,1)) * 1000000
                            WHEN followers LIKE '%K%' THEN CAST(REPLACE(followers, 'K', '') AS DECIMAL(10,1)) * 1000
                            ELSE CAST(followers AS DECIMAL(10,1))
                        END
                    ) BETWEEN ? AND ?";
                    $params[] = floatval($min) * 1000; // Convert to actual number (10K = 10000)
                    $params[] = floatval($max) * 1000; // Convert to actual number (50K = 50000)
                    $types .= "dd";
                    error_log("Filtering followers between " . ($min * 1000) . " and " . ($max * 1000));
                }
            }
            
            // Debug log the final query and parameters
            error_log("Query with followers filter: " . $query);
            error_log("Parameters: " . print_r($params, true));
        }
    } else {
        if (!empty($_GET['role'])) {
            $query .= " AND role = ?";
            $params[] = $_GET['role'];
            $types .= "s";
        }
        if (!empty($_GET['experience'])) {
            $experience = explode('-', $_GET['experience']);
            if (count($experience) === 2) {
                if ($experience[1] === '+') {
                    $query .= " AND experience >= ?";
                    $params[] = $experience[0];
                    $types .= "i";
                } else {
                    $query .= " AND experience BETWEEN ? AND ?";
                    $params[] = $experience[0];
                    $params[] = $experience[1];
                    $types .= "ii";
                }
            }
        }
    }

    // Common filters for both types
    if (!empty($_GET['gender'])) {
        $query .= " AND gender = ?";
        $params[] = $_GET['gender'];
        $types .= "s";
    }
    if (!empty($_GET['age'])) {
        $age = explode('-', $_GET['age']);
        if (count($age) === 2) {
            if ($age[1] === '+') {
                $query .= " AND age >= ?";
                $params[] = $age[0];
                $types .= "i";
            } else {
                $query .= " AND age BETWEEN ? AND ?";
                $params[] = $age[0];
                $params[] = $age[1];
                $types .= "ii";
            }
        }
    }
    if (!empty($_GET['language'])) {
        $query .= " AND FIND_IN_SET(?, language)";
        $params[] = $_GET['language'];
        $types .= "s";
    }
    if (!empty($_GET['city'])) {
        $query .= " AND city = ?";
        $params[] = $_GET['city'];
        $types .= "s";
    }

    // Add sorting
    if (!empty($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'newest':
                $query .= " ORDER BY created_at DESC";
                break;
            case 'popular':
                $query .= " ORDER BY followers DESC";
                break;
            case 'experience':
                $query .= " ORDER BY experience DESC";
                break;
            default:
                $query .= " ORDER BY created_at DESC";
        }
    } else {
        $query .= " ORDER BY created_at DESC";
    }

    // Add pagination
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    // Debug log
    error_log("Query: " . $query);
    error_log("Params: " . print_r($params, true));
    error_log("Types: " . $types);

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $clients = [];

    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }

    // Get total count for pagination
    $countQuery = str_replace("SELECT 
        id,
        name, 
        professional, 
        image_url, 
        city, 
        gender, 
        age, 
        language,
        category,
        role,
        experience,
        current_salary,
        followers,
        resume_url,
        created_at", "SELECT COUNT(*) as total", $query);
    $countQuery = preg_replace("/LIMIT.*OFFSET.*/", "", $countQuery);
    
    $countStmt = $conn->prepare($countQuery);
    if (!empty($params)) {
        array_pop($params); // Remove limit
        array_pop($params); // Remove offset
        $types = substr($types, 0, -2); // Remove "ii" for limit and offset
        $countStmt->bind_param($types, ...$params);
    }
    
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    // Return the response
    echo json_encode([
        'status' => 'success',
        'clients' => $clients,
        'total' => $total,
        'page' => $page,
        'items_per_page' => $limit
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Error in fetch_clients.php: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching clients',
        'debug_message' => $e->getMessage()
    ]);
}
?> 