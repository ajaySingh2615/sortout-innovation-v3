<?php
// Database connection is already included in the files that use these functions

/**
 * Function to sanitize user input
 * @param string $data - The data to sanitize
 * @return string - The sanitized data
 */
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $conn->real_escape_string($data);
    }
}

/**
 * Show success message
 * @param string $message - The message to show
 */
if (!function_exists('showSuccess')) {
    function showSuccess($message) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

/**
 * Show error message
 * @param string $message - The message to show
 */
if (!function_exists('showError')) {
    function showError($message) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

/**
 * Get all talent registrations with filtering options
 * @param array $filters - The filters to apply (optional)
 * @return array - The registrations
 */
function getTalentRegistrations($filters = []) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_talent_registrations WHERE 1=1";
    $params = [];
    $types = "";
    
    // Apply filters if provided
    if (!empty($filters)) {
        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        // Filter by talent category
        if (isset($filters['talent_category']) && !empty($filters['talent_category'])) {
            $sql .= " AND talent_category = ?";
            $params[] = $filters['talent_category'];
            $types .= "s";
        }
        
        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        // Search by name or contact
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND (full_name LIKE ? OR contact_number LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
    }
    
    // Order by created_at DESC by default
    $sql .= " ORDER BY created_at DESC";
    
    // Add pagination if needed
    if (isset($filters['limit']) && isset($filters['offset'])) {
        $sql .= " LIMIT ?, ?";
        $params[] = (int)$filters['offset'];
        $params[] = (int)$filters['limit'];
        $types .= "ii";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    
    return $registrations;
}

/**
 * Count total talent registrations with filters
 * @param array $filters - The filters to apply (optional)
 * @return int - The total count
 */
function countTalentRegistrations($filters = []) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM artist_v2_talent_registrations WHERE 1=1";
    $params = [];
    $types = "";
    
    // Apply filters if provided (same as getTalentRegistrations but without pagination)
    if (!empty($filters)) {
        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        // Filter by talent category
        if (isset($filters['talent_category']) && !empty($filters['talent_category'])) {
            $sql .= " AND talent_category = ?";
            $params[] = $filters['talent_category'];
            $types .= "s";
        }
        
        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        // Search by name or contact
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND (full_name LIKE ? OR contact_number LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Get a specific talent registration by ID
 * @param int $id - The registration ID
 * @return array|false - The registration or false if not found
 */
function getTalentRegistrationById($id) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_talent_registrations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Get all talent categories
 * @param bool $activeOnly - Whether to return only active categories
 * @return array - The categories
 */
function getTalentCategories($activeOnly = false) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_talent_categories";
    if ($activeOnly) {
        $sql .= " WHERE status = 1";
    }
    $sql .= " ORDER BY name ASC";
    
    $result = $conn->query($sql);
    $categories = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Add a new talent registration
 * @param array $data - The registration data
 * @return int|bool - The new registration ID or false on failure
 */
function addTalentRegistration($data) {
    global $conn;
    
    // Check if app_name and app_id exists in data
    $appNameExists = isset($data['app_name']) && !empty($data['app_name']);
    $appIdExists = isset($data['app_id']) && !empty($data['app_id']);
    
    // Determine which fields to include in query based on data
    if ($appNameExists && $appIdExists) {
        $sql = "INSERT INTO artist_v2_talent_registrations (full_name, contact_number, gender, talent_category, app_name, app_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", 
            $data['full_name'], 
            $data['contact_number'], 
            $data['gender'], 
            $data['talent_category'],
            $data['app_name'],
            $data['app_id']
        );
    } elseif ($appNameExists) {
        $sql = "INSERT INTO artist_v2_talent_registrations (full_name, contact_number, gender, talent_category, app_name) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", 
            $data['full_name'], 
            $data['contact_number'], 
            $data['gender'], 
            $data['talent_category'],
            $data['app_name']
        );
    } elseif ($appIdExists) {
        $sql = "INSERT INTO artist_v2_talent_registrations (full_name, contact_number, gender, talent_category, app_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", 
            $data['full_name'], 
            $data['contact_number'], 
            $data['gender'], 
            $data['talent_category'],
            $data['app_id']
        );
    } else {
        $sql = "INSERT INTO artist_v2_talent_registrations (full_name, contact_number, gender, talent_category) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $data['full_name'], 
            $data['contact_number'], 
            $data['gender'], 
            $data['talent_category']
        );
    }
    
    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    
    return false;
}

/**
 * Update a talent registration's status
 * @param int $id - The registration ID
 * @param string $status - The new status
 * @param string $notes - Optional notes
 * @return bool - True on success, false on failure
 */
function updateTalentRegistrationStatus($id, $status, $notes = null) {
    global $conn;
    
    $sql = "UPDATE artist_v2_talent_registrations SET status = ?";
    $params = [$status];
    $types = "s";
    
    if ($notes !== null) {
        $sql .= ", notes = ?";
        $params[] = $notes;
        $types .= "s";
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    return $stmt->execute();
}

/**
 * Get registration counts by status
 * @return array - Counts for each status
 */
function getTalentRegistrationStatusCounts() {
    global $conn;
    
    $sql = "SELECT status, COUNT(*) as count FROM artist_v2_talent_registrations GROUP BY status";
    $result = $conn->query($sql);
    
    $counts = [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'total' => 0
    ];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['total'] += (int)$row['count'];
        }
    }
    
    return $counts;
}

/**
 * Get registration counts by date range
 * @param string $range - The date range (today, week, month, year)
 * @return int - The count
 */
function getTalentRegistrationCountByDateRange($range) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM artist_v2_talent_registrations WHERE ";
    
    switch ($range) {
        case 'today':
            $sql .= "DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $sql .= "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $sql .= "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
            break;
        case 'year':
            $sql .= "YEAR(created_at) = YEAR(CURDATE())";
            break;
        default:
            return 0;
    }
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (int)$row['count'];
    }
    
    return 0;
}

/**
 * Get registration counts by talent category
 * @param int $limit - Limit the number of results
 * @return array - Category counts
 */
function getTalentRegistrationCountsByCategory($limit = 10) {
    global $conn;
    
    $sql = "SELECT talent_category, COUNT(*) as count 
            FROM artist_v2_talent_registrations 
            GROUP BY talent_category 
            ORDER BY count DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $counts = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $counts[$row['talent_category']] = (int)$row['count'];
        }
    }
    
    return $counts;
}

/**
 * Delete a talent registration
 * @param int $id - The registration ID
 * @return bool - True on success, false on failure
 */
function deleteTalentRegistration($id) {
    global $conn;
    
    $sql = "DELETE FROM artist_v2_talent_registrations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

/**
 * Generate a date range for filtering
 * @param string $range - The predefined range (today, week, month, year)
 * @return array - The date range [from, to]
 */
function generateDateRange($range) {
    $dates = [
        'from' => '',
        'to' => date('Y-m-d') // Today as the default end date
    ];
    
    switch ($range) {
        case 'today':
            $dates['from'] = date('Y-m-d');
            break;
        case 'week':
            $dates['from'] = date('Y-m-d', strtotime('-1 week'));
            break;
        case 'month':
            $dates['from'] = date('Y-m-d', strtotime('-1 month'));
            break;
        case 'year':
            $dates['from'] = date('Y-m-d', strtotime('-1 year'));
            break;
        default:
            $dates['from'] = '';
    }
    
    return $dates;
} 