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

// ✅ Get Job Categories for Filter
$categories_query = "SELECT DISTINCT job_category FROM candidates ORDER BY job_category";
$categories = $conn->query($categories_query);

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

        /* Filters Container */
        .filters-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .filter-title {
            color: var(--primary-red);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .filter-title i {
            margin-right: 0.5rem;
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

                 /* Date Shortcut Buttons */
         .date-shortcut {
             font-size: 0.75rem;
             padding: 0.25rem 0.5rem;
             border-radius: 15px;
             transition: all 0.3s ease;
         }

         .date-shortcut:hover {
             background-color: var(--primary-red);
             border-color: var(--primary-red);
             color: white;
         }

         .date-shortcut.btn-danger {
             background-color: var(--primary-red);
             border-color: var(--primary-red);
             color: white;
         }

         /* Filters Layout Fix */
         .filters-container .row {
             margin: 0 -0.5rem;
         }

         .filters-container .col-lg-6,
         .filters-container .col-lg-4,
         .filters-container .col-md-6,
         .filters-container .col-md-12 {
             padding: 0 0.5rem;
         }

         /* Responsive Design */
         @media (max-width: 1200px) {
             .filters-container {
                 padding: 1.5rem 1rem;
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
                 padding: 1rem;
             }

             .date-shortcut {
                 font-size: 0.7rem;
                 padding: 0.2rem 0.4rem;
                 margin-bottom: 0.25rem;
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

    <!-- Filters -->
    <div class="filters-container">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filters & Search
        </h5>
        
        <form method="GET" id="filterForm">
                         <div class="row g-3">
                <!-- First Row -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, phone...">
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Job Category</label>
                    <select class="form-select" name="job_category">
                        <option value="">All Categories</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($category['job_category']) ?>" 
                                    <?= $job_category_filter === $category['job_category'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['job_category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Experience</label>
                    <select class="form-select" name="experience_range">
                        <option value="">All Experience</option>
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
                
                <!-- Second Row -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="contacted" <?= $status_filter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                        <option value="archived" <?= $status_filter === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                
                <!-- Third Row - Date Range -->
                <div class="col-lg-6 col-md-12">
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
                
                <!-- Date Shortcuts -->
                <div class="col-lg-6 col-md-12">
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
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-outline-danger me-2">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <a href="candidate_dashboard.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                    <div class="dropdown d-inline">
                        <button type="button" class="btn btn-export dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export Data
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
        </form>
    </div>

    <!-- Results Summary -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            Showing <?= min($start + 1, $total_records) ?>-<?= min($start + $limit, $total_records) ?> of <?= number_format($total_records) ?> candidates
        </h5>
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
                                     <div class="dropdown d-inline">
                                         <button class="btn btn-action btn-edit dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Change Status">
                                             <i class="fas fa-edit"></i>
                                         </button>
                                         <ul class="dropdown-menu">
                                             <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $candidate['id'] ?>, 'active')">Mark Active</a></li>
                                             <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $candidate['id'] ?>, 'contacted')">Mark Contacted</a></li>
                                             <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $candidate['id'] ?>, 'archived')">Archive</a></li>
                                         </ul>
                                     </div>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Candidate Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="candidateDetails">
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
    
    // Date shortcuts functionality
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
    });
</script>

</body>
</html> 