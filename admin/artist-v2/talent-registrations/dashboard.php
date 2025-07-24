<?php
// Start output buffering
ob_start();

// Include necessary files
include_once 'includes/functions.php';

require_once '../../../auth/auth.php';
require_once '../../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage apps and categories.";
    exit();
}

// Determine the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define valid pages and their files
$validPages = [
    'dashboard' => 'dashboard-content.php',
    'registrations' => 'registrations.php',
    'registration-detail' => 'registration-detail.php',
    'export' => 'export.php'
];

// Make sure the requested page is valid
if (!array_key_exists($page, $validPages)) {
    $page = 'dashboard';
}

// Get statistics for the dashboard
$statusCounts = getTalentRegistrationStatusCounts();
$todayCount = getTalentRegistrationCountByDateRange('today');
$weekCount = getTalentRegistrationCountByDateRange('week');
$monthCount = getTalentRegistrationCountByDateRange('month');
$categoryStats = getTalentRegistrationCountsByCategory(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Registrations Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- DateRangePicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        
        #sidebar {
            width: 280px;
            background-color: #212529;
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            overflow-y: auto;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background-color: #1a1e21;
        }
        
        #sidebar ul li a {
            padding: 15px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        #sidebar ul li a:hover {
            color: #fff;
            background-color: #2c3136;
            border-left-color: #0d6efd;
        }
        
        #sidebar ul li a.active {
            color: #fff;
            background-color: #2c3136;
            border-left-color: #0d6efd;
        }
        
        #sidebar ul li a i {
            margin-right: 10px;
        }
        
        #content {
            width: calc(100% - 280px);
            margin-left: 280px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                position: relative;
                height: auto;
                margin-bottom: 20px;
            }
            
            #content {
                width: 100%;
                margin-left: 0;
            }
        }
        
        .card-dashboard {
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s;
        }
        
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .date-filter-btn {
            cursor: pointer;
        }
        
        .filter-card {
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-users me-2"></i> Talent System</h3>
            <p class="text-muted">Registration Dashboard</p>
        </div>
        
        <ul class="list-unstyled">
            <li>
                <a href="?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="?page=registrations" class="<?php echo $page === 'registrations' || $page === 'registration-detail' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Registrations
                </a>
            </li>
            <li>
                <a href="?page=export" class="<?php echo $page === 'export' ? 'active' : ''; ?>">
                    <i class="fas fa-file-export"></i> Export Data
                </a>
            </li>
            <li class="mt-4 border-top pt-3">
                <a href="../artist_dashboard.php">
                    <i class="fas fa-arrow-left"></i> Back to Artist Dashboard
                </a>
            </li>
            <li>
                <a href="../../../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout (<?= $_SESSION['username']; ?>)
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div id="content">
        <?php 
        // Include the selected page
        if ($page === 'dashboard') {
            // Display dashboard content directly here
        ?>
            <div class="container-fluid py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 mb-0">Talent Registration Dashboard</h1>
                        <p class="text-muted">Overview of talent registrations and statistics</p>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $statusCounts['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $statusCounts['pending']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Approved Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $statusCounts['approved']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Rejected Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $statusCounts['rejected']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Time Period Stats -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-info card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Today's Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $todayCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                                <a href="?page=registrations&date_range=today" class="btn btn-sm btn-outline-info mt-3">View Details</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-info card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            This Week's Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $weekCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-week stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                                <a href="?page=registrations&date_range=week" class="btn btn-sm btn-outline-info mt-3">View Details</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-info card-dashboard h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            This Month's Registrations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $monthCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                                <a href="?page=registrations&date_range=month" class="btn btn-sm btn-outline-info mt-3">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Status Distribution Chart -->
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Registration Status Distribution</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Categories Chart -->
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Top Talent Categories</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Registrations Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Registrations</h6>
                                <a href="?page=registrations" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Category</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Get the 5 most recent registrations
                                            $recentRegistrations = getTalentRegistrations(['limit' => 5, 'offset' => 0]);
                                            
                                            if (!empty($recentRegistrations)) {
                                                foreach ($recentRegistrations as $reg) {
                                                    echo '<tr>';
                                                    echo '<td>' . $reg['id'] . '</td>';
                                                    echo '<td>' . $reg['full_name'] . '</td>';
                                                    echo '<td>' . $reg['contact_number'] . '</td>';
                                                    echo '<td>' . $reg['talent_category'] . '</td>';
                                                    echo '<td>' . date('M d, Y', strtotime($reg['created_at'])) . '</td>';
                                                    
                                                    // Status badge
                                                    $statusClass = '';
                                                    switch ($reg['status']) {
                                                        case 'pending':
                                                            $statusClass = 'bg-warning';
                                                            break;
                                                        case 'approved':
                                                            $statusClass = 'bg-success';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'bg-danger';
                                                            break;
                                                    }
                                                    
                                                    echo '<td><span class="badge ' . $statusClass . '">' . ucfirst($reg['status']) . '</span></td>';
                                                    
                                                    // Actions
                                                    echo '<td>
                                                            <a href="?page=registration-detail&id=' . $reg['id'] . '" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="7" class="text-center">No registrations found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                // Status Distribution Chart
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Approved', 'Rejected'],
                        datasets: [{
                            data: [
                                <?php echo $statusCounts['pending']; ?>,
                                <?php echo $statusCounts['approved']; ?>,
                                <?php echo $statusCounts['rejected']; ?>
                            ],
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#dc3545'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
                
                // Top Categories Chart
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                const categoryChart = new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: [
                            <?php
                            foreach ($categoryStats as $category => $count) {
                                echo "'" . $category . "',";
                            }
                            ?>
                        ],
                        datasets: [{
                            label: 'Number of Registrations',
                            data: [
                                <?php
                                foreach ($categoryStats as $count) {
                                    echo $count . ",";
                                }
                                ?>
                            ],
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            </script>
        <?php
        } else {
            if (file_exists($validPages[$page])) {
                include $validPages[$page];
            } else {
                echo '<div class="alert alert-danger m-4">Page not found</div>';
            }
        }
        ?>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Moment.js -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- DateRangePicker -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</body>
</html> 