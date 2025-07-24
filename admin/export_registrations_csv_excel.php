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
    $filename = 'registrations_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    // Add filter description to filename if filters are applied
    $filterParts = [];
    if ($status) $filterParts[] = $status;
    if ($professional) $filterParts[] = $professional;
    if ($dateRange) $filterParts[] = $dateRange;
    
    if (!empty($filterParts)) {
        $filename = 'registrations_' . implode('_', $filterParts) . '_' . date('Y-m-d_H-i-s') . '.csv';
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Add BOM for proper UTF-8 encoding in Excel
    echo "\xEF\xBB\xBF";

    // Create file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Define headers
    $headers = [
        'ID',
        'Name',
        'Age',
        'Gender',
        'Phone',
        'Email',
        'City',
        'Professional Type',
        'Category/Role',
        'Experience',
        'Languages',
        'Approval Status',
        'Registration Date',
        'Followers',
        'Current Salary',
        'Influencer Category',
        'Influencer Type',
        'Expected Payment',
        'Work Type Preference',
        'Instagram Profile',
        'Visibility Status'
    ];

    // Write headers
    fputcsv($output, $headers);

    // Write data rows
    $totalRows = 0;
    while ($client = mysqli_fetch_assoc($result)) {
        $row = [
            $client['id'],
            $client['name'],
            $client['age'],
            $client['gender'],
            $client['phone'],
            $client['email'] ?: 'N/A',
            $client['city'],
            $client['professional'],
            $client['professional'] === 'Artist' ? ($client['category'] ?: 'N/A') : ($client['role'] ?: 'N/A'),
            $client['experience'] ?: 'N/A',
            $client['language'] ?: 'N/A',
            ucfirst($client['approval_status']),
            date('Y-m-d H:i:s', strtotime($client['created_at'])),
            $client['followers'] ?: 'N/A',
            $client['current_salary'] ?: 'N/A',
            $client['influencer_category'] ?: 'N/A',
            $client['influencer_type'] ?: 'N/A',
            $client['expected_payment'] ?: 'N/A',
            $client['work_type_preference'] ?: 'N/A',
            $client['instagram_profile'] ?: 'N/A',
            $client['is_visible'] ? 'Visible' : 'Hidden'
        ];
        
        fputcsv($output, $row);
        $totalRows++;
    }

    // Add summary information
    fputcsv($output, []); // Empty row
    fputcsv($output, ['Export Summary:']);
    fputcsv($output, ['Total Records:', $totalRows]);
    fputcsv($output, ['Export Date:', date('Y-m-d H:i:s')]);
    fputcsv($output, ['Exported By:', $_SESSION['username'] ?? 'Admin']);
    
    // Add filter information if any filters were applied
    if (!empty($filterParts)) {
        fputcsv($output, ['Applied Filters:', implode(', ', $filterParts)]);
    }

    fclose($output);

} catch (Exception $e) {
    error_log("CSV export error: " . $e->getMessage());
    
    // Show error message
    header('Content-Type: text/html');
    echo '<h3>Export Error</h3>';
    echo '<p>An error occurred while exporting data. Please try again.</p>';
    echo '<p><a href="registrations_dashboard.php">Back to Dashboard</a></p>';
} finally {
    // Close database connection
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 