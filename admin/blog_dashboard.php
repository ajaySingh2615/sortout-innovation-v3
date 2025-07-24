<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// ✅ Ensure Only Admins & Super Admins Can Access
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header("Location: ../index.php");
    exit();
}

// Define categories
$categories = [
    'Digital Marketing',
    'Live Streaming Service',
    'Find Talent',
    'Information Technology',
    'Chartered Accountant',
    'Human Resources',
    'Courier',
    'Shipping and Fulfillment',
    'Stationery',
    'Real Estate and Property',
    'Event Management',
    'Design and Creative',
    'Corporate Insurance',
    'Business Strategy',
    'Innovation',
    'Industry News',
    'Marketing and Sales',
    'Finance and Investment',
    'Legal Services',
    'Healthcare Services'
];
sort($categories);

// ✅ Fetch Pending Admins (Only for Super Admin)
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
    <title>Blog Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .blog-table th, .blog-table td {
            vertical-align: middle;
        }
        .category-badge {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            margin: 0.1rem;
            border-radius: 0.25rem;
        }
        .filters-container {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        /* ✅ Dashboard Styling */
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(230,230,230,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }

        /* ✅ Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #d90429;
            color: white;
        }
        tr:hover {
            background: #f8d7da;
        }

        /* ✅ Category Tags */
        .category-tag {
            display: inline-block;
            padding: 2px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 0.85em;
            background: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }

         /* ✅ Buttons */
         .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }

        /* ✅ Filters */
        .filter-box {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filter-box input, .filter-box select {
            max-width: 250px;
            flex-grow: 1;
        }

        /* ✅ Buttons */
        .btn-edit {
            background: #ffc107;
            color: black;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.85;
        }

        /* ✅ Pagination */
        .pagination-container {
            background: #222;
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
        }
        .pagination-btn {
            background: #555;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .pagination-btn:hover {
            background: #777;
        }

        /* ✅ Footer */
        footer {
            background: black;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-danger" href="#">
            <!-- <img src="../public/logo.png" alt="Logo" height="40" class="me-2"> -->
            Admin Panel
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <!-- <li class="nav-item"><a class="nav-link fw-semibold px-3" href="add_blog.php">Add Blog</a></li> -->
                <!-- <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../blog/index.php">Blogs</a></li> -->
                <!-- <li class="nav-item"><a class="nav-link fw-semibold px-3" href="../pages/our-services-page/service.html">Services</a></li> -->
                <li class="nav-item"><a class="nav-link fw-semibold px-3" href="main_dashboard.php">Main_dashboard</a></li>
                <!-- <li class="nav-item"><a class="nav-link fw-semibold px-3" href="model_agency_dashboard.php">Manage Talents</a></li> -->
                <!-- ✅ Display Logged-in User Info -->
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

<!-- ✅ Spacing for Navbar -->
<div style="height: 80px;"></div>

<!-- ✅ Super Admin Panel (Approve Admins) -->
<?php if ($_SESSION['role'] === 'super_admin' && $pendingAdmins->num_rows > 0): ?>
    <div class="container my-4">
        <h3 class="text-danger fw-bold">Pending Admin Approvals</h3>
        <table class="table table-bordered">
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

<!-- ✅ Dashboard Content -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Blog Management</h1>
        <a href="add_blog.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Blog
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="row">
            <div class="col-md-5 mb-3">
                <label for="searchInput" class="form-label">Search Blogs</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title, content...">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-5 mb-3">
                <label for="categoryFilter" class="form-label">Filter by Category</label>
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                    <?php endforeach; ?>
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

    <!-- Blog Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover blog-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>SEO Title</th>
                            <th>Categories</th>
                            <th>SEO Score</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="blogTableBody">
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
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

<!-- ✅ Footer -->
<footer>© 2025 Admin Panel | All Rights Reserved.</footer>

<!-- ✅ Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Global variables
    let currentPage = 1;
    let totalPages = 1;
    const limit = 10;
    
    // Load blogs on page load
    $(document).ready(function() {
        loadBlogs(currentPage, limit);
        
        // Set up search functionality
        $('#searchInput').on('keyup', function(e) {
            if(e.key === 'Enter') {
                loadBlogs(1, limit);
            }
        });
        
        // Search button click
        $('#searchButton').on('click', function() {
            loadBlogs(1, limit);
        });
        
        // Set up category filter
        $('#categoryFilter').on('change', function() {
            loadBlogs(1, limit);
        });
        
        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#searchInput').val('');
            $('#categoryFilter').val('');
            loadBlogs(1, limit);
        });

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
                            // Remove the row on success
                            row.fadeOut(400, function() {
                                $(this).remove();
                                // If no more pending admins, remove the entire section
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
                            // Remove the row on success
                            row.fadeOut(400, function() {
                                $(this).remove();
                                // If no more pending admins, remove the entire section
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
    
    // Function to load blogs
    function loadBlogs(page, limit) {
        currentPage = page;
        const search = $('#searchInput').val();
        const category = $('#categoryFilter').val();
        
        // Show loading indicator
        $('#blogTableBody').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        
        // Fetch blogs data
                $.ajax({
            url: 'fetch_blogs.php',
            type: 'GET',
            data: { 
                page: page, 
                limit: limit,
                search: search,
                category: category
            },
            dataType: 'json',
            success: function(data) {
                // Update table with blog data
                let html = '';
                
                if (data.blogs && data.blogs.length > 0) {
                    data.blogs.forEach(blog => {
                        const categories = blog.categories.map(cat => 
                            `<span class="category-badge">${cat}</span>`
                        ).join('');

                        // Calculate SEO score
                        let seoScore = 100;
                        
                        // Check title length (20-70 chars)
                        if (!blog.seo_title || blog.seo_title.length < 20 || blog.seo_title.length > 70) {
                            seoScore -= 20;
                        }
                        
                        // Check meta description (120-160 chars)
                        if (!blog.meta_description || blog.meta_description.length < 120 || blog.meta_description.length > 160) {
                            seoScore -= 20;
                        }
                        
                        // Check focus keyword presence
                        if (!blog.focus_keyword) {
                            seoScore -= 20;
                        }
                        
                        // Check slug
                        if (!blog.slug) {
                            seoScore -= 20;
                        }
                        
                        // Determine score class
                        let scoreClass = seoScore >= 80 ? 'text-success' : (seoScore >= 50 ? 'text-warning' : 'text-danger');

                        html += `
                            <tr>
                                <td>${blog.id}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        ${blog.image_url ? `<img src="${blog.image_url}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : ''}
                                        <div>
                                            <div class="fw-bold">${blog.title}</div>
                                            <div class="small text-muted">${blog.excerpt || ''}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${blog.seo_title || '<span class="text-muted">Same as title</span>'}</td>
                                <td>${categories}</td>
                                <td><span class="${scoreClass}">${seoScore}%</span></td>
                                <td>${blog.formatted_date}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="edit_blog.php?id=${blog.id}" class="btn btn-sm btn-primary me-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBlog(${blog.id})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">No blogs found</td></tr>';
                }
                
                $('#blogTableBody').html(html);
                
                // Update pagination
                updatePagination(data.pagination);
                
                // Update pagination info
                if (data.pagination) {
                    const start = (data.pagination.current_page - 1) * data.pagination.limit + 1;
                    const end = Math.min(data.pagination.current_page * data.pagination.limit, data.pagination.total_rows);
                    $('.pagination-info').html(`Showing ${start} to ${end} of ${data.pagination.total_rows} entries`);
                }
            },
            error: function(xhr, status, error) {
                $('#blogTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading blogs</td></tr>');
            }
        });
    }
    
    // Function to update pagination
    function updatePagination(pagination) {
        if (!pagination) {
            console.error('Pagination data is missing');
            return;
        }
        
        console.log('Pagination data:', pagination); // Debug log
        
        const totalPages = parseInt(pagination.total_pages) || 1;
        const currentPage = parseInt(pagination.current_page) || 1;
        
        let paginationHtml = '';
        
        // Previous button
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="${currentPage > 1 ? 'loadBlogs(' + (currentPage - 1) + ', ' + limit + ')' : ''}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const maxPages = 5; // Maximum number of page links to show
        let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="loadBlogs(${i}, ${limit})">${i}</a>
                </li>
            `;
        }
        
        // Next button
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="${currentPage < totalPages ? 'loadBlogs(' + (currentPage + 1) + ', ' + limit + ')' : ''}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        $('#pagination').html(paginationHtml);
    }
    
    // Function to delete blog
    function deleteBlog(blogId) {
        if (confirm('Are you sure you want to delete this blog post?')) {
            $.ajax({
                url: 'delete_blog.php',
                type: 'POST',
                data: { id: blogId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Blog post deleted successfully!');
                        loadBlogs(currentPage, limit);
                    } else {
                        alert('Failed to delete blog post: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete request failed:', status, error);
                    let errorMessage = 'An error occurred while deleting the blog post.';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.error) {
                            errorMessage += ' ' + response.error;
                        }
                    } catch (e) {
                        // If response is not valid JSON, use the status text
                        if (xhr.statusText) {
                            errorMessage += ' ' + xhr.statusText;
                        }
                    }
                    
                    alert(errorMessage);
                }
            });
        }
    }
</script>

</body>
</html>
