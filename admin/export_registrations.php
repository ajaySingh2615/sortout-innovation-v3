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
    echo "Unauthorized access";
    exit();
}

try {
    // Get parameters
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

    // Build the query
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

    // Add filters
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

    // Add date range filter
    if ($dateRange) {
        switch ($dateRange) {
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

    $query .= " ORDER BY created_at DESC";

    // Execute query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    // Set headers for CSV download
    $filename = 'registrations_export_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write CSV headers
    $headers = [
        'ID',
        'Name',
        'Age',
        'Gender',
        'Professional',
        'Category/Role',
        'City',
        'Phone',
        'Email',
        'Followers',
        'Experience',
        'Languages',
        'Current Salary',
        'Instagram Profile',
        'Expected Payment',
        'Work Type Preference',
        'Influencer Category',
        'Influencer Type',
        'Approval Status',
        'Is Visible',
        'Registration Date'
    ];
    fputcsv($output, $headers);

    // Write data rows
    while ($row = mysqli_fetch_assoc($result)) {
        $csvRow = [
            $row['id'],
            $row['name'],
            $row['age'],
            $row['gender'],
            $row['professional'],
            $row['professional'] === 'Artist' ? ($row['category'] ?? '') : ($row['role'] ?? ''),
            $row['city'],
            $row['phone'],
            $row['email'] ?? '',
            $row['followers'] ?? '',
            $row['experience'] ?? '',
            $row['language'] ?? '',
            $row['current_salary'] ?? '',
            $row['instagram_profile'] ?? '',
            $row['expected_payment'] ?? '',
            $row['work_type_preference'] ?? '',
            $row['influencer_category'] ?? '',
            $row['influencer_type'] ?? '',
            $row['approval_status'],
            $row['is_visible'] ? 'Yes' : 'No',
            $row['created_at']
        ];
        fputcsv($output, $csvRow);
    }

    // Close output stream
    fclose($output);

} catch (Exception $e) {
    error_log("Error in export_registrations.php: " . $e->getMessage());
    http_response_code(500);
    echo "Error exporting data. Please try again.";
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 