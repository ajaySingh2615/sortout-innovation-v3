<?php
// Include necessary files
include_once 'includes/functions.php';

require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage apps and categories.";
    exit();
}

// Determine the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define valid pages and their files
$validPages = [
    'dashboard' => 'pages/dashboard.php',
    'categories' => 'pages/categories.php',
    'category-form' => 'pages/category_form.php',
    'apps' => 'pages/apps.php',
    'app-form' => 'pages/app_form.php',
    'setup' => 'pages/setup.php'
];

// Make sure the requested page is valid
if (!array_key_exists($page, $validPages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard v2</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .frontend-link {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-paint-brush me-2"></i> Artist Dashboard</h3>
            <p class="text-muted">Version 2.0</p>
        </div>
        
        <ul class="list-unstyled">
            <li>
                <a href="?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="?page=categories" class="<?php echo $page === 'categories' || $page === 'category-form' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
            <li>
                <a href="?page=apps" class="<?php echo $page === 'apps' || $page === 'app-form' ? 'active' : ''; ?>">
                    <i class="fas fa-mobile-alt"></i> Apps
                </a>
            </li>
            <li>
                <a href="?page=setup" class="<?php echo $page === 'setup' ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i> Setup
                </a>
            </li>
            <li>
                <a href="talent-registrations/dashboard.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'talent-registrations') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> <strong>Talent Registrations</strong>
                </a>
            </li>
            <li class="mt-4 border-top pt-3">
                <a href="public/" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Frontend
                </a>
            </li>
            <li>
                <a href="../main_dashboard.php">
                    <i class="fas fa-arrow-left"></i> Back to Main Dashboard
                </a>
            </li>
            <li>
                <a href="../../auth/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout (<?= $_SESSION['username']; ?>)
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div id="content">
        <?php 
        // Include the selected page
        if (file_exists($validPages[$page])) {
            include $validPages[$page];
        } else {
            echo '<div class="alert alert-danger m-4">Page not found</div>';
        }
        ?>
    </div>
    
    <!-- Frontend link button -->
    <a href="public/" target="_blank" class="btn btn-primary rounded-circle frontend-link" title="View Frontend">
        <i class="fas fa-eye"></i>
    </a>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</body>
</html> 