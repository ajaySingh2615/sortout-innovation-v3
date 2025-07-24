<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can perform this operation.";
    exit();
}

$updateCount = 0;
$errorMsg = "";
$successMsg = "";

// Process fix request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fix_categories'])) {
    // Update all jobs where category is '0' to empty string
    $stmt = $conn->prepare("UPDATE jobs SET category = '' WHERE category = '0'");
    
    if ($stmt->execute()) {
        $updateCount = $stmt->affected_rows;
        $successMsg = "Successfully updated $updateCount job records with '0' category to empty string.";
    } else {
        $errorMsg = "Error updating category values: " . $stmt->error;
    }
    
    $stmt->close();
}

// Count records with '0' category
$countStmt = $conn->prepare("SELECT COUNT(*) as count FROM jobs WHERE category = '0'");
$countStmt->execute();
$result = $countStmt->get_result();
$row = $result->fetch_assoc();
$zeroCount = $row['count'];
$countStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Job Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            Fix Job Categories
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="job-dashboard.php">Job Dashboard</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../main_dashboard.php">Admin Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold px-3" href="#" role="button" data-bs-toggle="dropdown">
                        👤 <?= $_SESSION['username']; ?> (<?= ucfirst($_SESSION['role']); ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Spacing for Navbar -->
<div style="height: 80px;"></div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-container">
                <h2 class="mb-4">Fix Job Categories</h2>
                
                <!-- Display error or success messages -->
                <?php if ($errorMsg): ?>
                    <div class="alert alert-danger"><?= $errorMsg ?></div>
                <?php endif; ?>
                
                <?php if ($successMsg): ?>
                    <div class="alert alert-success"><?= $successMsg ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Category Fix Tool</h5>
                        <p class="card-text">
                            This tool will update all job records where the category is stored as '0' to an empty string.
                            This fixes the issue where null categories were incorrectly stored as '0' in the database.
                        </p>
                        <p class="card-text">
                            <strong>Number of jobs with '0' category value: <?= $zeroCount ?></strong>
                        </p>
                        
                        <?php if ($zeroCount > 0): ?>
                            <form method="POST" action="">
                                <button type="submit" name="fix_categories" class="btn btn-primary">Fix Categories</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success">No jobs with '0' category found. Everything looks good!</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="job-dashboard.php" class="btn btn-secondary">Back to Job Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html> 