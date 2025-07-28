<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// ✅ Ensure Only Admins & Super Admins Can Access
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header("Location: ../index.php");
    exit();
}

// ✅ Pagination Setup
$limit = 20; // Records per page
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// ✅ Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$job_category_filter = isset($_GET['job_category']) ? trim($_GET['job_category']) : '';
$experience_filter = isset($_GET['experience_range']) ? trim($_GET['experience_range']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// ✅ Build Query with Filters
$where_conditions = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR phone_number LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
    $types .= "ss";
}

if (!empty($job_category_filter)) {
    $where_conditions[] = "job_category = ?";
    $params[] = $job_category_filter;
    $types .= "s";
}

if (!empty($experience_filter)) {
    $where_conditions[] = "experience_range = ?";
    $params[] = $experience_filter;
    $types .= "s";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
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

// ✅ Get Total Count
$count_query = "SELECT COUNT(*) as total FROM candidates $where_clause";
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// ✅ Get Candidates Data
$main_query = "SELECT * FROM candidates $where_clause ORDER BY created_at DESC LIMIT $start, $limit";
$main_stmt = $conn->prepare($main_query);
if (!empty($params)) {
    $main_stmt->bind_param($types, ...$params);
}
$main_stmt->execute();
$candidates = $main_stmt->get_result();

// ✅ Get Statistics
$stats_query = "SELECT * FROM candidate_stats";
$stats = $conn->query($stats_query)->fetch_assoc();

// ✅ Get Job Categories for Filter from JSON file
$job_roles_json = file_get_contents('../job_roles.json');
$job_roles_data = json_decode($job_roles_json, true);
$all_categories = [];
if ($job_roles_data && isset($job_roles_data['job_categories'])) {
    foreach ($job_roles_data['job_categories'] as $category) {
        $all_categories[] = $category['category'];
    }
}

// ✅ Handle AJAX requests for quick actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_status') {
        $candidate_id = intval($_POST['candidate_id']);
        $new_status = $_POST['status'];
        
        if (in_array($new_status, ['active', 'archived', 'contacted'])) {
            $update_stmt = $conn->prepare("UPDATE candidates SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_status, $candidate_id);
            
            if ($update_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
        }
        exit();
    }
    
    if ($_POST['action'] === 'delete_candidate') {
        $candidate_id = intval($_POST['candidate_id']);
        
        $delete_stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
        $delete_stmt->bind_param("i", $candidate_id);
        
        if ($delete_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Candidate deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete candidate']);
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>Candidate Management Dashboard - SortOut Innovation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-red: #d90429;
            --secondary-red: #ef233c;
            --accent-red: #e63946;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Enhanced Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            box-shadow: 0 4px 15px rgba(217, 4, 41, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }

        /* Statistics Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary-red);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
        }

        .stats-label {
            color: var(--dark-gray);
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Modern Filters Container */
        .filters-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 1px solid rgba(217, 4, 41, 0.1);
            position: relative;
            overflow: hidden;
        }

        .filters-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--secondary-red) 100%);
        }

        .filter-title {
            color: var(--primary-red);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            position: relative;
        }

        .filter-title i {
            margin-right: 0.75rem;
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .filter-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            border-radius: 2px;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--primary-red);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: rgba(217, 4, 41, 0.05);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d1f2eb;
            color: #148a5c;
        }

        .status-contacted {
            background-color: #d4f1fc;
            color: #0c7a96;
        }

        .status-archived {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Action Buttons */
        .btn-action {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            border: none;
            font-size: 0.8rem;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .btn-view {
            background-color: var(--primary-red);
            color: white;
        }

        .btn-view:hover {
            background-color: var(--secondary-red);
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .page-link {
            color: var(--primary-red);
            border-color: #dee2e6;
        }

        .page-link:hover {
            color: var(--secondary-red);
            background-color: rgba(217, 4, 41, 0.1);
            border-color: var(--primary-red);
        }

        .page-item.active .page-link {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }

        /* Modal Styling */
        .modal-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            color: white;
        }

        .modal-title {
            font-weight: 600;
        }

        /* Export Button */
        .btn-export {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }

        /* Modern Form Controls */
        .filters-container .form-control,
        .filters-container .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filters-container .form-control:focus,
        .filters-container .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(217, 4, 41, 0.15);
            transform: translateY(-1px);
        }

        .filters-container .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Modern Date Shortcut Buttons */
         .date-shortcut {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
             transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            background: white;
            color: #6c757d;
            font-weight: 500;
            margin: 0.25rem;
         }

         .date-shortcut:hover {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
             border-color: var(--primary-red);
             color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(217, 4, 41, 0.3);
         }

         .date-shortcut.btn-danger {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
             border-color: var(--primary-red);
             color: white;
            box-shadow: 0 4px 12px rgba(217, 4, 41, 0.3);
        }

        /* Modern Action Buttons */
        .filters-container .btn {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid;
        }

        .filters-container .btn-outline-danger {
            border-color: var(--primary-red);
            color: var(--primary-red);
            background: white;
        }

        .filters-container .btn-outline-danger:hover {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            border-color: var(--primary-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 4, 41, 0.3);
        }

        .filters-container .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
            background: white;
        }

        .filters-container .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        /* Enhanced Export Button */
        .btn-export {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }

        /* Filters Layout */
         .filters-container .row {
            margin: 0 -0.75rem;
         }

         .filters-container .col-lg-6,
         .filters-container .col-lg-4,
         .filters-container .col-md-6,
         .filters-container .col-md-12 {
            padding: 0 0.75rem;
        }

        /* Filter Groups */
        .filter-group {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(217, 4, 41, 0.1);
            transition: all 0.3s ease;
        }

        .filter-group:hover {
            box-shadow: 0 4px 15px rgba(217, 4, 41, 0.1);
            transform: translateY(-1px);
        }

        /* Live Filter Indicators */
        .live-filter-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .live-filter-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Enhanced Loading States */
        .filter-loading {
            position: relative;
        }

        .filter-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .filter-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-red);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Active Filter Highlight */
        .form-control:not(:placeholder-shown),
        .form-select:not([value=""]) {
            border-color: var(--primary-red);
            background-color: rgba(217, 4, 41, 0.02);
        }

        /* Filter Status Bar */
        .filter-status {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            display: none;
        }

        .filter-status.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modern Results Summary */
        .results-summary-container {
            margin-bottom: 2rem;
        }

        .results-summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(217, 4, 41, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .results-info {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .results-count {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .count-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-red);
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .count-label {
            font-size: 0.9rem;
            color: var(--dark-gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .results-range {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .range-text {
            font-size: 0.9rem;
            color: #495057;
            font-weight: 500;
        }

        .range-text strong {
            color: var(--primary-red);
            font-weight: 700;
        }

        .results-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .view-options {
            display: flex;
            gap: 0.5rem;
        }

        .view-toggle {
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            border: 2px solid #e9ecef;
            background: white;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .view-toggle:hover {
            border-color: var(--primary-red);
            color: var(--primary-red);
            transform: translateY(-1px);
        }

        .view-toggle.active {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            border-color: var(--primary-red);
            color: white;
            box-shadow: 0 4px 12px rgba(217, 4, 41, 0.3);
        }

        .sort-options .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            background: white;
            min-width: 150px;
            transition: all 0.3s ease;
        }

        .sort-options .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(217, 4, 41, 0.15);
        }

        /* Enhanced Table Styling */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(217, 4, 41, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1.25rem 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .table th:first-child {
            border-top-left-radius: 20px;
        }

        .table th:last-child {
            border-top-right-radius: 20px;
        }

        .table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-color: #f8f9fa;
            font-size: 0.95rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(217, 4, 41, 0.05) 0%, rgba(239, 35, 60, 0.05) 100%);
            border-left-color: var(--primary-red);
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .status-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-active {
            background: linear-gradient(135deg, #d1f2eb 0%, #a8e6cf 100%);
            color: #148a5c;
            border: 1px solid rgba(20, 138, 92, 0.2);
        }

        .status-contacted {
            background: linear-gradient(135deg, #d4f1fc 0%, #b3e5fc 100%);
            color: #0c7a96;
            border: 1px solid rgba(12, 122, 150, 0.2);
        }

        .status-archived {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid rgba(114, 28, 36, 0.2);
        }

        /* Enhanced Action Buttons */
        .btn-action {
            padding: 0.5rem;
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-view {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            color: white;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, var(--secondary-red) 0%, var(--primary-red) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(217, 4, 41, 0.4);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #212529;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffc107 100%);
            color: #212529;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 193, 7, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #dc3545 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
        }

        /* Experience Badge Enhancement */
        .badge.bg-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
            border-radius: 15px;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
        }

        /* Responsive Design for Results Summary */
        @media (max-width: 768px) {
            .results-summary-card {
                flex-direction: column;
                align-items: stretch;
                padding: 1.25rem;
            }

            .results-info {
                justify-content: center;
                gap: 1rem;
            }

            .results-actions {
                justify-content: center;
                gap: 0.75rem;
            }

            .count-number {
                font-size: 1.5rem;
            }

            .view-options {
                width: 100%;
                justify-content: center;
            }

            .sort-options {
                width: 100%;
            }

            .sort-options .form-select {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .results-summary-card {
                padding: 1rem;
            }

            .results-info {
                flex-direction: column;
                gap: 0.75rem;
            }

            .results-range {
                justify-content: center;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }

            .btn-action {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
                margin: 0 0.15rem;
            }
        }

        .filter-group-title {
            font-weight: 600;
            color: var(--primary-red);
            margin-bottom: 1rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .filter-group-title i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
         }

         /* Responsive Design */
         @media (max-width: 1200px) {
             .filters-container {
                 padding: 1.5rem;
             }
             
             .filter-group {
                 padding: 1.25rem;
             }
         }

         @media (max-width: 768px) {
             .table-responsive {
                 font-size: 0.9rem;
             }
             
             .stats-number {
                 font-size: 1.5rem;
             }
             
             .filters-container {
                 padding: 1.25rem;
                 border-radius: 15px;
             }

             .filter-title {
                 font-size: 1.1rem;
             }

             .date-shortcut {
                 font-size: 0.75rem;
                 padding: 0.4rem 0.8rem;
                 margin: 0.15rem;
             }

             .filters-container .form-control,
             .filters-container .form-select {
                 padding: 0.6rem 0.8rem;
                 font-size: 0.9rem;
             }

             .filters-container .btn {
                 padding: 0.6rem 1.2rem;
                 font-size: 0.9rem;
             }

             .filters-container .row {
                 margin: 0 -0.5rem;
             }

             .filters-container .col-lg-6,
             .filters-container .col-lg-4,
             .filters-container .col-md-6,
             .filters-container .col-md-12 {
                 padding: 0 0.5rem;
             }

             .filter-group {
                 padding: 1rem;
                 margin-bottom: 0.75rem;
             }
         }

         @media (max-width: 576px) {
             .filters-container {
                 padding: 1rem;
             }

             .filter-title {
                 font-size: 1rem;
             }

             .date-shortcut {
                 font-size: 0.7rem;
                 padding: 0.3rem 0.6rem;
                 margin: 0.1rem;
             }

             .filters-container .btn {
                 width: 100%;
                 margin-bottom: 0.5rem;
             }

             .filters-container .row {
                 margin: 0 -0.25rem;
             }

             .filters-container .col-lg-6,
             .filters-container .col-lg-4,
             .filters-container .col-md-6,
             .filters-container .col-md-12 {
                 padding: 0 0.25rem;
             }
         }

        /* Compact Candidate Modal Styles */
        .candidate-modal {
            border: none;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .candidate-modal-header {
            background: var(--primary-red);
            border: none;
            padding: 0.75rem 1rem;
            color: white;
        }

        .candidate-modal-header .modal-title {
            color: white;
            font-size: 1rem;
            font-weight: 600;
        }

        .candidate-modal-body {
            padding: 1rem;
            background: white;
        }

        /* Compact Candidate Details Content Styles */
        .candidate-details-container {
            background: white;
        }

        .candidate-section {
            margin-bottom: 1rem;
        }

        .candidate-section:last-child {
            margin-bottom: 0;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid #e9ecef;
        }

        .section-icon {
            width: 20px;
            height: 20px;
            background: var(--primary-red);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
        }

        .info-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 0.6rem;
            border-left: 2px solid var(--primary-red);
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 0.85rem;
            font-weight: 500;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .info-value i {
            color: var(--primary-red);
            font-size: 0.75rem;
        }

        .info-value a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
        }

        .status-badge-modern {
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active-modern {
            background: #d1f2eb;
            color: #148a5c;
        }

        .status-contacted-modern {
            background: #d4f1fc;
            color: #0c7a96;
        }

        .status-archived-modern {
            background: #f8d7da;
            color: #721c24;
        }

        .job-category-badge {
            background: var(--primary-red);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .experience-badge {
            background: #667eea;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .date-info {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 0.4rem;
            border-left: 2px solid #17a2b8;
        }

        .date-info .date-main {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.2rem;
        }

        .date-info .date-secondary {
            font-size: 0.7rem;
            color: #6c757d;
        }

        /* Compact Quick Actions Section */
        .quick-actions-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 0.75rem;
            margin-top: 0.75rem;
            border: 1px solid #e9ecef;
        }

        .quick-actions-header {
            margin-bottom: 0.75rem;
        }

        .quick-actions-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.2rem;
        }

        .quick-actions-subtitle {
            font-size: 0.7rem;
            color: #6c757d;
        }

        .quick-actions-grid {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .action-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            color: #495057;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .action-btn-contact {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .action-btn-contact:hover {
            background: #17a2b8;
            color: white;
        }

        .action-btn-active {
            border-color: #28a745;
            color: #28a745;
        }

        .action-btn-active:hover {
            background: #28a745;
            color: white;
        }

        .action-btn-archive {
            border-color: #6c757d;
            color: #6c757d;
        }

        .action-btn-archive:hover {
            background: #6c757d;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .candidate-modal-header {
                padding: 0.6rem 0.8rem;
            }

            .candidate-modal-body {
                padding: 0.8rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 0.6rem;
            }

            .quick-actions-grid {
                flex-direction: column;
            }

            .action-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-users me-2"></i>
            Candidate Management Dashboard
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="main_dashboard.php">
                        <i class="fas fa-arrow-left me-1"></i> Main Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-semibold px-3" href="#" role="button" data-bs-toggle="dropdown">
                        👤 <?= $_SESSION['username']; ?> (<?= ucfirst($_SESSION['role']); ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-number"><?= number_format($stats['total_candidates']) ?></div>
                <div class="stats-label">Total Candidates</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-number"><?= number_format($stats['today_registrations']) ?></div>
                <div class="stats-label">Today's Registrations</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="stats-number"><?= number_format($stats['active_candidates']) ?></div>
                <div class="stats-label">Active Candidates</div>
            </div>
        </div>
                 <div class="col-md-3 mb-3">
             <div class="stats-card">
                 <div class="stats-number" style="font-size: 1.2rem;"><?= htmlspecialchars($stats['most_common_experience'] ?? 'N/A') ?></div>
                 <div class="stats-label">Most Common Experience</div>
             </div>
         </div>
    </div>

    <!-- Modern Filters -->
    <div class="filters-container">
        <h5 class="filter-title">
            <i class="fas fa-sliders-h"></i>
            Advanced Filters & Search
            <span class="live-filter-indicator">LIVE</span>
        </h5>
        
        <!-- Filter Status Bar -->
        <div class="filter-status" id="filterStatus">
            <i class="fas fa-info-circle me-2"></i>
            <span id="filterStatusText">Filters applied: <span id="activeFiltersCount">0</span></span>
        </div>
        
        <form method="GET" id="filterForm">
            <!-- Search & Basic Filters -->
            <div class="filter-group">
                <h6 class="filter-group-title">
                    <i class="fas fa-search"></i>
                    Search & Basic Filters
                </h6>
                         <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                        <label class="form-label">Search Candidates</label>
                        <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or phone number...">
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Job Category</label>
                    <select class="form-select" name="job_category">
                        <option value="">All Categories</option>
                            <?php foreach ($all_categories as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>" 
                                        <?= $job_category_filter === $category ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category) ?>
                            </option>
                            <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6">
                        <label class="form-label">Experience Level</label>
                    <select class="form-select" name="experience_range">
                            <option value="">All Experience Levels</option>
                        <option value="Fresher" <?= $experience_filter === 'Fresher' ? 'selected' : '' ?>>Fresher</option>
                        <option value="1-2 years" <?= $experience_filter === '1-2 years' ? 'selected' : '' ?>>1-2 years</option>
                        <option value="2-3 years" <?= $experience_filter === '2-3 years' ? 'selected' : '' ?>>2-3 years</option>
                        <option value="3-4 years" <?= $experience_filter === '3-4 years' ? 'selected' : '' ?>>3-4 years</option>
                        <option value="4-5 years" <?= $experience_filter === '4-5 years' ? 'selected' : '' ?>>4-5 years</option>
                        <option value="5-7 years" <?= $experience_filter === '5-7 years' ? 'selected' : '' ?>>5-7 years</option>
                        <option value="7-10 years" <?= $experience_filter === '7-10 years' ? 'selected' : '' ?>>7-10 years</option>
                        <option value="10+ years" <?= $experience_filter === '10+ years' ? 'selected' : '' ?>>10+ years</option>
                    </select>
                    </div>
                </div>
                </div>
                
            <!-- Status & Date Filters -->
            <div class="filter-group">
                <h6 class="filter-group-title">
                    <i class="fas fa-calendar-alt"></i>
                    Status & Date Range
                </h6>
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Candidate Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="contacted" <?= $status_filter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                        <option value="archived" <?= $status_filter === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                
                    <div class="col-lg-4 col-md-6">
                    <label class="form-label">Date Range</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" class="form-control" name="date_from" id="date_from" value="<?= htmlspecialchars($date_from) ?>" placeholder="From">
                        </div>
                        <div class="col-6">
                            <input type="date" class="form-control" name="date_to" id="date_to" value="<?= htmlspecialchars($date_to) ?>" placeholder="To">
                        </div>
                    </div>
                </div>
                
                    <div class="col-lg-5 col-md-12">
                    <label class="form-label">Quick Date Filters</label>
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm date-shortcut" data-range="today">Today</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm date-shortcut" data-range="yesterday">Yesterday</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm date-shortcut" data-range="this_week">This Week</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm date-shortcut" data-range="last_week">Last Week</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm date-shortcut" data-range="this_month">This Month</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-search me-2"></i> Apply Filters
                    </button>
                        <a href="candidate_dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Clear All
                    </a>
                        <div class="dropdown">
                        <button type="button" class="btn btn-export dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i> Export Data
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportData('csv')">
                                <i class="fas fa-file-csv me-2"></i> Export as CSV
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('xlsx')">
                                <i class="fas fa-file-excel me-2"></i> Export as Excel (XLSX)
                            </a></li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modern Results Summary -->
    <div class="results-summary-container">
        <div class="results-summary-card">
            <div class="results-info">
                <div class="results-count">
                    <i class="fas fa-users text-primary me-2"></i>
                    <span class="count-number"><?= number_format($total_records) ?></span>
                    <span class="count-label">Total Candidates</span>
                </div>
                <div class="results-range">
                    <i class="fas fa-eye text-success me-2"></i>
                    <span class="range-text">
                        Showing <strong><?= min($start + 1, $total_records) ?></strong> to <strong><?= min($start + $limit, $total_records) ?></strong>
                    </span>
                </div>
            </div>
            <div class="results-actions">
                <div class="view-options">
                    <button class="btn btn-sm btn-outline-primary view-toggle active" data-view="table">
                        <i class="fas fa-table me-1"></i> Table
                    </button>
                    <button class="btn btn-sm btn-outline-primary view-toggle" data-view="cards">
                        <i class="fas fa-th-large me-1"></i> Cards
                    </button>
                </div>
                <div class="sort-options">
                    <select class="form-select form-select-sm" id="sortBy">
                        <option value="created_at DESC">Newest First</option>
                        <option value="created_at ASC">Oldest First</option>
                        <option value="full_name ASC">Name A-Z</option>
                        <option value="full_name DESC">Name Z-A</option>
                        <option value="experience_range ASC">Experience Low-High</option>
                        <option value="experience_range DESC">Experience High-Low</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table">
                                 <thead>
                     <tr>
                         <th>Name</th>
                         <th>Contact</th>
                         <th>Gender</th>
                         <th>Role</th>
                         <th>Experience</th>
                         <th>Status</th>
                         <th>Registered</th>
                         <th>Actions</th>
                     </tr>
                 </thead>
                <tbody>
                    <?php if ($candidates->num_rows > 0): ?>
                                                 <?php while ($candidate = $candidates->fetch_assoc()): ?>
                             <tr id="candidate-<?= $candidate['id'] ?>">
                                 <td>
                                     <strong><?= htmlspecialchars($candidate['full_name']) ?></strong>
                                 </td>
                                 <td>
                                     <i class="fas fa-phone text-muted me-1"></i><?= htmlspecialchars($candidate['phone_number']) ?>
                                 </td>
                                 <td><?= ucfirst($candidate['gender']) ?></td>
                                 <td>
                                     <strong><?= htmlspecialchars($candidate['job_role']) ?></strong><br>
                                     <small class="text-muted"><?= htmlspecialchars($candidate['job_category']) ?></small>
                                 </td>
                                 <td>
                                     <span class="badge bg-info text-dark"><?= htmlspecialchars($candidate['experience_range']) ?></span>
                                 </td>
                                 <td>
                                     <span class="status-badge status-<?= $candidate['status'] ?>">
                                         <?= ucfirst($candidate['status']) ?>
                                     </span>
                                 </td>
                                 <td>
                                     <?= date('M d, Y', strtotime($candidate['created_at'])) ?><br>
                                     <small class="text-muted"><?= date('h:i A', strtotime($candidate['created_at'])) ?></small>
                                 </td>
                                 <td>
                                     <button class="btn btn-action btn-view" onclick="viewCandidate(<?= $candidate['id'] ?>)" title="View Details">
                                         <i class="fas fa-eye"></i>
                                     </button>
                                     <button class="btn btn-action btn-delete" onclick="deleteCandidate(<?= $candidate['id'] ?>)" title="Delete">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 </td>
                             </tr>
                         <?php endwhile; ?>
                                         <?php else: ?>
                         <tr>
                             <td colspan="8" class="text-center py-4">
                                 <i class="fas fa-search fa-3x text-muted mb-3"></i><br>
                                 <h5 class="text-muted">No candidates found</h5>
                                 <p class="text-muted">Try adjusting your filters or search criteria</p>
                             </td>
                         </tr>
                     <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Candidates pagination">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                </li>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Candidate Details Modal -->
<div class="modal fade" id="candidateModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content candidate-modal">
            <div class="modal-header candidate-modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Candidate Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body candidate-modal-body" id="candidateDetails">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // View candidate details
    function viewCandidate(id) {
        $('#candidateDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...</div>');
        $('#candidateModal').modal('show');
        
        $.get('fetch_candidate_details.php', {id: id}, function(data) {
            $('#candidateDetails').html(data);
        }).fail(function() {
            $('#candidateDetails').html('<div class="text-center text-danger">Error loading candidate details</div>');
        });
    }
    
    // Update candidate status
    function updateStatus(id, status) {
        if (confirm(`Are you sure you want to mark this candidate as ${status}?`)) {
            $.post('candidate_dashboard.php', {
                action: 'update_status',
                candidate_id: id,
                status: status
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    }
    
    // Delete candidate
    function deleteCandidate(id) {
        if (confirm('Are you sure you want to delete this candidate? This action cannot be undone.')) {
            $.post('candidate_dashboard.php', {
                action: 'delete_candidate',
                candidate_id: id
            }, function(response) {
                if (response.success) {
                    $(`#candidate-${id}`).fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }
    }
    
    // Export data
    function exportData(type = 'csv') {
        const params = new URLSearchParams(window.location.search);
        params.set('type', type);
        window.open('export_candidates.php?' + params.toString(), '_blank');
    }
    
    // Live filtering functionality
    let filterTimeout;
    const filterDelay = 500; // 500ms delay for search input
    
    // Function to perform live filtering
    function performLiveFilter() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams();
        let activeFilters = 0;
        
        // Add all form data to params and count active filters
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
                activeFilters++;
            }
        }
        
        // Add page parameter
        params.append('page', '1');
        
        // Update filter status
        updateFilterStatus(activeFilters);
        
        // Show loading state
        showLoadingState();
        
        // Show live indicator
        $('.live-filter-indicator').addClass('show');
        
        // Make AJAX request
        $.get('candidate_dashboard.php?' + params.toString(), function(data) {
            // Extract content from response
            const tableContent = $(data).find('.table-container').html();
            const paginationContent = $(data).find('.pagination').parent().html();
            const resultsSummaryContent = $(data).find('.results-summary-container').html();
            
            // Update content
            $('.table-container').html(tableContent);
            $('.pagination').parent().html(paginationContent);
            $('.results-summary-container').html(resultsSummaryContent);
            
            // Update URL without page reload
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newUrl);
            
            // Hide loading state
            hideLoadingState();
            
            // Hide live indicator after a delay
            setTimeout(() => {
                $('.live-filter-indicator').removeClass('show');
            }, 2000);
            
            // Reinitialize any necessary event handlers
            initializeEventHandlers();
        }).fail(function() {
            hideLoadingState();
            showErrorMessage('Error loading filtered results. Please try again.');
            $('.live-filter-indicator').removeClass('show');
        });
    }
    
    // Update filter status
    function updateFilterStatus(activeFilters) {
        const statusBar = $('#filterStatus');
        const countElement = $('#activeFiltersCount');
        
        countElement.text(activeFilters);
        
        if (activeFilters > 0) {
            statusBar.addClass('show');
        } else {
            statusBar.removeClass('show');
        }
    }
    
    // Show loading state
    function showLoadingState() {
        $('.table-container').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Filtering candidates...</p>
            </div>
        `);
    }
    
    // Hide loading state
    function hideLoadingState() {
        // Loading state will be replaced by actual content
    }
    
    // Show error message
    function showErrorMessage(message) {
        $('.table-container').html(`
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Error</h5>
                <p class="text-muted">${message}</p>
                <button class="btn btn-outline-danger" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Reload Page
                </button>
            </div>
        `);
    }
    
    // Initialize event handlers for dynamic content
    function initializeEventHandlers() {
        // Re-attach click handlers for action buttons
        $('.btn-view').off('click').on('click', function() {
            const id = $(this).attr('onclick').match(/\d+/)[0];
            viewCandidate(id);
        });
        
        $('.btn-delete').off('click').on('click', function() {
            const id = $(this).attr('onclick').match(/\d+/)[0];
            deleteCandidate(id);
        });
        
        // Initialize Bootstrap dropdowns for dynamically loaded content
        $('.dropdown-toggle').each(function() {
            const dropdownToggleEl = this;
            // Check if dropdown is already initialized
            if (!dropdownToggleEl.hasAttribute('data-bs-toggle')) {
                // Re-add the data-bs-toggle attribute if it was lost
                dropdownToggleEl.setAttribute('data-bs-toggle', 'dropdown');
            }
            // Initialize the dropdown if not already done
            if (!dropdownToggleEl._bsDropdown) {
                new bootstrap.Dropdown(dropdownToggleEl);
            }
        });
    }
    
    // Live search input with debouncing
    $('input[name="search"]').on('input', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(performLiveFilter, filterDelay);
    });
    
    // Live filter for select elements
    $('select[name="job_category"], select[name="experience_range"], select[name="status"]').on('change', function() {
        performLiveFilter();
    });
    
    // Live filter for date inputs
    $('input[name="date_from"], input[name="date_to"]').on('change', function() {
        performLiveFilter();
    });
    
    // Date shortcuts functionality with live filtering
    $('.date-shortcut').on('click', function() {
        const range = $(this).data('range');
        const today = new Date();
        let startDate, endDate;
        
        switch(range) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = endDate = yesterday.toISOString().split('T')[0];
                break;
            case 'this_week':
                const startOfWeek = new Date(today);
                startOfWeek.setDate(today.getDate() - today.getDay());
                startDate = startOfWeek.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_week':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                startDate = lastWeekStart.toISOString().split('T')[0];
                endDate = lastWeekEnd.toISOString().split('T')[0];
                break;
            case 'this_month':
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                startDate = startOfMonth.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
        }
        
        $('#date_from').val(startDate);
        $('#date_to').val(endDate);
        
        // Highlight active button
        $('.date-shortcut').removeClass('btn-danger').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-danger');
        
        // Trigger live filter
        performLiveFilter();
    });
    
    // Prevent form submission and use live filtering instead
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        performLiveFilter();
    });
    
    // Clear filters with live update
    $('a[href="candidate_dashboard.php"]').on('click', function(e) {
        e.preventDefault();
        
        // Clear all form inputs
        $('#filterForm')[0].reset();
        
        // Remove active state from date shortcuts
        $('.date-shortcut').removeClass('btn-danger').addClass('btn-outline-secondary');
        
        // Perform live filter with cleared values
        performLiveFilter();
    });
    
    // Pagination with live filtering
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        
        const href = $(this).attr('href');
        const url = new URL(href, window.location.origin);
        const params = url.searchParams;
        
        // Show loading state
        showLoadingState();
        
        // Make AJAX request for pagination
        $.get('candidate_dashboard.php?' + params.toString(), function(data) {
            const tableContent = $(data).find('.table-container').html();
            const paginationContent = $(data).find('.pagination').parent().html();
            const resultsSummaryContent = $(data).find('.results-summary-container').html();
            
            $('.table-container').html(tableContent);
            $('.pagination').parent().html(paginationContent);
            $('.results-summary-container').html(resultsSummaryContent);
            
            // Update URL
            window.history.pushState({}, '', href);
            
            // Reinitialize event handlers
            initializeEventHandlers();
        }).fail(function() {
            hideLoadingState();
            showErrorMessage('Error loading page. Please try again.');
        });
    });
    
    // Initialize event handlers on page load
    initializeEventHandlers();
    
    // View toggle functionality
    $('.view-toggle').on('click', function() {
        const view = $(this).data('view');
        
        // Update active state
        $('.view-toggle').removeClass('active');
        $(this).addClass('active');
        
        // Toggle view (placeholder for future card view implementation)
        if (view === 'cards') {
            // TODO: Implement card view
            alert('Card view coming soon!');
            $(this).removeClass('active');
            $('.view-toggle[data-view="table"]').addClass('active');
        }
    });
    
    // Sort functionality
    $('#sortBy').on('change', function() {
        const sortValue = $(this).val();
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('sort', sortValue);
        currentUrl.searchParams.set('page', '1');
        
        // Show loading state
        showLoadingState();
        
        // Make AJAX request with new sort
        $.get(currentUrl.toString(), function(data) {
            const tableContent = $(data).find('.table-container').html();
            const paginationContent = $(data).find('.pagination').parent().html();
            const resultsSummaryContent = $(data).find('.results-summary-container').html();
            
            $('.table-container').html(tableContent);
            $('.pagination').parent().html(paginationContent);
            $('.results-summary-container').html(resultsSummaryContent);
            
            // Update URL
            window.history.pushState({}, '', currentUrl.toString());
            
            // Reinitialize event handlers
            initializeEventHandlers();
        }).fail(function() {
            showErrorMessage('Error applying sort. Please try again.');
        });
    });
    
    // Enhanced hover effects for table rows
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-hover-effect');
        },
        function() {
            $(this).removeClass('table-hover-effect');
        }
    );
</script>

</body>
</html> 