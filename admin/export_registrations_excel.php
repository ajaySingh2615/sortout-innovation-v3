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

// Check if PhpSpreadsheet is available
if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
    // If PhpSpreadsheet is not available, fall back to CSV
    header("Location: export_registrations.php?" . $_SERVER['QUERY_STRING']);
    exit();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

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

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Registrations Export');

    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('SortOut Innovation')
        ->setLastModifiedBy('Admin Dashboard')
        ->setTitle('Registrations Export')
        ->setSubject('Client Registrations Data')
        ->setDescription('Exported client registrations data from admin dashboard')
        ->setKeywords('registrations clients export')
        ->setCategory('Data Export');

    // Define headers
    $headers = [
        'A' => 'ID',
        'B' => 'Name',
        'C' => 'Age',
        'D' => 'Gender',
        'E' => 'Phone',
        'F' => 'Email',
        'G' => 'City',
        'H' => 'Professional Type',
        'I' => 'Category/Role',
        'J' => 'Experience',
        'K' => 'Languages',
        'L' => 'Approval Status',
        'M' => 'Registration Date',
        'N' => 'Followers',
        'O' => 'Current Salary',
        'P' => 'Influencer Category',
        'Q' => 'Influencer Type',
        'R' => 'Expected Payment',
        'S' => 'Work Type Preference',
        'T' => 'Instagram Profile',
        'U' => 'Visibility Status'
    ];

    // Set headers
    $row = 1;
    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . $row, $header);
    }

    // Style the header row
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 12
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2563EB']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    $sheet->getStyle('A1:U1')->applyFromArray($headerStyle);

    // Set column widths
    $columnWidths = [
        'A' => 8,   // ID
        'B' => 20,  // Name
        'C' => 8,   // Age
        'D' => 10,  // Gender
        'E' => 15,  // Phone
        'F' => 25,  // Email
        'G' => 15,  // City
        'H' => 15,  // Professional Type
        'I' => 25,  // Category/Role
        'J' => 12,  // Experience
        'K' => 20,  // Languages
        'L' => 15,  // Approval Status
        'M' => 20,  // Registration Date
        'N' => 12,  // Followers
        'O' => 15,  // Current Salary
        'P' => 25,  // Influencer Category
        'Q' => 25,  // Influencer Type
        'R' => 18,  // Expected Payment
        'S' => 25,  // Work Type Preference
        'T' => 30,  // Instagram Profile
        'U' => 15   // Visibility Status
    ];

    foreach ($columnWidths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }

    // Add data rows
    $row = 2;
    while ($client = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $row, $client['id']);
        $sheet->setCellValue('B' . $row, $client['name']);
        $sheet->setCellValue('C' . $row, $client['age']);
        $sheet->setCellValue('D' . $row, $client['gender']);
        $sheet->setCellValue('E' . $row, $client['phone']);
        $sheet->setCellValue('F' . $row, $client['email'] ?: 'N/A');
        $sheet->setCellValue('G' . $row, $client['city']);
        $sheet->setCellValue('H' . $row, $client['professional']);
        $sheet->setCellValue('I' . $row, $client['professional'] === 'Artist' ? ($client['category'] ?: 'N/A') : ($client['role'] ?: 'N/A'));
        $sheet->setCellValue('J' . $row, $client['experience'] ?: 'N/A');
        $sheet->setCellValue('K' . $row, $client['language'] ?: 'N/A');
        $sheet->setCellValue('L' . $row, ucfirst($client['approval_status']));
        $sheet->setCellValue('M' . $row, date('Y-m-d H:i:s', strtotime($client['created_at'])));
        $sheet->setCellValue('N' . $row, $client['followers'] ?: 'N/A');
        $sheet->setCellValue('O' . $row, $client['current_salary'] ?: 'N/A');
        $sheet->setCellValue('P' . $row, $client['influencer_category'] ?: 'N/A');
        $sheet->setCellValue('Q' . $row, $client['influencer_type'] ?: 'N/A');
        $sheet->setCellValue('R' . $row, $client['expected_payment'] ?: 'N/A');
        $sheet->setCellValue('S' . $row, $client['work_type_preference'] ?: 'N/A');
        $sheet->setCellValue('T' . $row, $client['instagram_profile'] ?: 'N/A');
        $sheet->setCellValue('U' . $row, $client['is_visible'] ? 'Visible' : 'Hidden');

        // Apply alternating row colors
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':U' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F8FAFC');
        }

        // Apply status-based coloring
        $statusColor = '';
        switch ($client['approval_status']) {
            case 'approved':
                $statusColor = 'D1FAE5'; // Light green
                break;
            case 'rejected':
                $statusColor = 'FEE2E2'; // Light red
                break;
            case 'pending':
                $statusColor = 'FEF3C7'; // Light yellow
                break;
        }
        
        if ($statusColor) {
            $sheet->getStyle('L' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColor);
        }

        $row++;
    }

    // Apply borders to all data
    $dataRange = 'A1:U' . ($row - 1);
    $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN)
        ->getColor()->setRGB('E5E7EB');

    // Freeze the header row
    $sheet->freezePane('A2');

    // Add summary information
    $totalRows = $row - 2; // Subtract header and current row
    $summaryRow = $row + 2;
    
    $sheet->setCellValue('A' . $summaryRow, 'Export Summary:');
    $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Records: ' . $totalRows);
    $sheet->setCellValue('A' . ($summaryRow + 2), 'Export Date: ' . date('Y-m-d H:i:s'));
    $sheet->setCellValue('A' . ($summaryRow + 3), 'Exported By: ' . ($_SESSION['username'] ?? 'Admin'));

    // Style summary
    $sheet->getStyle('A' . $summaryRow . ':A' . ($summaryRow + 3))->getFont()->setBold(true);

    // Generate filename
    $filename = 'registrations_export_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    // Add filter description to filename if filters are applied
    $filterParts = [];
    if ($status) $filterParts[] = $status;
    if ($professional) $filterParts[] = $professional;
    if ($dateRange) $filterParts[] = $dateRange;
    
    if (!empty($filterParts)) {
        $filename = 'registrations_' . implode('_', $filterParts) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
    }

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Create writer and output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

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