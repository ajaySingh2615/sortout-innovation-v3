<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage jobs.";
    exit();
}

// Get total job count
$totalJobs = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .filters-container {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th {
            background: #d90429;
            color: white;
        }
        .badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }
        .badge-verified {
            background-color: #198754;
        }
        .badge-pending {
            background-color: #ffc107;
        }
        .badge-high-demand {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            Job Management Dashboard
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../main_dashboard.php">Main_Dashboard</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Job Management</h1>
        <div>
            <a href="add-job.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Job
            </a>
            <!-- <a href="fix-category.php" class="btn btn-warning ms-2">
                <i class="fas fa-wrench"></i> Fix Category Issues
            </a>
            <a href="fix-category-type.php" class="btn btn-danger ms-2">
                <i class="fas fa-database"></i> Fix Category Type
            </a> -->
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Jobs</h5>
                        <h2 class="mb-0"><?= $totalJobs ?></h2>
                    </div>
                    <i class="fas fa-briefcase fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Verified Jobs</h5>
                        <h2 class="mb-0" id="verified-jobs-count">-</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">High Demand</h5>
                        <h2 class="mb-0" id="high-demand-count">-</h2>
                    </div>
                    <i class="fas fa-fire fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="searchInput" class="form-label">Search Jobs</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title, company...">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="jobTypeFilter" class="form-label">Job Type</label>
                <select class="form-select" id="jobTypeFilter">
                    <option value="">All Types</option>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Contract">Contract</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="verifiedFilter" class="form-label">Status</label>
                <select class="form-select" id="verifiedFilter">
                    <option value="">All</option>
                    <option value="1">Verified</option>
                    <option value="0">Pending</option>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-outline-secondary form-control" id="resetFilters">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Job Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Salary Range</th>
                    <th>Job Type</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="jobTableBody">
                <!-- Data will be loaded here via AJAX -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <!-- Pagination info will be populated by JavaScript -->
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination" id="pagination">
                <!-- Pagination will be populated by JavaScript -->
            </ul>
        </nav>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Global variables
    let currentPage = 1;
    let totalPages = 1;
    const limit = 10;
    
    // Load jobs on page load
    $(document).ready(function() {
        loadJobs(currentPage, limit);
        
        // Set up search functionality
        $('#searchInput').on('keyup', function(e) {
            if(e.key === 'Enter') {
                loadJobs(1, limit);
            }
        });
        
        // Search button click
        $('#searchButton').on('click', function() {
            loadJobs(1, limit);
        });
        
        // Filter change events
        $('#jobTypeFilter, #verifiedFilter').on('change', function() {
            loadJobs(1, limit);
        });
        
        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#searchInput').val('');
            $('#jobTypeFilter').val('');
            $('#verifiedFilter').val('');
            loadJobs(1, limit);
        });
        
        // Handle toggle verification
        $(document).on('click', '.toggle-verification', function(e) {
            e.preventDefault();
            const jobId = $(this).data('id');
            const button = $(this);
            
            $.ajax({
                url: 'toggle-verification.php',
                type: 'POST',
                data: { id: jobId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update button appearance
                        if (response.newStatus) {
                            button.html('<i class="fas fa-check-circle text-success"></i>');
                            button.attr('title', 'Click to unverify');
                        } else {
                            button.html('<i class="fas fa-times-circle text-warning"></i>');
                            button.attr('title', 'Click to verify');
                        }
                        // Show success message
                        alert(response.message);
                        // Reload jobs to update counts
                        loadJobs(currentPage, limit);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error occurred while toggling verification status');
                }
            });
        });
        
        // Handle delete job
        $(document).on('click', '.delete-job', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this job?')) {
                const jobId = $(this).data('id');
                
                $.ajax({
                    url: 'delete-job.php',
                    type: 'POST',
                    data: { id: jobId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            loadJobs(currentPage, limit);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error occurred while deleting the job');
                    }
                });
            }
        });
    });
    
    // Function to load jobs
    function loadJobs(page, limit) {
        const search = $('#searchInput').val();
        const jobType = $('#jobTypeFilter').val();
        const verified = $('#verifiedFilter').val();
        
        $.ajax({
            url: 'fetch-jobs.php',
            type: 'GET',
            data: {
                page: page,
                limit: limit,
                search: search,
                job_type: jobType,
                verified: verified
            },
            dataType: 'json',
            success: function(response) {
                displayJobs(response.jobs);
                updatePagination(response.pagination);
                updateStats(response.stats);
            },
            error: function() {
                alert('Error occurred while fetching jobs');
            }
        });
    }
    
    // Function to display jobs
    function displayJobs(jobs) {
        let html = '';
        
        jobs.forEach(job => {
            const verificationIcon = job.is_verified ? 
                '<i class="fas fa-check-circle text-success"></i>' : 
                '<i class="fas fa-times-circle text-warning"></i>';
            
            const verificationTitle = job.is_verified ? 
                'Click to unverify' : 
                'Click to verify';
            
            html += `
                <tr>
                    <td>${job.id}</td>
                    <td>${job.title}</td>
                    <td>${job.company_name}</td>
                    <td>${job.location}</td>
                    <td>₹${job.min_salary} - ₹${job.max_salary}</td>
                    <td>${job.job_type}</td>
                    <td>
                        <span class="badge ${job.is_verified ? 'badge-verified' : 'badge-pending'}">
                            ${job.is_verified ? 'Verified' : 'Pending'}
                        </span>
                        ${job.high_demand ? '<span class="badge badge-high-demand ms-1">High Demand</span>' : ''}
                    </td>
                    <td>${job.formatted_date}</td>
                    <td class="text-center">
                        <a href="#" class="btn btn-sm btn-link toggle-verification" data-id="${job.id}" title="${verificationTitle}">
                            ${verificationIcon}
                        </a>
                        <a href="edit-job.php?id=${job.id}" class="btn btn-sm btn-link" title="Edit">
                            <i class="fas fa-edit text-primary"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-link delete-job" data-id="${job.id}" title="Delete">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
        
        $('#jobTableBody').html(html);
    }
    
    // Function to update pagination
    function updatePagination(pagination) {
        totalPages = pagination.total_pages;
        currentPage = pagination.current_page;
        
        let html = '';
        
        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `;
        
        $('#pagination').html(html);
        
        // Update pagination info
        const start = ((currentPage - 1) * pagination.limit) + 1;
        const end = Math.min(start + pagination.limit - 1, pagination.total_rows);
        $('.pagination-info').html(`Showing ${start} to ${end} of ${pagination.total_rows} entries`);
        
        // Add click handlers for pagination
        $('.page-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page >= 1 && page <= totalPages) {
                loadJobs(page, limit);
            }
        });
    }
    
    // Function to update statistics
    function updateStats(stats) {
        $('#verified-jobs-count').text(stats.verified);
        $('#high-demand-count').text(stats.high_demand);
    }
</script>

</body>
</html> 