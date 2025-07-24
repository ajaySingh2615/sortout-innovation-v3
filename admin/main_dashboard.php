<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// Ensure Only Admins & Super Admins Can Access
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch Pending Admins (Only for Super Admin)
$pendingAdmins = [];
if ($_SESSION['role'] === 'super_admin') {
    $adminQuery = "SELECT id, username, email FROM users WHERE role = 'admin' AND status = 'pending'";
    $pendingAdmins = $conn->query($adminQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />

    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            Admin Dashboard
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold px-3" href="#" role="button" data-bs-toggle="dropdown">
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

<!-- Spacing for Navbar -->
<div style="height: 80px;"></div>

<!-- Super Admin Panel (Approve Admins) -->
<?php if ($_SESSION['role'] === 'super_admin' && $pendingAdmins->num_rows > 0): ?>
    <div class="container my-4">
        <h3 class="text-danger fw-bold">Pending Admin Approvals</h3>
        <table class="table table-bordered bg-white rounded shadow-sm">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($admin = $pendingAdmins->fetch_assoc()): ?>
                <tr id="admin-<?= $admin['id']; ?>">
                    <td><?= htmlspecialchars($admin['username']); ?></td>
                    <td><?= htmlspecialchars($admin['email']); ?></td>
                    <td>
                        <button class="btn btn-approve approve-btn" data-id="<?= $admin['id']; ?>">Approve</button>
                        <button class="btn btn-reject reject-btn" data-id="<?= $admin['id']; ?>">Reject</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
<?php endif; ?>

<!-- Main Dashboard Content -->
<div class="container my-5">
    <h2 class="mb-4">Management Sections</h2>
    
    <div class="row">
        <!-- Blog Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary-subtle">
                            <i class="fas fa-blog text-primary"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Blog Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage blog posts, categories, and SEO settings.</p>
                    <a href="blog_dashboard.php" class="btn btn-outline-primary mt-auto">Manage Blogs</a>
                </div>
            </div>
        </div>

        <!-- Job Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success-subtle">
                            <i class="fas fa-briefcase text-success"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Job Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage job postings, applications, and employment opportunities.</p>
                    <a href="job/job-dashboard.php" class="btn btn-outline-success mt-auto">Manage Jobs</a>
                </div>
            </div>
        </div>

        <!-- Device Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info-subtle">
                            <i class="fas fa-mobile-alt text-info"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Device Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage devices, inventory, and device assignments.</p>
                    <a href="device_dashboard.php" class="btn btn-outline-info mt-auto">Manage Devices</a>
                </div>
            </div>
        </div>

        <!-- Talent Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning-subtle">
                            <i class="fas fa-users text-warning"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Talent Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage talent profiles, portfolios, and bookings.</p>
                    <a href="registrations_dashboard.php" class="btn btn-outline-warning mt-auto">Manage Talents</a>
                </div>
            </div>
        </div>

        <!-- Artist Job Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger-subtle">
                            <i class="fas fa-palette text-danger"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">Artist Job Management</h5>
                    </div>
                    <p class="card-text text-muted">Manage artist categories, apps, and job opportunities.</p>
                    <a href="artist-dashboard/artist_dashboard.php" class="btn btn-outline-danger mt-auto">Manage Artist Jobs</a>
                </div>
            </div>
        </div>
        
        <!-- App Directory Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary-subtle">
                            <i class="fas fa-mobile-alt text-primary"></i>
                        </div>
                        <h5 class="card-title ms-3 mb-0">App Directory</h5>
                    </div>
                    <p class="card-text text-muted">Manage app categories and applications for the app directory.</p>
                    <a href="artist-v2/artist_dashboard.php" class="btn btn-outline-primary mt-auto">Manage App Directory</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Handle Admin Approval
    $('.approve-btn').on('click', function() {
        const adminId = $(this).data('id');
        const row = $(`#admin-${adminId}`);
        
        if (confirm('Are you sure you want to approve this admin?')) {
            $.ajax({
                url: 'approve_admin.php',
                type: 'POST',
                data: { id: adminId },
                success: function(response) {
                    alert(response);
                    if (response.includes('✅')) {
                        row.fadeOut(400, function() {
                            $(this).remove();
                            if ($('.approve-btn').length === 0) {
                                $('.container:has(h3:contains("Pending Admin Approvals"))').remove();
                            }
                        });
                    }
                },
                error: function() {
                    alert('Error occurred while approving admin');
                }
            });
        }
    });

    // Handle Admin Rejection
    $('.reject-btn').on('click', function() {
        const adminId = $(this).data('id');
        const row = $(`#admin-${adminId}`);
        
        if (confirm('Are you sure you want to reject this admin?')) {
            $.ajax({
                url: 'reject_admin.php',
                type: 'POST',
                data: { id: adminId },
                success: function(response) {
                    alert(response);
                    if (response.includes('❌')) {
                        row.fadeOut(400, function() {
                            $(this).remove();
                            if ($('.reject-btn').length === 0) {
                                $('.container:has(h3:contains("Pending Admin Approvals"))').remove();
                            }
                        });
                    }
                },
                error: function() {
                    alert('Error occurred while rejecting admin');
                }
            });
        }
    });
});
</script>

</body>
</html> 