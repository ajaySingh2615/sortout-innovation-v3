<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can perform this operation.";
    exit();
}

$errorMsg = "";
$successMsg = "";

// Process fix request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fix_category_type'])) {
    // First, set all '0' categories to empty string
    $stmt = $conn->prepare("UPDATE jobs SET category = '' WHERE category = '0'");
    
    if ($stmt->execute()) {
        $updateCount = $stmt->affected_rows;
        $successMsg = "Successfully updated $updateCount job records with '0' category to empty string.<br>";
    } else {
        $errorMsg = "Error updating category values: " . $stmt->error;
    }
    
    $stmt->close();
    
    // Then, verify the data type of the category column
    try {
        // Get information about the category column
        $result = $conn->query("SHOW COLUMNS FROM jobs WHERE Field = 'category'");
        $column = $result->fetch_assoc();
        
        if ($column) {
            $currentType = $column['Type'];
            $successMsg .= "Current category column type: $currentType<br>";
            
            // If not a varchar(100), modify the column
            if ($currentType !== 'varchar(100)') {
                $alterStmt = $conn->prepare("ALTER TABLE jobs MODIFY COLUMN category varchar(100) DEFAULT NULL");
                if ($alterStmt->execute()) {
                    $successMsg .= "Successfully modified category column to varchar(100) DEFAULT NULL<br>";
                } else {
                    $errorMsg .= "Error modifying category column: " . $alterStmt->error;
                }
                $alterStmt->close();
            }
        } else {
            $errorMsg = "Could not retrieve column information for 'category'";
        }
    } catch (Exception $e) {
        $errorMsg = "Error checking column type: " . $e->getMessage();
    }
}

// Count records with '0' category
$countStmt = $conn->prepare("SELECT COUNT(*) as count FROM jobs WHERE category = '0'");
$countStmt->execute();
$result = $countStmt->get_result();
$row = $result->fetch_assoc();
$zeroCount = $row['count'];
$countStmt->close();

// Check current column type
$columnType = "Unknown";
try {
    $result = $conn->query("SHOW COLUMNS FROM jobs WHERE Field = 'category'");
    if ($column = $result->fetch_assoc()) {
        $columnType = $column['Type'];
    }
} catch (Exception $e) {
    // Silently fail, we'll show Unknown
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Category Data Type</title>
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
            Fix Category Data Type
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="job-dashboard.php">Job Dashboard</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../dashboard.php">Admin Dashboard</a></li>
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
                <h2 class="mb-4">Fix Category Data Type</h2>
                
                <!-- Display error or success messages -->
                <?php if ($errorMsg): ?>
                    <div class="alert alert-danger"><?= $errorMsg ?></div>
                <?php endif; ?>
                
                <?php if ($successMsg): ?>
                    <div class="alert alert-success"><?= $successMsg ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Database Schema Analysis</h5>
                        <p class="card-text">
                            This tool will check and fix issues with the category column in the jobs table.
                        </p>
                        
                        <div class="alert alert-info">
                            <strong>Current settings:</strong><br>
                            Category column type: <code><?= htmlspecialchars($columnType) ?></code><br>
                            Number of jobs with '0' as category value: <strong><?= $zeroCount ?></strong>
                        </div>
                        
                        <form method="POST" action="">
                            <button type="submit" name="fix_category_type" class="btn btn-primary">Fix Category Issues</button>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">How This Fix Works</h5>
                        <ol>
                            <li>Updates all jobs with '0' as category to empty string</li>
                            <li>Verifies that the category column is properly defined as varchar(100)</li>
                            <li>If needed, alters the column definition to ensure it works properly</li>
                        </ol>
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