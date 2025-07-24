<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Admin Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn {
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            overflow: hidden;
        }
        .btn-close-white {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top bg-white">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            <i class="fas fa-tachometer-alt me-2"></i> Admin Panel
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="<?php echo str_replace('/includes', '', dirname($_SERVER['PHP_SELF'])); ?>/admin/main_dashboard.php">Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold px-3" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> <?= isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?> 
                        (<?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Role'; ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo str_replace('/includes', '', dirname($_SERVER['PHP_SELF'])); ?>/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Spacing for fixed navbar -->
<div style="height: 70px;"></div>

<!-- Main Content Container --> 