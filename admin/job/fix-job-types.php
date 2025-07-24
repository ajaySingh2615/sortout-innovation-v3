<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can perform this operation.";
    exit();
}

// Check if the form was submitted to update job types
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
    
    // Update all jobs with empty or 0 job_type to 'Full Time'
    $updateStmt = $conn->prepare("UPDATE jobs SET job_type = 'Full Time' WHERE job_type = '' OR job_type = '0' OR job_type IS NULL");
    
    if ($updateStmt->execute()) {
        $affectedRows = $updateStmt->affected_rows;
        $successMessage = "Successfully updated $affectedRows job(s) with missing job types.";
    } else {
        $errorMessage = "Error updating jobs: " . $updateStmt->error;
    }
    
    $updateStmt->close();
}

// Count jobs with missing job types
$countStmt = $conn->prepare("SELECT COUNT(*) as count FROM jobs WHERE job_type = '' OR job_type = '0' OR job_type IS NULL");
$countStmt->execute();
$result = $countStmt->get_result();
$jobsToFix = $result->fetch_assoc()['count'];
$countStmt->close();

// Get all jobs to display their current job types
$jobsStmt = $conn->prepare("SELECT id, title, job_type FROM jobs ORDER BY id");
$jobsStmt->execute();
$result = $jobsStmt->get_result();
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$jobsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Job Types</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            Fix Job Types
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="job-dashboard.php">Job Dashboard</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../dashboard.php">Admin Dashboard</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">Fix Job Types</h2>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Jobs with Missing Job Types</h5>
            <p>There are <strong><?= $jobsToFix ?></strong> job(s) with missing job types.</p>
            
            <?php if ($jobsToFix > 0): ?>
                <form method="POST" action="">
                    <p>Click the button below to update all jobs with missing job types to 'Full Time':</p>
                    <button type="submit" name="confirm_update" class="btn btn-primary">Update Job Types</button>
                </form>
            <?php else: ?>
                <div class="alert alert-success">All jobs have a job type assigned!</div>
            <?php endif; ?>
        </div>
    </div>
    
    <h4>Current Job Types</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Job Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?= $job['id'] ?></td>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['job_type']) ?></td>
                    <td>
                        <?php if (empty($job['job_type']) || $job['job_type'] === '0'): ?>
                            <span class="badge bg-danger">Missing</span>
                        <?php else: ?>
                            <span class="badge bg-success">OK</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <a href="job-dashboard.php" class="btn btn-secondary">Back to Job Dashboard</a>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html> 