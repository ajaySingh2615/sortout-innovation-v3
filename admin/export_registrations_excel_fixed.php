<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: ../auth/login.php");
    exit();
}

try {
    // Get filter parameters
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

    // Build the query with same logic as fetch_registrations.php
    $query = "SELECT 
                id, name, age, gender, phone, email, city, professional, 
                category, role, approval_status, created_at, experience,
                followers, language, current_salary, is_visible,
                influencer_category, influencer_type, expected_payment, work_type_preference,
                instagram_profile, resume_url
              FROM clients WHERE 1=1";
    
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

    // Add filters (same logic as fetch_registrations.php)
    if ($status) {
        $conditions[] = "approval_status = '$status'";
    }
    if ($professional) {
        $conditions[] = "professional = '$professional'";
    }
    if ($category) {
        $conditions[] = "category = '$category'";
    }
    if ($city) {
        $conditions[] = "city = '$city'";
    }
    if ($influencerCategory) {
        $conditions[] = "influencer_category = '$influencerCategory'";
    }
    if ($influencerType) {
        $conditions[] = "influencer_type = '$influencerType'";
    }
    if ($workType) {
        $conditions[] = "work_type_preference = '$workType'";
    }
    if ($paymentRange) {
        $conditions[] = "expected_payment = '$paymentRange'";
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

    // Combine conditions
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // Add ordering
    $query .= " ORDER BY created_at DESC";

    // Execute query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    // Generate filename
    $filename = 'registrations_export_' . date('Y-m-d_H-i-s') . '.xls';
    
    // Add filter description to filename if filters are applied
    $filterParts = [];
    if ($status) $filterParts[] = $status;
    if ($professional) $filterParts[] = $professional;
    if ($dateRange) $filterParts[] = $dateRange;
    
    if (!empty($filterParts)) {
        $filename = 'registrations_' . implode('_', $filterParts) . '_' . date('Y-m-d_H-i-s') . '.xls';
    }

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Start HTML table that Excel can read
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    echo '<meta name="ProgId" content="Excel.Sheet">';
    echo '<meta name="Generator" content="SortOut Innovation Admin Dashboard">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; }';
    echo 'th { background-color: #2563EB; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center; }';
    echo 'td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }';
    echo '.status-approved { background-color: #D1FAE5; }';
    echo '.status-rejected { background-color: #FEE2E2; }';
    echo '.status-pending { background-color: #FEF3C7; }';
    echo '.alternate-row { background-color: #F8FAFC; }';
    echo '.summary { font-weight: bold; margin-top: 20px; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    echo '<table>';
    
    // Header row
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Name</th>';
    echo '<th>Age</th>';
    echo '<th>Gender</th>';
    echo '<th>Phone</th>';
    echo '<th>Email</th>';
    echo '<th>City</th>';
    echo '<th>Professional Type</th>';
    echo '<th>Category/Role</th>';
    echo '<th>Experience</th>';
    echo '<th>Languages</th>';
    echo '<th>Approval Status</th>';
    echo '<th>Registration Date</th>';
    echo '<th>Followers</th>';
    echo '<th>Current Salary</th>';
    echo '<th>Influencer Category</th>';
    echo '<th>Influencer Type</th>';
    echo '<th>Expected Payment</th>';
    echo '<th>Work Type Preference</th>';
    echo '<th>Instagram Profile</th>';
    echo '<th>Visibility Status</th>';
    echo '</tr>';

    // Data rows
    $rowNum = 1;
    while ($client = mysqli_fetch_assoc($result)) {
        $isAlternate = ($rowNum % 2 == 0);
        $rowClass = $isAlternate ? 'alternate-row' : '';
        
        // Determine status class
        $statusClass = '';
        switch ($client['approval_status']) {
            case 'approved':
                $statusClass = 'status-approved';
                break;
            case 'rejected':
                $statusClass = 'status-rejected';
                break;
            case 'pending':
                $statusClass = 'status-pending';
                break;
        }
        
        echo '<tr class="' . $rowClass . '">';
        echo '<td>' . htmlspecialchars($client['id']) . '</td>';
        echo '<td>' . htmlspecialchars($client['name']) . '</td>';
        echo '<td>' . htmlspecialchars($client['age']) . '</td>';
        echo '<td>' . htmlspecialchars($client['gender']) . '</td>';
        echo '<td>' . htmlspecialchars($client['phone']) . '</td>';
        echo '<td>' . htmlspecialchars($client['email'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['city']) . '</td>';
        echo '<td>' . htmlspecialchars($client['professional']) . '</td>';
        echo '<td>' . htmlspecialchars($client['professional'] === 'Artist' ? ($client['category'] ?: 'N/A') : ($client['role'] ?: 'N/A')) . '</td>';
        echo '<td>' . htmlspecialchars($client['experience'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['language'] ?: 'N/A') . '</td>';
        echo '<td class="' . $statusClass . '">' . htmlspecialchars(ucfirst($client['approval_status'])) . '</td>';
        echo '<td>' . htmlspecialchars(date('Y-m-d H:i:s', strtotime($client['created_at']))) . '</td>';
        echo '<td>' . htmlspecialchars($client['followers'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['current_salary'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['influencer_category'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['influencer_type'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['expected_payment'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['work_type_preference'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['instagram_profile'] ?: 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($client['is_visible'] ? 'Visible' : 'Hidden') . '</td>';
        echo '</tr>';
        
        $rowNum++;
    }
    
    echo '</table>';

    // Add summary information
    $totalRows = $rowNum - 1;
    echo '<div class="summary">';
    echo '<h3>Export Summary</h3>';
    echo '<p><strong>Total Records:</strong> ' . $totalRows . '</p>';
    echo '<p><strong>Export Date:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    echo '<p><strong>Exported By:</strong> ' . htmlspecialchars($_SESSION['username'] ?? 'Admin') . '</p>';
    
    // Add filter information if any filters were applied
    if (!empty($filterParts)) {
        echo '<p><strong>Applied Filters:</strong> ' . implode(', ', $filterParts) . '</p>';
    }
    echo '</div>';

    echo '</body>';
    echo '</html>';

} catch (Exception $e) {
    error_log("Excel export error: " . $e->getMessage());
    
    // Fallback to CSV export
    header("Location: export_registrations.php?" . $_SERVER['QUERY_STRING']);
    exit();
} finally {
    // Close database connection
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 