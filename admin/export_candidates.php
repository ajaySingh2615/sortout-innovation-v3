<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// ✅ Ensure Only Admins & Super Admins Can Access
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    exit('Access Denied');
}

// ✅ Export Type (default to CSV)
$export_type = isset($_GET['type']) ? $_GET['type'] : 'csv';

// ✅ Apply same filters as dashboard
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$job_category_filter = isset($_GET['job_category']) ? trim($_GET['job_category']) : '';
$city_filter = isset($_GET['city']) ? trim($_GET['city']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$min_experience = isset($_GET['min_experience']) ? floatval($_GET['min_experience']) : '';
$max_experience = isset($_GET['max_experience']) ? floatval($_GET['max_experience']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// ✅ Build Query with Filters
$where_conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR phone_number LIKE ? OR city LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= "sss";
}

if (!empty($job_category_filter)) {
    $where_conditions[] = "job_category = ?";
    $params[] = $job_category_filter;
    $types .= "s";
}

if (!empty($city_filter)) {
    $where_conditions[] = "city = ?";
    $params[] = $city_filter;
    $types .= "s";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($min_experience)) {
    $where_conditions[] = "years_experience >= ?";
    $params[] = $min_experience;
    $types .= "d";
}

if (!empty($max_experience)) {
    $where_conditions[] = "years_experience <= ?";
    $params[] = $max_experience;
    $types .= "d";
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// ✅ Fetch all matching candidates
$export_query = "SELECT 
    id,
    full_name,
    age,
    phone_number,
    gender,
    city,
    job_category,
    job_role,
    years_experience,
    current_salary,
    status,
    created_at,
    updated_at
FROM candidates $where_clause ORDER BY created_at DESC";

$export_stmt = $conn->prepare($export_query);
if (!empty($params)) {
    $export_stmt->bind_param($types, ...$params);
}
$export_stmt->execute();
$candidates = $export_stmt->get_result();

// ✅ Generate filename with timestamp
$timestamp = date('Y-m-d_His');
$filename = "candidates_export_$timestamp";

if ($export_type === 'csv') {
    // ✅ CSV Export
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // ✅ CSV Headers
    $headers = [
        'ID',
        'Full Name',
        'Age',
        'Phone Number',
        'Gender',
        'City',
        'Job Category',
        'Job Role',
        'Years of Experience',
        'Current Salary (INR)',
        'Status',
        'Registration Date',
        'Last Updated'
    ];
    
    fputcsv($output, $headers);
    
    // ✅ CSV Data
    while ($candidate = $candidates->fetch_assoc()) {
        $row = [
            $candidate['id'],
            $candidate['full_name'],
            $candidate['age'],
            $candidate['phone_number'],
            ucfirst($candidate['gender']),
            $candidate['city'],
            $candidate['job_category'],
            $candidate['job_role'],
            $candidate['years_experience'],
            $candidate['current_salary'] ? number_format($candidate['current_salary'], 2) : 'Not specified',
            ucfirst($candidate['status']),
            date('Y-m-d H:i:s', strtotime($candidate['created_at'])),
            date('Y-m-d H:i:s', strtotime($candidate['updated_at']))
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} elseif ($export_type === 'xlsx') {
    // ✅ XLSX Export using XML format
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create temporary directory for XLSX files
    $tempDir = sys_get_temp_dir() . '/xlsx_export_' . uniqid();
    mkdir($tempDir);
    mkdir($tempDir . '/_rels');
    mkdir($tempDir . '/xl');
    mkdir($tempDir . '/xl/_rels');
    mkdir($tempDir . '/xl/worksheets');
    
    // Create [Content_Types].xml
    file_put_contents($tempDir . '/[Content_Types].xml', 
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
        '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
        '<Default Extension="xml" ContentType="application/xml"/>' .
        '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
        '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
        '</Types>'
    );
    
    // Create _rels/.rels
    file_put_contents($tempDir . '/_rels/.rels',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
        '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>'
    );
    
    // Create xl/_rels/workbook.xml.rels
    file_put_contents($tempDir . '/xl/_rels/workbook.xml.rels',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
        '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
        '</Relationships>'
    );
    
    // Create xl/workbook.xml
    file_put_contents($tempDir . '/xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
        '<sheets>' .
        '<sheet name="Candidates" sheetId="1" r:id="rId1"/>' .
        '</sheets>' .
        '</workbook>'
    );
    
    // Create worksheet content
    $worksheetXML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
        '<sheetData>';
    
    // Add header row
    $worksheetXML .= '<row r="1">';
    $headers = ['ID', 'Full Name', 'Age', 'Phone Number', 'Gender', 'City', 'Job Category', 'Job Role', 'Years of Experience', 'Current Salary (INR)', 'Status', 'Registration Date', 'Last Updated'];
    foreach ($headers as $col => $header) {
        $cellRef = chr(65 + $col) . '1';
        $worksheetXML .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($header) . '</t></is></c>';
    }
    $worksheetXML .= '</row>';
    
    // Add data rows
    $rowNum = 2;
    while ($candidate = $candidates->fetch_assoc()) {
        $worksheetXML .= '<row r="' . $rowNum . '">';
        
        $data = [
            $candidate['id'],
            $candidate['full_name'],
            $candidate['age'],
            $candidate['phone_number'],
            ucfirst($candidate['gender']),
            $candidate['city'],
            $candidate['job_category'],
            $candidate['job_role'],
            $candidate['years_experience'],
            $candidate['current_salary'] ? number_format($candidate['current_salary'], 2) : 'Not specified',
            ucfirst($candidate['status']),
            date('Y-m-d H:i:s', strtotime($candidate['created_at'])),
            date('Y-m-d H:i:s', strtotime($candidate['updated_at']))
        ];
        
        foreach ($data as $col => $value) {
            $cellRef = chr(65 + $col) . $rowNum;
            if (is_numeric($value)) {
                $worksheetXML .= '<c r="' . $cellRef . '"><v>' . htmlspecialchars($value) . '</v></c>';
            } else {
                $worksheetXML .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($value) . '</t></is></c>';
            }
        }
        $worksheetXML .= '</row>';
        $rowNum++;
    }
    
    $worksheetXML .= '</sheetData></worksheet>';
    
    // Save worksheet
    file_put_contents($tempDir . '/xl/worksheets/sheet1.xml', $worksheetXML);
    
    // Create ZIP file
    $zip = new ZipArchive();
    $zipFile = $tempDir . '.zip';
    
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($tempDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        
        // Output the file
        readfile($zipFile);
        
        // Clean up
        unlink($zipFile);
        $files = glob($tempDir . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $subFiles = glob($file . '/*');
                foreach ($subFiles as $subFile) {
                    if (is_file($subFile)) {
                        unlink($subFile);
                    }
                }
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($tempDir);
    } else {
        // If ZIP creation fails, still clean up temp directory
        $files = glob($tempDir . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $subFiles = glob($file . '/*');
                foreach ($subFiles as $subFile) {
                    if (is_file($subFile)) {
                        unlink($subFile);
                    }
                }
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($tempDir);
        http_response_code(500);
        echo 'Error creating XLSX file';
    }
    
} elseif ($export_type === 'excel') {
    // ✅ Basic Excel Export (HTML table format that Excel can read) - Legacy XLS format
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo '<html><head><meta charset="UTF-8"></head><body>';
    echo '<table border="1">';
    
    // ✅ Excel Headers
    echo '<tr style="background-color: #d90429; color: white; font-weight: bold;">';
    echo '<td>ID</td>';
    echo '<td>Full Name</td>';
    echo '<td>Age</td>';
    echo '<td>Phone Number</td>';
    echo '<td>Gender</td>';
    echo '<td>City</td>';
    echo '<td>Job Category</td>';
    echo '<td>Job Role</td>';
    echo '<td>Years of Experience</td>';
    echo '<td>Current Salary (INR)</td>';
    echo '<td>Status</td>';
    echo '<td>Registration Date</td>';
    echo '<td>Last Updated</td>';
    echo '</tr>';
    
    // ✅ Excel Data
    while ($candidate = $candidates->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($candidate['id']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['full_name']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['age']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['phone_number']) . '</td>';
        echo '<td>' . htmlspecialchars(ucfirst($candidate['gender'])) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['city']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['job_category']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['job_role']) . '</td>';
        echo '<td>' . htmlspecialchars($candidate['years_experience']) . '</td>';
        echo '<td>' . ($candidate['current_salary'] ? '₹' . number_format($candidate['current_salary'], 2) : 'Not specified') . '</td>';
        echo '<td style="background-color: ' . getStatusColor($candidate['status']) . ';">' . htmlspecialchars(ucfirst($candidate['status'])) . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($candidate['created_at'])) . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($candidate['updated_at'])) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</body></html>';
    
} else {
    // ✅ Invalid export type
    http_response_code(400);
    echo 'Invalid export type. Supported types: csv, xlsx, excel';
}

// ✅ Helper function for status colors in Excel
function getStatusColor($status) {
    switch ($status) {
        case 'active':
            return '#d1f2eb';
        case 'contacted':
            return '#d4f1fc';
        case 'archived':
            return '#f8d7da';
        default:
            return '#ffffff';
    }
}

exit();
?> 