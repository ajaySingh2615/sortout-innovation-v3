<?php
require_once '../includes/db_connect.php';

// Get blog ID or slug from URL
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$blog_slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// If we have a slug, prioritize that for fetching the blog post
if (!empty($blog_slug)) {
    // Prepare the SQL statement to fetch by slug
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ?");
    $stmt->bind_param("s", $blog_slug);
} else if ($blog_id > 0) {
    // Use ID as fallback
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $blog_id);
} else {
    // Neither ID nor slug provided
    header("Location: index.php");
    exit;
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Blog post not found
    header("Location: index.php");
    exit;
}

$blog = $result->fetch_assoc();
$blog_id = $blog['id']; // Ensure we have the ID for related queries

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
$formatted_date = $date->format('F d, Y');

// Get related posts (same category)
$related_posts = [];
if (!empty($categories)) {
    $category = $categories[0];
    $related_query = "SELECT id, title, slug, image_url, created_at FROM blogs 
                    WHERE id != ? AND categories LIKE ? 
                    ORDER BY created_at DESC LIMIT 3";
    $stmt = $conn->prepare($related_query);
    $category_param = "%$category%";
    $stmt->bind_param("is", $blog_id, $category_param);
    $stmt->execute();
    $related_result = $stmt->get_result();
    
    while ($related = $related_result->fetch_assoc()) {
        $related_posts[] = $related;
    }
}

// After getting blog data, add reading time calculation
$word_count = str_word_count(strip_tags($blog['content']));
$reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute

// Check if we need table of contents (for longer articles)
$needs_toc = ($word_count > 1000) ? true : false;

// Get previous and next posts
$prev_post = null;
$next_post = null;

// Get previous post
$prev_query = "SELECT id, title, slug FROM blogs WHERE id < ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($prev_query);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$prev_result = $stmt->get_result();
if ($prev_result->num_rows > 0) {
    $prev_post = $prev_result->fetch_assoc();
}

// Get next post
$next_query = "SELECT id, title, slug FROM blogs WHERE id > ? ORDER BY id ASC LIMIT 1";
$stmt = $conn->prepare($next_query);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$next_result = $stmt->get_result();
if ($next_result->num_rows > 0) {
    $next_post = $next_result->fetch_assoc();
}

// Get author info if available
$author = "Admin"; // Default
$author_image = "../public/images/default-avatar.png"; // Default

if (!empty($blog['created_by'])) {
    // If you have an authors table, you can fetch author details here
    // This is a placeholder for the enhanced version
    $author = $blog['created_by'];
}

// Extract headings for table of contents if needed
$headings = [];
if ($needs_toc) {
    preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h\1>/i', $blog['content'], $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $level = $match[1];
        $text = strip_tags($match[2]);
        $id = 'heading-' . count($headings);
        
        // Replace the heading in content with one that has an ID
        $blog['content'] = str_replace(
            $match[0],
            "<h{$level} id=\"{$id}\">{$match[2]}</h{$level}>",
            $blog['content']
        );
        
        $headings[] = [
            'level' => $level,
            'text' => $text,
            'id' => $id
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title><?php echo htmlspecialchars($blog['title']); ?> - Sortout Innovation</title>
    
    <!-- Meta Description -->
    <?php if(!empty($blog['meta_description'])): ?>
    <meta name="description" content="<?php echo htmlspecialchars($blog['meta_description']); ?>">
    <?php else: ?>
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($blog['content']), 0, 160)); ?>...">
    <?php endif; ?>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <?php if(!empty($blog['meta_description'])): ?>
    <meta property="og:description" content="<?php echo htmlspecialchars($blog['meta_description']); ?>">
    <?php else: ?>
    <meta property="og:description" content="<?php echo htmlspecialchars(substr(strip_tags($blog['content']), 0, 160)); ?>...">
    <?php endif; ?>
    <?php if(!empty($blog['image_url'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($blog['image_url']); ?>">
    <?php endif; ?>
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <?php if(!empty($blog['meta_description'])): ?>
    <meta property="twitter:description" content="<?php echo htmlspecialchars($blog['meta_description']); ?>">
    <?php else: ?>
    <meta property="twitter:description" content="<?php echo htmlspecialchars(substr(strip_tags($blog['content']), 0, 160)); ?>...">
    <?php endif; ?>
    <?php if(!empty($blog['image_url'])): ?>
    <meta property="twitter:image" content="<?php echo htmlspecialchars($blog['image_url']); ?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    
    <!-- Schema.org markup for Google -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?php echo htmlspecialchars($blog['title']); ?>",
        "image": "<?php echo !empty($blog['image_url']) ? htmlspecialchars($blog['image_url']) : ''; ?>",
        "datePublished": "<?php echo date('c', strtotime($blog['created_at'])); ?>",
        "dateModified": "<?php echo date('c', strtotime($blog['created_at'])); ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo htmlspecialchars($author); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Sortout Innovation",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/images/sortoutInnovation-icon/Sortout innovation.jpg'); ?>"
            }
        },
        "description": "<?php echo !empty($blog['meta_description']) ? htmlspecialchars($blog['meta_description']) : htmlspecialchars(substr(strip_tags($blog['content']), 0, 160)) . '...'; ?>",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
        }
        <?php if(!empty($categories)): ?>,
        "keywords": "<?php echo htmlspecialchars(implode(', ', $categories)); ?>"
        <?php endif; ?>
    }
    </script>
    
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
            line-height: 1.6;
        }
        
        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-logo img {
            height: 40px;
        }

        .navbar-links {
            display: flex;
        }

        .navbar-links ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar-links li {
            margin-left: 25px;
        }

        .navbar-links .nav-link {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .navbar-links .nav-link:hover,
        .navbar-links .nav-link.active {
            color: var(--primary-color);
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark-color);
            cursor: pointer;
            padding: 5px;
        }

        /* Mobile Menu */
        @media (max-width: 991px) {
            .navbar-toggle {
                display: block;
            }

            .navbar-links {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background-color: white;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
                transition: all 0.3s ease;
                display: block;
                z-index: 999;
            }

            .navbar-links.active {
                left: 0;
            }

            .navbar-links ul {
                flex-direction: column;
                width: 100%;
                gap: 15px;
                padding: 0;
                margin: 0;
            }

            .navbar-links li {
                margin: 0;
                width: 100%;
                text-align: center;
                padding: 10px 0;
            }

            .navbar-links .nav-link {
                display: block;
                padding: 12px 20px;
                font-size: 16px;
                border-radius: 5px;
                color: var(--dark-color);
            }

            .navbar-links .nav-link:hover {
                background-color: #f5f5f5;
                color: var(--primary-color);
            }
        }
        
        /* Blog Post Styles */
        .post-header {
            margin-top: 90px;
            padding: 60px 0;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .post-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .post-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .post-date {
            display: flex;
            align-items: center;
            color: var(--gray-color);
            margin-right: 20px;
            font-size: 0.9rem;
        }
        
        .post-date i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        .post-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .post-category {
            background-color: var(--primary-color);
            color: white;
            font-size: 0.75rem;
        font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
        }
        
        .post-category:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        /* Post Image Styles - Direct image styling without container */
        .post-content img:not(.featured-image):not(.author-avatar) {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }
        
        /* Responsive image sizes for inline content images */
    @media (min-width: 768px) {
            .post-content img:not(.featured-image):not(.author-avatar) {
                max-width: 85%;
            }
        }

        @media (min-width: 992px) {
            .post-content img:not(.featured-image):not(.author-avatar) {
                max-width: 75%;
            }
        }

        @media (min-width: 1200px) {
            .post-content img:not(.featured-image):not(.author-avatar) {
                max-width: 70%;
            }
        }
        
        /* Featured Image - Instagram style */
        .featured-image {
            display: block;
            width: 100%;
            max-width: 500px; /* Default size */
            height: auto;
            aspect-ratio: 1/1; /* Square aspect ratio */
            object-fit: cover;
            margin: 0 auto 40px; /* Center the image */
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Responsive image sizes for different screens */
        @media (min-width: 768px) {
            .featured-image {
                max-width: 600px; /* Larger on tablets */
            }
        }

        @media (min-width: 992px) {
            .featured-image {
                max-width: 700px; /* Even larger on laptops */
            }
        }

        @media (min-width: 1200px) {
            .featured-image {
                max-width: 800px; /* Largest on desktops */
            }
        }
        
        /* Remove old post-image styles */
        .post-image {
            display: none;
        }
        
        .post-content {
            padding: 60px 0;
        }
        
        .post-content p {
            margin-bottom: 20px;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
        }
        
        .post-content h2 {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .post-content h3 {
            margin-top: 30px;
            margin-bottom: 15px;
        font-weight: 600;
            color: var(--dark-color);
        }
        
        .post-content ul, 
        .post-content ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .post-content li {
            margin-bottom: 10px;
        }
        
        .post-content blockquote {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin: 20px 0;
        font-style: italic;
        }
        
        /* Related Posts Section */
        .related-posts {
            padding: 60px 0;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .related-posts h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--dark-color);
            text-align: center;
        }
        
        .related-post-card {
            display: block;
            text-decoration: none;
            background-color: white;
        border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .related-post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .related-post-img {
            height: 200px;
            overflow: hidden;
        }
        
        .related-post-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .related-post-card:hover .related-post-img img {
            transform: scale(1.05);
        }
        
        .related-post-content {
            padding: 20px;
        }
        
        .related-post-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .related-post-date {
            font-size: 0.8rem;
            color: var(--gray-color);
        }
        
        /* Share Buttons */
        .share-buttons {
        display: flex;
        gap: 10px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .share-title {
            font-weight: 600;
            margin-right: 15px;
            color: var(--dark-color);
        }
        
        .share-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #f5f5f5;
            color: var(--gray-color);
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .share-button:hover {
            color: white;
        }
        
        .share-facebook:hover {
            background-color: #3b5998;
        }
        
        .share-twitter:hover {
            background-color: #1da1f2;
        }
        
        .share-linkedin:hover {
            background-color: #0077b5;
        }
        
        .share-pinterest:hover {
            background-color: #bd081c;
        }
        
        .back-to-blogs {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--light-color);
            color: var(--dark-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .back-to-blogs i {
            margin-right: 8px;
        }
        
        .back-to-blogs:hover {
            background-color: #e9ecef;
            color: var(--primary-color);
        }
        
        /* Footer Section */
        .footer-section {
            background-color: #000;
            padding: 60px 0 20px;
            color: #f8f9fa;
        }
        
        .footer-section h4 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 18px;
        }
        
        .footer-logo {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .footer-logo img {
            height: 60px;
            margin-bottom: 15px;
            background-color: #fff;
            padding: 5px;
            border-radius: 5px;
        }
        
        .footer-logo p {
            color: #d1d1d1;
        }
        
        /* Footer Links */
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        a{
            text-decoration: none;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links li a {
            color: #d1d1d1;
            text-decoration: none;
            transition: color 0.3s;
            font-size: 14px;
        }
        
        .footer-links li a:hover {
            color: #fff;
        }
        
        .footer-links li i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        /* Social Icons */
        .social-icons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 50%;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .social-icons a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Footer Bottom */
        .footer-bottom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding-top: 30px;
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-bottom p {
            margin: 0;
            font-size: 14px;
            color: #a0a0a0;
        }
        
        .footer-bottom ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .footer-bottom ul li {
            margin-left: 20px;
        }
        
        .footer-bottom ul li a {
            color: #a0a0a0;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .footer-bottom ul li a:hover {
            color: #fff;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .post-title {
                font-size: 2.2rem;
            }
            
            .related-post-img {
                height: 180px;
            }
            
            .post-content p {
                font-size: 1rem;
            }
            
            .footer-section .col-md-6 {
                margin-bottom: 40px;
            }
            
            .footer-bottom {
            flex-direction: column;
                text-align: center;
            }
            
            .footer-bottom ul {
                margin-top: 15px;
                justify-content: center;
            }
            
            .footer-bottom ul li {
                margin: 0 10px;
            }
        }
        
        @media (max-width: 576px) {
            .post-header {
                padding: 40px 0;
            }
            
            .post-title {
                font-size: 1.8rem;
            }
            
            .post-content {
                padding: 40px 0;
            }
            
            .share-buttons {
                flex-wrap: wrap;
            }
            
            .featured-image {
                width: 280px; /* Smaller on mobile */
                height: 280px;
                margin-left: auto;
                margin-right: auto;
            }
        }
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            z-index: 99;
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Table of Contents */
        .table-of-contents {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .toc-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .toc-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .toc-list {
            padding-left: 20px;
            list-style: none;
        }
        
        .toc-list li {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }
        
        .toc-list a {
            color: var(--dark-color);
            text-decoration: none;
            transition: color 0.3s;
            display: inline-block;
        }
        
        .toc-list a:hover {
            color: var(--primary-color);
        }
        
        .toc-h3 {
            padding-left: 20px;
            font-size: 0.9rem;
        }
        
        /* Author Info - removed as requested */
        .author-info,
        .author-avatar,
        .author-details {
            display: none;
        }
        
        /* Reading Time */
        .reading-time {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--gray-color);
            margin-left: 20px;
        }
        
        .reading-time i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        /* Post Navigation */
        .post-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .nav-previous,
        .nav-next {
            flex: 0 0 48%;
        }
        
        .nav-next {
            text-align: right;
        }
        
        .nav-link {
            display: block;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: #e9ecef;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .nav-label {
            font-size: 0.85rem;
            color: var(--gray-color);
            margin-bottom: 5px;
            display: block;
        }
        
        .nav-title {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 1rem;
            transition: color 0.3s;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .nav-link:hover .nav-title {
            color: var(--primary-color);
        }
        
        /* Copy Link Button */
        .share-copy {
            background-color: #f5f5f5;
        }
        
        .share-copy:hover {
            background-color: #4CAF50;
    }

    /* Dropdown Menu */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #ffffff;
    min-width: 200px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 8px;
    z-index: 1000;
}

.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-item {
    display: block;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 6px;
    text-align: center;
}

.dropdown-item:hover {
    background: #f5f5f5;
    color: #d10000;
}

/* Mobile Dropdown */
@media (max-width: 991px) {
    .dropdown-menu {
        position: static;
        transform: none;
        box-shadow: none;
        width: 100%;
        display: none;
        padding: 0;
        margin-top: 10px;
    }

    .dropdown.active .dropdown-menu {
        display: block;
    }

    .dropdown-item {
        padding: 10px 20px;
        text-align: center;
        background: #f5f5f5;
    }
}

</style>
</head>
<body>
<header class="navbar">
      <div class="container">
        <!-- Logo -->
        <a href="/" class="navbar-logo">
          <img
            src="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif"
            alt="Sortout Innovation"
          />
        </a>

        <!-- Navigation Links -->
        <nav class="navbar-links">
          <ul>
            <li><a href="/" class="nav-link">Home</a></li>
            <li><a href="/pages/about-page/about.html" class="nav-link">About</a></li>
            <li class="dropdown">
              <a href="#" class="nav-link">Career <i class="fas fa-chevron-down"></i></a>
              <div class="dropdown-menu">
                <a href="/employee-job/index.php" class="dropdown-item">Employee Jobs</a>
                <a href="/artist-job/index.php" class="dropdown-item">Artist Jobs</a>
              </div>
            </li>
                            <li><a href="/registration.php" class="nav-link">Find Talent</a></li>
            <li><a href="/pages/our-services-page/service.html" class="nav-link">Service</a></li>
            <li><a href="/pages/contact-page/contact-page.html" class="nav-link">Contact</a></li>
            <li><a href="/blog/index.php" class="nav-link">Blog</a></li>
          </ul>
        </nav>

        <!-- Mobile Menu Button -->
        <button class="navbar-toggle" aria-label="Toggle navigation">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </header>

    <script>
      // Mobile Menu Toggle
      document.addEventListener('DOMContentLoaded', function() {
          const navbarToggle = document.querySelector(".navbar-toggle");
          const navbarLinks = document.querySelector(".navbar-links");
          const dropdowns = document.querySelectorAll(".dropdown");

          // Toggle mobile menu
          navbarToggle.addEventListener("click", function(e) {
              e.preventDefault();
              navbarLinks.classList.toggle("active");
              // Toggle icon between bars and times
              const icon = this.querySelector("i");
              icon.classList.toggle("fa-bars");
              icon.classList.toggle("fa-times");
          });

          // Handle dropdowns on mobile
          dropdowns.forEach(dropdown => {
              const link = dropdown.querySelector("a");
              link.addEventListener("click", (e) => {
                  if (window.innerWidth <= 991) {
                      e.preventDefault();
                      dropdown.classList.toggle("active");
                  }
              });
          });

          // Close mobile menu when clicking outside
          document.addEventListener("click", function(e) {
              if (!navbarLinks.contains(e.target) && !navbarToggle.contains(e.target) && navbarLinks.classList.contains("active")) {
                  navbarLinks.classList.remove("active");
                  const icon = navbarToggle.querySelector("i");
                  icon.classList.add("fa-bars");
                  icon.classList.remove("fa-times");
                  dropdowns.forEach(dropdown => dropdown.classList.remove("active"));
              }
          });

          // Sticky Navbar on Scroll
          window.addEventListener("scroll", () => {
              const navbar = document.querySelector(".navbar");
              if (window.scrollY > 50) {
                  navbar.classList.add("scrolled");
              } else {
                  navbar.classList.remove("scrolled");
              }
          });
      });
    </script>

    <!-- Post Header -->
    <section class="post-header">
        <div class="container">
            <a href="index.php" class="back-to-blogs">
                <i class="fas fa-arrow-left"></i> Back to Blogs
            </a>
            
            <h1 class="post-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
            
            <div class="post-meta">
                <div class="post-date">
                    <i class="far fa-calendar-alt"></i> <?php echo $formatted_date; ?>
                </div>
                
                <div class="reading-time">
                    <i class="far fa-clock"></i> <?php echo $reading_time; ?> min read
                </div>
                
                <?php if(!empty($categories)): ?>
                <div class="post-categories">
                    <?php foreach($categories as $category): ?>
                        <a href="index.php?category=<?php echo urlencode($category); ?>" class="post-category">
                            <?php echo htmlspecialchars($category); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Post Content -->
    <article class="post-content">
        <div class="container">
            <?php if(!empty($blog['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="featured-image">
            <?php endif; ?>
            
            <?php if($needs_toc && count($headings) > 0): ?>
            <div class="table-of-contents">
                <h3 class="toc-title"><i class="fas fa-list"></i> Table of Contents</h3>
                <ul class="toc-list">
                    <?php foreach($headings as $heading): ?>
                        <li class="<?php echo $heading['level'] == 3 ? 'toc-h3' : ''; ?>">
                            <a href="#<?php echo $heading['id']; ?>"><?php echo $heading['text']; ?></a>
                        </li>
                    <?php endforeach; ?>
        </ul>
            </div>
            <?php endif; ?>
            
            <div class="post-body">
                <?php echo $blog['content']; ?>
            </div>
            
            <!-- Share Buttons -->
            <div class="share-buttons">
                <span class="share-title">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-button share-facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-button share-twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-button share-linkedin">
                <i class="fab fa-linkedin-in"></i>
            </a>
                <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&media=<?php echo urlencode($blog['image_url']); ?>&description=<?php echo urlencode($blog['title']); ?>" target="_blank" class="share-button share-pinterest">
                    <i class="fab fa-pinterest-p"></i>
                </a>
                <button class="share-button share-copy" id="copyLinkBtn" data-url="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
                    <i class="fas fa-link"></i>
                </button>
            </div>
            
            <!-- Post Navigation -->
            <?php if($prev_post || $next_post): ?>
            <div class="post-navigation">
                <?php if($prev_post): ?>
                <div class="nav-previous">
                    <a href="post.php?slug=<?php echo urlencode($prev_post['slug']); ?>" class="nav-link">
                        <span class="nav-label"><i class="fas fa-arrow-left mr-1"></i> Previous Post</span>
                        <div class="nav-title"><?php echo htmlspecialchars($prev_post['title']); ?></div>
            </a>
        </div>
                <?php else: ?>
                <div class="nav-previous"></div>
                <?php endif; ?>
                
                <?php if($next_post): ?>
                <div class="nav-next">
                    <a href="post.php?slug=<?php echo urlencode($next_post['slug']); ?>" class="nav-link">
                        <span class="nav-label">Next Post <i class="fas fa-arrow-right ml-1"></i></span>
                        <div class="nav-title"><?php echo htmlspecialchars($next_post['title']); ?></div>
                    </a>
                </div>
                <?php else: ?>
                <div class="nav-next"></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </article>
    
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Related Posts -->
    <?php if(!empty($related_posts)): ?>
    <section class="related-posts">
        <div class="container">
            <h2>You Might Also Like</h2>
            
            <div class="row">
                <?php foreach($related_posts as $related): ?>
                    <?php 
                        $related_date = new DateTime($related['created_at']);
                        $related_formatted_date = $related_date->format('M d, Y');
                    ?>
                    <div class="col-md-4 mb-4">
                        <a href="post.php?slug=<?php echo urlencode($related['slug']); ?>" class="related-post-card">
                            <div class="related-post-img">
                                <?php if(!empty($related['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php else: ?>
                                    <img src="../public/images/blog-placeholder.jpg" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php endif; ?>
    </div>
                            <div class="related-post-content">
                                <h3 class="related-post-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                                <div class="related-post-date">
                                    <i class="far fa-calendar-alt"></i> <?php echo $related_formatted_date; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <!-- Column 1: Company Info -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-logo">
                        <img src="/images/sortoutInnovation-icon/Sortout innovation.jpg" alt="Sortout Innovation" />
                        <p class="text-center">
                            Empowering businesses with top-notch solutions in digital, IT, and business services.
                        </p>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/pages/about-page/about.html">About Us</a></li>
                        <li><a href="/pages/contact-page/contact-page.html">Contact</a></li>
                        <li><a href="/pages/career.html">Careers</a></li>
                        <li><a href="/pages/our-services-page/service.html">Services</a></li>
                        <li><a href="/blog/index.php">Blogs</a></li>
                        <li><a href="/auth/register.php">Register</a></li>
                        <li><a href="/registration.php">talent</a></li>
                    </ul>
                </div>

                <!-- Column 3: Our Services -->
                <div class="col-lg-2 col-md-6">
                    <h4>Our Services</h4>
                    <ul class="footer-links">
                        <li><a href="/pages/services/socialMediaInfluencers.html">Digital Marketing</a></li>
                        <li><a href="/pages/services/itServices.html">IT Support</a></li>
                        <li><a href="/pages/services/caServices.html">CA Services</a></li>
                        <li><a href="/pages/services/hrServices.html">HR Services</a></li>
                        <li><a href="/pages/services/courierServices.html">Courier Services</a></li>
                        <li><a href="/pages/services/shipping.html">Shipping & Fulfillment</a></li>
                        <li><a href="/pages/services/stationeryServices.html">Stationery Services</a></li>
                        <li><a href="/pages/services/propertyServices.html">Real Estate & Property</a></li>
                        <li><a href="/pages/services/event-managementServices.html">Event Management</a></li>
                        <li><a href="/pages/services/designAndCreative.html">Design & Creative</a></li>
                        <li><a href="/pages/services/designAndCreative.html">Web & App Development</a></li>
                        <li><a href="/pages/talent.page/talent.html">Find Talent</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h4>Contact Us</h4>
                    <ul class="footer-links">
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:+919818559036">+91 9818559036</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@sortoutinnovation.com">info@sortoutinnovation.com</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i> Spaze i-Tech Park, Gurugram, India
                        </li>
                    </ul>
                </div>

                <!-- Column 5: Social Media -->
                <div class="col-lg-2 col-md-6">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/profile.php?id=61556452066209"><i class="fab fa-facebook"></i></a>
                        <a href="https://youtu.be/tw-xk-Pb-zA?si=QMTwuvhEuTegpqDr"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.linkedin.com/company/sortout-innovation/"><i class="fab fa-linkedin"></i></a>
                        <a href="https://www.instagram.com/sortoutinnovation"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Copyright & Legal Links -->
            <div class="footer-bottom">
                <p>&copy; 2025 Sortout Innovation. All Rights Reserved.</p>
                <ul>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/terms">Terms & Conditions</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Back to Top Button
            const backToTopBtn = document.getElementById('backToTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            });
            
            backToTopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Copy Link Button
            const copyLinkBtn = document.getElementById('copyLinkBtn');
            
            if (copyLinkBtn) {
                copyLinkBtn.addEventListener('click', () => {
                    const url = copyLinkBtn.getAttribute('data-url');
                    
                    // Create a temporary input element
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    
                    // Select and copy the URL
                    tempInput.select();
                    document.execCommand('copy');
                    
                    // Remove the temporary input
                    document.body.removeChild(tempInput);
                    
                    // Provide visual feedback
                    const originalIcon = copyLinkBtn.innerHTML;
                    copyLinkBtn.innerHTML = '<i class="fas fa-check"></i>';
                    copyLinkBtn.style.backgroundColor = '#4CAF50';
                    copyLinkBtn.style.color = 'white';
                    
                    // Restore original icon after 2 seconds
                    setTimeout(() => {
                        copyLinkBtn.innerHTML = originalIcon;
                        copyLinkBtn.style.backgroundColor = '';
                        copyLinkBtn.style.color = '';
                    }, 2000);
                });
            }
            
            // Smooth scroll for table of contents links
            const tocLinks = document.querySelectorAll('.toc-list a');
            
            tocLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const targetId = link.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        // Offset for fixed header
                        const offset = 100;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
