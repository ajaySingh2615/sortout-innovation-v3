<?php
$currentPage = 'blog';
require_once '../includes/db_connect.php';

// Pagination setup
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 6; // Number of blogs per page
$offset = ($page - 1) * $limit;

// Get category filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Get search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base query
$query = "SELECT * FROM blogs WHERE 1=1";
$countQuery = "SELECT COUNT(*) as total FROM blogs WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR excerpt LIKE '%$search%')";
    $countQuery .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR excerpt LIKE '%$search%')";
}

// Add category filter
if (!empty($category_filter)) {
    $category_filter = $conn->real_escape_string($category_filter);
    $query .= " AND categories LIKE '%$category_filter%'";
    $countQuery .= " AND categories LIKE '%$category_filter%'";
}

// Order by creation date (newest first)
$query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

// Execute queries
$result = $conn->query($query);
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Get all unique categories for filter
$categoriesQuery = "SELECT DISTINCT categories FROM blogs";
$categoriesResult = $conn->query($categoriesQuery);
$allCategories = [];

if ($categoriesResult) {
    while ($row = $categoriesResult->fetch_assoc()) {
        if (!empty($row['categories'])) {
            $cats = json_decode($row['categories'], true);
            if (is_array($cats)) {
                foreach ($cats as $cat) {
                    if (!in_array($cat, $allCategories)) {
                        $allCategories[] = $cat;
                    }
                }
            }
        }
    }
}
sort($allCategories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>Sortout Innovation - Blog</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Reset and basic styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        a{
            text-decoration: none;
        }
        
        :root {
            --primary-color: #d90429;
            --secondary-color: #ef233c;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --text-color: #333333;
            --gray-color: #6c757d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            color: var(--text-color);
            /* line-height: 1.6; */
        }
        
        
        /* Header/Hero Section */
        .blog-header {
            background: url('/images/blog/hero-background.webp') no-repeat center center;
            background-size: cover;
            padding: 120px 0 100px;
            position: relative;
            margin-bottom: 0;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            /* Removing margin-top as it's handled by navbar's body padding */
        }
        
        .blog-header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        
        .blog-header .container {
            position: relative;
            z-index: 2;
        }
        
        .blog-header h1 {
            color: white;
            font-weight: 800;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #ff3e3e, #d70000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            text-shadow: none;
            font-style: italic;
        }
        
        .text-danger {
            color: var(--primary-color) !important;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .blog-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.25rem;
            max-width: 650px;
            margin-bottom: 2.5rem;
            line-height: 1.7;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
        }
        
        .header-cta {
            display: inline-flex;
            align-items: center;
            background-color: white;
            color: var(--primary-color);
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .header-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            color: var(--secondary-color);
        }
        
        .header-cta i {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }
        
        .header-cta:hover i {
            transform: translateX(3px);
        }
        
        /* Responsive Hero Section */
        @media (max-width: 992px) {
            .blog-header {
                padding: 100px 0 80px;
            }
            
            .blog-header h1 {
                font-size: 3rem;
            }
            
            .blog-header p {
                font-size: 1.15rem;
            }
        }
        
        @media (max-width: 768px) {
            .blog-header {
                padding: 90px 0 70px;
            }
            
            .blog-header h1 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }
            
            .blog-header p {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }
            
            .header-cta {
                padding: 10px 22px;
            }
        }
        
        @media (max-width: 576px) {
            .blog-header {
                padding: 80px 0 60px;
            }
            
            .blog-header h1 {
                font-size: 2.2rem;
            }
            
            .blog-header p {
                font-size: 1rem;
            }
        }
        
        /* Search and Filter Section */
        .search-filter-section {
            background-color: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 30px 0;
            margin-bottom: 50px;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 0;
        }
        
        .search-input {
            border: none;
            background-color: #f5f5f5;
            padding: 15px 20px;
            border-radius: 5px;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 2px var(--primary-color);
            outline: none;
        }
        
        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-color);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            color: var(--primary-color);
        }
        
        .filter-dropdown {
            border: none;
            background-color: #f5f5f5;
            padding: 15px 20px;
            border-radius: 5px;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .filter-dropdown:focus {
            background-color: #fff;
            box-shadow: 0 0 0 2px var(--primary-color);
            outline: none;
        }
        
        /* Blog Posts Section */
        .blog-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .blog-list {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }
        
        @media (min-width: 576px) {
            .blog-list {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
        
        @media (min-width: 768px) {
            .blog-list {
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
        }
        
        @media (min-width: 992px) {
            .blog-list {
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
            }
        }
        
        /* Blog Card */
        .blog-card {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .blog-img-container {
            width: 100%;
            position: relative;
            overflow: hidden;
            aspect-ratio: 1/1;
            background-color: #f5f5f5;
        }
        
        .blog-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        /* Add responsive adjustments for different screen sizes */
        @media (max-width: 767px) {
            .blog-img-container {
                aspect-ratio: 1/1; /* Keep square on mobile */
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .blog-img-container {
                aspect-ratio: 1/1; /* Keep square on tablets */
            }
        }

        @media (min-width: 1024px) {
            .blog-img-container {
                aspect-ratio: 1/1; /* Keep square on desktop */
            }
        }
        
        .blog-card:hover .blog-img {
            transform: scale(1.05);
        }
        
        .blog-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 20px;
            z-index: 2;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .blog-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            background-color: #fff;
        }
        
        .blog-date {
            font-size: 0.85rem;
            color: var(--gray-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .blog-date i {
            margin-right: 5px;
        }
        
        .blog-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.4;
            color: var(--dark-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .blog-excerpt {
            font-size: 0.9rem;
            color: var(--gray-color);
            margin-bottom: 15px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }
        
        .read-more {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            margin-top: auto;
            transition: color 0.3s;
        }
        
        .read-more:hover {
            color: var(--secondary-color);
        }
        
        .read-more i {
            margin-left: 5px;
            transition: transform 0.3s;
        }
        
        .read-more:hover i {
            transform: translateX(3px);
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-bottom: 50px;
        }
        
        .pagination .page-item .page-link {
            color: var(--text-color);
            border: none;
            margin: 0 5px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }
        
        .pagination .page-item .page-link:hover:not(.active) {
            background-color: #f5f5f5;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #dee2e6;
        }
        
        /* No results */
        .no-results {
            text-align: center;
            padding: 80px 0;
        }
        
        .no-results i {
            font-size: 3rem;
            color: #e9ecef;
            margin-bottom: 20px;
        }
        
        .no-results h3 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .no-results p {
            color: var(--gray-color);
            max-width: 500px;
            margin: 0 auto 20px;
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Footer Section */
        .footer-section {
            background: #0a0a0a;
            color: #fff;
            padding: 60px 20px;
            font-family: Arial, sans-serif;
        }
        
        .footer-section h4 {
            color: #ff4b4b;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        
        /* Footer Links */
        .footer-links {
            list-style: none;
            padding: 0;
            font-size: 1rem;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links li a {
            text-decoration: none;
            color: #ddd;
            transition: color 0.3s ease;
        }
        
        .footer-links li a:hover {
            color: #ff4b4b;
        }
        
        /* Social Icons */
        .social-icons {
            display: flex;
            gap: 10px;
        }
        
        .social-icons a {
            font-size: 1.5rem;
            color: #fff;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        
        .social-icons a:hover {
            color: #ff4b4b;
            transform: scale(1.1);
        }
        
        /* Footer Bottom */
        .footer-bottom {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-bottom p {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .footer-bottom ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }
        
        .footer-bottom ul li a {
            text-decoration: none;
            color: #ddd;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .footer-bottom ul li a:hover {
            color: #ff4b4b;
        }
        
        .footer-logo img {
            padding-bottom: 15px;
            width: 180px; /* Adjust width as needed */
            height: auto; /* Maintains aspect ratio */
            max-width: 100%; /* Ensures responsiveness */
            display: block;
            margin: 0 auto; /* Centers the logo */
        }
        
        @media (max-width: 768px) {
            .footer-logo img {
                width: 140px; /* Smaller size for mobile */
            }
        }
        
        /* Responsive Design */
        @media (max-width: 991px) {
            .footer-section .row {
                flex-direction: column;
                text-align: center;
            }
        
            .social-icons {
                justify-content: center;
            }
        
            .footer-bottom ul {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/navbar/navbar.php'; ?>

    <!-- Header -->
    <header class="blog-header">
        <div class="blog-header-overlay"></div>
        <div class="container position-relative">
            <div class="row">
                <div class="col-lg-8">
                Stay Informed. Stay Inspired.
                    <h1><span class="text-gradient">Stay</span> Informed. <span class="text-danger">Stay Inspired.</span></h1>
                    <p>Discover content that resonates. From industry trends and how-to guides to thought-provoking stories — our blog is your gateway to knowledge and inspiration.</p>
                    <a href="#search-section" class="header-cta">
                        Explore Articles <i class="fas fa-chevron-down"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Search and Filter -->
    <div id="search-section" class="search-filter-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <form action="index.php" method="GET" class="search-box">
                        <input type="text" name="search" class="search-input" placeholder="Search articles..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
    </div>
                <div class="col-md-4">
                    <form action="index.php" method="GET" id="categoryForm">
                        <select name="category" class="filter-dropdown" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach($allCategories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category_filter == $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
    </div>
    </div>
        </div>
    </div>

    <!-- Blog Posts -->
    <div class="blog-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php 
                // Reset data pointer to beginning
                $result->data_seek(0);
                
                // Count total blogs
                $total_blogs = $result->num_rows;
                $blog_count = 0;
                
                // Array to store blogs
                $blogs = [];
                while($blog = $result->fetch_assoc()) {
                    $blogs[] = $blog;
                }
            ?>
            
            <div class="blog-list">
                <?php foreach($blogs as $blog): ?>
                    <?php 
                        // Parse categories
                        $categories = [];
                        if (!empty($blog['categories'])) {
                            if (strpos($blog['categories'], '[') === 0) {
                                // JSON format
                                $categories = json_decode($blog['categories'], true) ?: [];
                            } else {
                                // Comma-separated format
                                $categories = explode(',', $blog['categories']);
                            }
                        }
                        
                        // Format date
                        $date = new DateTime($blog['created_at']);
                        $formatted_date = $date->format('M d, Y');
                        
                        // Generate excerpt if not present
                        $excerpt = isset($blog['excerpt']) && !empty($blog['excerpt']) 
                            ? $blog['excerpt'] 
                            : substr(strip_tags($blog['content']), 0, 150) . '...';
                    ?>
                    
                    <div class="blog-card">
                        <div class="blog-img-container">
                            <?php if(!empty($categories)): ?>
                                <div class="blog-category"><?php echo htmlspecialchars($categories[0]); ?></div>
                            <?php endif; ?>
                            
                            <?php if(!empty($blog['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-img">
                            <?php else: ?>
                                <img src="../public/images/blog-placeholder.jpg" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-img">
                            <?php endif; ?>
                    </div>
                        
                        <div class="blog-content">
                            <div class="blog-date">
                                <i class="far fa-calendar-alt me-2"></i> <?php echo $formatted_date; ?>
                </div>

                            <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                            
                            <p class="blog-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                            
                            <a href="post.php?slug=<?php echo urlencode($blog['slug']); ?>" class="read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
                <nav aria-label="Blog pagination">
                    <ul class="pagination">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($category_filter) ? '&category='.urlencode($category_filter) : ''; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for($i = max(1, $page-2); $i <= min($page+2, $totalPages); $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($category_filter) ? '&category='.urlencode($category_filter) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($category_filter) ? '&category='.urlencode($category_filter) : ''; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No Blog Posts Found</h3>
                <p>We couldn't find any blog posts matching your criteria. Try adjusting your search or browse all articles.</p>
                <a href="index.php" class="btn btn-outline-primary">View All Articles</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
