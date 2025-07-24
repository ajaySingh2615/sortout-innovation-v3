<?php
require_once '../../../includes/db_connect.php';
include_once '../includes/functions.php';

// Get all active categories
$categories = getCategories(true);

// Sort categories in descending order by name
usort($categories, function($a, $b) {
    return strcmp($b['name'], $a['name']); // Descending order (b compared to a)
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      rel="icon"
      type="image/png"
      href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif"
    />
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>App Directory - SortOut Innovation</title>
    <meta property="og:url" content="https://example.com/artist-v2/">
    <meta property="og:image" content="https://example.com/artist-v2/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    
    <!-- Google Fonts: Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #d10000;
            --primary-light: #ff4b4b;
            --primary-dark: #c50000;
            --text-color: #2c3e50;
            --light-color: #f8f9f9;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
            --border-radius: 1rem;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #444;
            line-height: 1.6;
            padding-top: 76px; /* Account for fixed navbar */
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Typography */
        .display-3, .display-4, .display-5 {
            font-weight: 700;
        }
        
        /* Button styles */
        .btn {
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }
        
        .btn-danger {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        
        .btn-outline-danger {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-danger:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        /* Badge styles */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
    
    <!-- Modern Navbar Styles -->
    <style>
        /* Modern Navbar Styles with Enhanced Features */
        .navbar {
            padding: 0.8rem 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
        }

        /* Logo Container with Glow Effect */
        .logo-container {
            position: relative;
            display: inline-block;
        }

        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(209, 0, 0, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .navbar-brand:hover .logo-glow {
            opacity: 1;
        }

        /* Navigation Links */
        .nav-link {
            font-weight: 500;
            color: #2c3e50 !important;
            position: relative;
            transition: all 0.3s ease;
            font-size: 15px;
            letter-spacing: 0.3px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, #d10000, #ff4b4b);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-link:hover::after {
            width: calc(100% - 1.5rem);
        }

        .nav-link:hover, .nav-link.active {
            color: #d10000 !important;
        }

        /* Enhanced Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 0.8rem 0.5rem;
            margin-top: 0.5rem;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
        }

        .dropdown-item {
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #2c3e50;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: linear-gradient(45deg, rgba(209, 0, 0, 0.05), rgba(255, 75, 75, 0.05));
            color: #d10000;
            transform: translateX(5px);
        }
        
        .dropdown-item.active {
            background: linear-gradient(45deg, rgba(209, 0, 0, 0.1), rgba(255, 75, 75, 0.1));
            color: #d10000;
            font-weight: 500;
        }

        /* Animated Mobile Toggle Button */
        .toggle-icon {
            width: 24px;
            height: 20px;
            position: relative;
            cursor: pointer;
        }

        .toggle-icon span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: #2c3e50;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .toggle-icon span:first-child {
            top: 0;
        }

        .toggle-icon span:nth-child(2) {
            top: 9px;
        }

        .toggle-icon span:last-child {
            top: 18px;
        }

        .navbar-toggler[aria-expanded="true"] .toggle-icon span:first-child {
            transform: rotate(45deg);
            top: 9px;
        }

        .navbar-toggler[aria-expanded="true"] .toggle-icon span:nth-child(2) {
            opacity: 0;
        }

        .navbar-toggler[aria-expanded="true"] .toggle-icon span:last-child {
            transform: rotate(-45deg);
            top: 9px;
        }

        /* Mobile Styles */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                padding: 1rem;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                margin-top: 1rem;
            }

            .nav-link {
                padding: 0.8rem 1.2rem !important;
                border-radius: 8px;
            }

            .nav-link:hover {
                background: linear-gradient(45deg, rgba(209, 0, 0, 0.05), rgba(255, 75, 75, 0.05));
            }

            .nav-link::after {
                display: none;
            }

            .dropdown-menu {
                box-shadow: none;
                padding-left: 1rem;
                background: transparent;
            }

            .dropdown-item:hover {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <!-- Bootstrap Navbar with Enhanced Features -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <!-- Logo with hover effect -->
            <a class="navbar-brand position-relative" href="/">
                <div class="logo-container">
                    <img src="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" alt="SortOut Innovation" height="45" class="main-logo">
                    <div class="logo-glow"></div>
                </div>
            </a>

            <!-- Animated Mobile Toggle Button -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <div class="toggle-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <!-- Enhanced Navigation Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/pages/about-page/about.html">About</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3 active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Career
                        </a>
                        <ul class="dropdown-menu animate slideIn">
                            <li>
                                <a class="dropdown-item" href="/employee-job/index.php">Employee Jobs</a>
                            </li>
                            <li>
                                <!-- <a class="dropdown-item" href="/artist-job/index.php">Artist Jobs</a> -->
                            </li>
                            <li>
                                <a class="dropdown-item active" href="/admin/artist-v2/public/index.php">Artist Jobs</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/registration.php">Find Talent</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/pages/our-services-page/service.html">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/pages/contact-page/contact-page.html">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/blog/index.php">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Bootstrap Carousel -->
    <div class="hero-wrapper">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>
            
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <img src="/images/artist-page-banner/banner-artist-job-1.jpg" class="d-block w-100" alt="Artist Jobs">
                </div>
                
                <!-- Slide 2 -->
                <div class="carousel-item">
                    <img src="/images/artist-page-banner/banner-artist-job-2.jpg" class="d-block w-100" alt="Talent Showcase">
                </div>
                
                <!-- Slide 3 -->
                <div class="carousel-item">
                    <img src="/images/artist-page-banner/banner-artist-job-3.jpg" class="d-block w-100" alt="Creative Opportunity">
                </div>
                
                <!-- Slide 4 -->
                <div class="carousel-item">
                    <img src="/images/artist-page-banner/banner-artist-job-4.jpg" class="d-block w-100" alt="App Directory">
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <!-- Hero & Carousel Styling -->
    <style>
        /* Hero Styling */
        .hero-wrapper {
            margin-top: 0;
            width: 100%;
            overflow: hidden;
        }
        
        #heroCarousel {
            margin-bottom: 0;
        }
        
        #heroCarousel .carousel-item {
            height: auto;
            max-height: none;
        }
        
        #heroCarousel .carousel-item img {
            width: 100%;
            height: auto;
            object-fit: contain;
            max-height: 80vh;
            object-position: center;
        }
        
        /* Carousel controls */
        #heroCarousel .carousel-control-prev,
        #heroCarousel .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
        }
        
        #heroCarousel .carousel-control-prev {
            left: 20px;
        }
        
        #heroCarousel .carousel-control-next {
            right: 20px;
        }
        
        #heroCarousel .carousel-indicators {
            bottom: 20px;
        }
        
        #heroCarousel .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 5px;
            background-color: rgba(255, 255, 255, 0.5);
        }
        
        #heroCarousel .carousel-indicators button.active {
            background-color: #ff2020;
            transform: scale(1.2);
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            #heroCarousel .carousel-item img {
                max-height: 60vh;
            }
        }
        
        @media (max-width: 767px) {
            #heroCarousel .carousel-item img {
                max-height: 50vh;
            }
            
            #heroCarousel .carousel-control-prev,
            #heroCarousel .carousel-control-next {
                width: 40px;
                height: 40px;
            }
        }
        
        @media (max-width: 480px) {
            #heroCarousel .carousel-item img {
                max-height: 40vh;
            }
        }
    </style>

    <!-- Animation script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const heroCarousel = document.getElementById('heroCarousel');
        
        // Initialize Bootstrap carousel with specific settings
        var carousel = new bootstrap.Carousel(heroCarousel, {
            interval: 5000,
            wrap: true,
            touch: true
        });
    });
    </script>
    
    <!-- Link Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    
    <!-- Categories Section -->
    <section id="categories" class="py-5 mt-4 categories-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill mb-3">Explore</span>
                <h2 class="display-5 fw-bold mb-3">App <span class="text-danger">Categories</span></h2>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <p class="lead text-muted">Find the perfect app for your needs by exploring our carefully curated categories</p>
                    </div>
                </div>
                <div class="custom-divider mx-auto mt-3"></div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card category-card border-0 rounded-4 shadow-sm h-100 animate__animated animate__fadeIn" 
                         data-category-id="<?php echo $category['id']; ?>" 
                         onclick="openCategoryModal(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')">
                        <div class="category-img-container">
                            <div class="category-image-overlay d-flex align-items-center justify-content-center">
                                <i class="fas fa-search-plus text-white fs-1 opacity-0 category-icon"></i>
                            </div>
                            <div class="category-ribbon">
                                <span>Category</span>
                            </div>
                            <img src="<?php echo $category['image_url']; ?>" 
                                 class="category-img" 
                                 alt="<?php echo $category['name']; ?>" 
                                 onerror="this.src='../../assets/img/default-category.jpg';">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-2"><?php echo $category['name']; ?></h5>
                            <p class="card-text text-muted mb-3"><?php echo substr($category['description'], 0, 80); ?><?php echo (strlen($category['description']) > 80) ? '...' : ''; ?></p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-danger rounded-pill px-3 py-2">
                                    <i class="fas fa-mobile-alt me-1"></i> Explore Apps
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (count($categories) === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info rounded-4 shadow-sm">
                        <i class="fas fa-info-circle me-2"></i> No categories available at the moment. Please check back later.
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Selected Category Apps -->
    <section id="category-apps" class="py-5 category-apps-section d-none">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div>
                    <span class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill mb-2">Category</span>
                    <h2 class="display-5 fw-bold" id="category-title">Category Apps</h2>
                </div>
                <button class="btn btn-outline-danger rounded-pill px-4 py-2 mt-3 mt-md-0" onclick="showAllCategories()">
                    <i class="fas fa-arrow-left me-2"></i> Back to Categories
                </button>
            </div>
            <div class="custom-divider mb-4"></div>
            <p id="category-description" class="lead mb-4"></p>
            <div class="row g-4" id="apps-container">
                <!-- Apps will be loaded here dynamically -->
            </div>
        </div>
    </section>

    <!-- Popular Apps Section -->
    <!-- <section id="popular-apps" class="py-5 popular-apps-section">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill mb-3">Trending</span>
                <h2 class="display-5 fw-bold mb-3">Popular <span class="text-danger">Apps</span></h2>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <p class="lead text-muted">Discover the most popular apps loved by our users</p>
                    </div>
                </div>
                <div class="custom-divider mx-auto mt-3"></div>
            </div>
            
            <div class="row g-4" id="popular-apps-container">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- About Section -->
    <section id="about" class="py-5 about-section">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill mb-3">About Us</span>
                <h2 class="display-5 fw-bold mb-3">About Our <span class="text-danger">App Directory</span></h2>
                <div class="custom-divider mx-auto mt-3"></div>
            </div>
            
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-content pe-lg-4">
                        <p class="lead mb-4">Our App Directory provides a curated collection of the best apps across various categories. Whether you're looking for productivity tools, entertainment, social media, or educational apps, we've got you covered.</p>
                        <p class="mb-4">We carefully review each app to ensure it meets our quality standards before adding it to our directory.</p>
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="about-icon-wrapper me-3">
                                <i class="fas fa-check text-danger"></i>
                            </div>
                            <p class="mb-0"><strong>Quality Assured:</strong> Every app is thoroughly tested</p>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="about-icon-wrapper me-3">
                                <i class="fas fa-shield-alt text-danger"></i>
                            </div>
                            <p class="mb-0"><strong>Safety First:</strong> We prioritize user privacy and security</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow feature-card">
                        <div class="card-body p-4 p-xl-5">
                            <h4 class="fw-bold mb-4 text-center">Why Use Our <span class="text-danger">Directory</span>?</h4>
                            
                            <div class="feature-item d-flex align-items-start mb-4">
                                <div class="feature-icon bg-danger text-white rounded-circle p-3 me-3 flex-shrink-0">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">Curated Selection</h5>
                                    <p class="text-muted mb-0">We hand-pick only the highest quality apps</p>
                                </div>
                            </div>
                            
                            <div class="feature-item d-flex align-items-start mb-4">
                                <div class="feature-icon bg-danger text-white rounded-circle p-3 me-3 flex-shrink-0">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">Easy Browsing</h5>
                                    <p class="text-muted mb-0">Organized by categories for effortless discovery</p>
                                </div>
                            </div>
                            
                            <div class="feature-item d-flex align-items-start mb-4">
                                <div class="feature-icon bg-danger text-white rounded-circle p-3 me-3 flex-shrink-0">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">Direct Links</h5>
                                    <p class="text-muted mb-0">Quick access to app download forms</p>
                                </div>
                            </div>
                            
                            <div class="feature-item d-flex align-items-start">
                                <div class="feature-icon bg-danger text-white rounded-circle p-3 me-3 flex-shrink-0">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">Regular Updates</h5>
                                    <p class="text-muted mb-0">Constantly updated with the newest apps</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
      <div class="container">
        <div class="row">
          <!-- Column 1: Company Info -->
          <div class="col-lg-3 col-md-6">
            <div class="footer-logo">
              <img
                src="/images/sortoutInnovation-icon/Sortout innovation.jpg"
                alt="SortOut Innovation"
              />
              <p class="text-center">
                Empowering businesses with top-notch solutions in digital, IT,
                and business services.
              </p>
            </div>
          </div>

          <!-- Column 2: Quick Links -->
          <div class="col-lg-2 col-md-6">
            <h4>Quick Links</h4>
            <ul class="footer-links">
              <li><a href="/">Home</a></li>
              <li><a href="/pages/about-page/about.html">About Us</a></li>
              <li>
                <a href="/pages/contact-page/contact-page.html">Contact</a>
              </li>
              <li>
                <a href="/pages/career.html">Careers</a>
              </li>
              <li>
                <a href="/pages/our-services-page/service.html">Services</a>
              </li>
              <li>
                <a href="/blog/index.php">Blogs</a>
              </li>
              <li>
                <a href="/auth/register.php">Register</a>
              </li>
              <li>
                <a href="/registration.php">talent</a>
              </li>
            </ul>
          </div>

          <!-- Column 3: Our Services -->
          <div class="col-lg-2 col-md-6">
            <h4>Our Services</h4>
            <ul class="footer-links">
              <li>
                <a href="/pages/services/socialMediaInfluencers.html"
                  >Digital Marketing</a
                >
              </li>
              <li><a href="/pages/services/itServices.html">IT Support</a></li>
              <li><a href="/pages/services/caServices.html">CA Services</a></li>
              <li><a href="/pages/services/hrServices.html">HR Services</a></li>
              <li>
                <a href="/pages/services/courierServices.html"
                  >Courier Services</a
                >
              </li>
              <li>
                <a href="/pages/services/shipping.html"
                  >Shipping & Fulfillment</a
                >
              </li>
              <li>
                <a href="/pages/services/stationeryServices.html"
                  >Stationery Services</a
                >
              </li>
              <li>
                <a href="/pages/services/propertyServices.html"
                  >Real Estate & Property</a
                >
              </li>
              <li>
                <a href="/pages/services/event-managementServices.html"
                  >Event Management</a
                >
              </li>
              <li>
                <a href="/pages/services/designAndCreative.html"
                  >Design & Creative</a
                >
              </li>
              <li>
                <a href="/pages/services/designAndCreative.html"
                  >Web & App Development</a
                >
              </li>
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
                <a href="mailto:info@sortoutinnovation.com"
                  >info@sortoutinnovation.com</a
                >
              </li>
              <li>
                <i class="fas fa-map-marker-alt"></i> Spaze i-Tech Park,
                Gurugram, India
              </li>
            </ul>
          </div>

          <!-- Column 5: Social Media -->
          <div class="col-lg-2 col-md-6">
            <h4>Follow Us</h4>
            <div class="social-icons">
              <a href="https://www.facebook.com/profile.php?id=61556452066209"
                ><i class="fab fa-facebook"></i
              ></a>
              <a href="https://youtu.be/tw-xk-Pb-zA?si=QMTwuvhEuTegpqDr"
                ><i class="fab fa-youtube"></i
              ></a>
              <a href="https://www.linkedin.com/company/sortout-innovation/"
                ><i class="fab fa-linkedin"></i
              ></a>
              <a href="https://www.instagram.com/sortoutinnovation"
                ><i class="fab fa-instagram"></i
              ></a>
            </div>
          </div>
        </div>

        <!-- Copyright & Legal Links -->
        <div class="footer-bottom">
          <p>&copy; <?php echo date('Y'); ?> SortOut Innovation. All Rights Reserved.</p>
          <ul>
            <li><a href="/privacy-policy">Privacy Policy</a></li>
            <li><a href="/terms">Terms & Conditions</a></li>
          </ul>
        </div>
      </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-danger rounded-circle shadow-lg back-to-top" id="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Add this modal at the end of the body -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="categoryModalLabel">Category Apps</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalAppsContainer" class="row g-4">
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar animation
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            let lastScroll = 0;

            // Enhanced scroll behavior
            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 50) {
                    navbar.style.padding = "0.6rem 0";
                    navbar.style.boxShadow = "0 5px 30px rgba(0,0,0,0.08)";
                } else {
                    navbar.style.padding = "0.8rem 0";
                    navbar.style.boxShadow = "none";
                }

                // Smooth hide/show on scroll
                if (currentScroll > lastScroll && currentScroll > 100) {
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    navbar.style.transform = 'translateY(0)';
                }

                lastScroll = currentScroll;
            });

            // Handle mobile menu toggle animation
            const navbarToggler = document.querySelector('.navbar-toggler');
            navbarToggler.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
            });
        });

        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initialize the Bootstrap modal
        let categoryModal;
        document.addEventListener('DOMContentLoaded', function() {
            categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
            
            // Add CSS for hover effects
            const style = document.createElement('style');
            style.textContent = `
                .back-to-top {
                    position: fixed;
                    bottom: 25px;
                    right: 25px;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background-color: #ff4b4b;
                    color: white;
                    display: none;
                    z-index: 1000;
                    box-shadow: 0 4px 15px rgba(255, 75, 75, 0.3);
                    transition: all 0.3s ease;
                    opacity: 0.9;
                }
                
                .back-to-top:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 6px 20px rgba(255, 75, 75, 0.4);
                    opacity: 1;
                }
                
                .back-to-top i {
                    font-size: 1.2rem;
                    line-height: 0;
                }
                
                .category-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    cursor: pointer;
                }
                .category-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
                }
                .category-image-overlay {
                    background: rgba(0,0,0,0.2);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }
                .category-card:hover .category-image-overlay {
                    opacity: 1;
                }
                .category-card:hover .category-icon {
                    opacity: 1 !important;
                }
                .feature-icon {
                    width: 50px;
                    height: 50px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .app-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    border-radius: 1rem;
                    overflow: hidden;
                    border: none;
                }
                .app-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
                }
                .app-img {
                    width: 60px;
                    height: 60px;
                    object-fit: cover;
                    border-radius: 15px;
                }
            `;
            document.head.appendChild(style);
            
            // Update the primary buttons to use danger class
            setTimeout(() => {
                document.querySelectorAll('.btn-primary').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-danger');
                });
                
                // Update text-primary to text-danger
                document.querySelectorAll('.text-primary').forEach(el => {
                    el.classList.remove('text-primary');
                    el.classList.add('text-danger');
                });
            }, 100);
        });

        // Open category modal and load apps
        function openCategoryModal(categoryId, categoryName) {
            // Set modal title
            document.getElementById('categoryModalLabel').textContent = categoryName + ' Apps';
            
            // Show loading spinner
            document.getElementById('modalAppsContainer').innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Show the modal
            categoryModal.show();
            
            // Fetch apps for this category
            fetch(`get_category_apps.php?category_id=${categoryId}`)
                .then(response => {
                    console.log('Fetch response status:', response.status);
                    console.log('Response headers:', [...response.headers.entries()]);
                    return response.json();
                })
                .then(data => {
                    console.log('Category apps data:', data);
                    
                    const appsContainer = document.getElementById('modalAppsContainer');
                    appsContainer.innerHTML = '';
                    
                    if (!data.success || data.apps.length === 0) {
                        appsContainer.innerHTML = '<div class="col-12"><div class="alert alert-info rounded-4 shadow-sm"><i class="fas fa-info-circle me-2"></i> No apps available in this category yet.</div></div>';
                        return;
                    }
                    
                    data.apps.forEach(app => {
                        const appCard = document.createElement('div');
                        appCard.className = 'col-md-6 col-lg-4 mb-4';
                        appCard.innerHTML = `
                            <div class="card app-card shadow-sm h-100 animate__animated animate__fadeIn">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="app-icon-wrapper rounded-4 p-2 me-3 bg-light">
                                            <img src="${app.image_url}" class="app-img" alt="${app.name}" onerror="this.src='../../assets/img/default-app.jpg';">
                                        </div>
                                        <div>
                                            <h5 class="card-title fw-bold mb-1">${app.name}</h5>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                </div>
                                                <small class="text-muted">(4.5)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-text mb-4">${app.description}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="badge bg-light text-danger rounded-pill px-3 py-2 small">
                                            <i class="fas fa-mobile-alt me-1"></i> App
                                        </span>
                                        <a href="app-details.php?app=${app.name}" class="btn btn-danger rounded-pill px-4" target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i> Apply Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        appsContainer.appendChild(appCard);
                    });
                })
                .catch(error => {
                    console.error('Error loading category apps:', error);
                    document.getElementById('modalAppsContainer').innerHTML = 
                        '<div class="col-12"><div class="alert alert-danger rounded-4 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i> Failed to load apps for this category</div></div>';
                });
        }

        // Load popular apps on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            fetch('get_popular_apps.php')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    const container = document.getElementById('popular-apps-container');
                    container.innerHTML = '';
                    
                    if (!data.success || data.apps.length === 0) {
                        container.innerHTML = '<div class="col-12"><div class="alert alert-info rounded-4 shadow-sm"><i class="fas fa-info-circle me-2"></i> No popular apps available at the moment.</div></div>';
                        return;
                    }
                    
                    data.apps.forEach(app => {
                        const appCard = document.createElement('div');
                        appCard.className = 'col-md-6 col-lg-3 mb-4';
                        appCard.innerHTML = `
                            <div class="card app-card shadow-sm h-100 animate__animated animate__fadeIn">
                                <div class="position-relative">
                                    <div class="position-absolute top-0 end-0 m-3 z-1">
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-fire me-1"></i> Popular
                                        </span>
                                    </div>
                                    <div class="app-image-container">
                                        <img src="${app.image_url}" class="card-img-top" 
                                             alt="${app.name}" 
                                             onerror="this.src='../../assets/img/default-app.jpg';">
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title fw-bold mb-0">${app.name}</h5>
                                        <div class="app-icon bg-light rounded-circle p-2">
                                            <img src="${app.image_url}" 
                                                 class="rounded-circle" 
                                                 style="width: 30px; height: 30px; object-fit: cover;"
                                                 alt="${app.name}" 
                                                 onerror="this.src='../../assets/img/default-app.jpg';">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        <small class="text-muted ms-1">(4.5)</small>
                                    </div>
                                    <p class="card-text small mb-4">${app.description.substring(0, 100)}${app.description.length > 100 ? '...' : ''}</p>
                                </div>
                                <div class="card-footer bg-white border-0 p-4 pt-0">
                                    <a href="app-details.php?app=${app.name}" class="btn btn-danger w-100 rounded-pill" target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i> Apply Now
                                    </a>
                                </div>
                            </div>
                        `;
                        container.appendChild(appCard);
                    });
                })
                .catch(error => {
                    console.error('Error loading popular apps:', error);
                    document.getElementById('popular-apps-container').innerHTML = 
                        '<div class="col-12"><div class="alert alert-danger rounded-4 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i> Failed to load popular apps</div></div>';
                });
        });
    </script>

    <style>
        /* Categories and Selected Category Apps Sections Styling */
        .categories-section, .category-apps-section {
            font-family: 'Poppins', sans-serif;
        }
        
        .categories-section h2, .category-apps-section h2 {
            letter-spacing: -0.5px;
        }
        
        .categories-section .text-danger, .category-apps-section .text-danger {
            color: #ff2020 !important;
        }
        
        .custom-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff3e3e 0%, #c50000 100%);
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        
        .category-card {
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
        }
        
        .category-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        
        .category-img {
            height: 180px;
            object-fit: cover;
            transition: transform 0.5s ease;
            aspect-ratio: 16/9;
            width: 100%;
            object-position: center;
        }
        
        .category-card .card-img-top {
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
        }
        
        .position-relative.overflow-hidden {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .category-image-overlay {
            background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.6));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .category-card:hover .category-image-overlay {
            opacity: 1;
        }
        
        .category-ribbon {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            background: #ff2020;
            color: white;
            padding: 3px 10px;
            font-size: 0.75rem;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .bg-danger {
            background-color: #ff2020 !important;
        }
        
        .text-danger {
            color: #ff2020 !important;
        }
        
        .btn-outline-danger {
            border-color: #ff2020;
            color: #ff2020;
        }
        
        .btn-outline-danger:hover {
            background-color: #ff2020;
            color: white;
        }
        
        .badge.bg-light.text-danger {
            background-color: rgba(255, 32, 32, 0.1) !important;
            color: #ff2020 !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .category-card:hover .badge.bg-light.text-danger {
            background-color: rgba(255, 32, 32, 0.2) !important;
        }
        
        .category-card:hover .category-img {
            transform: scale(1.1);
        }
        
        /* Additional responsive styles for category cards and images */
        .category-card .position-relative.overflow-hidden {
            max-height: 180px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 991px) {
            .category-card .position-relative.overflow-hidden {
                max-height: 160px;
            }
        }

        @media (max-width: 575px) {
            .category-img {
                height: 180px;
            }
            
            .category-card .position-relative.overflow-hidden {
                max-height: 180px;
            }
        }

        /* Popular Apps Section Styling */
        .popular-apps-section {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            position: relative;
            overflow: hidden;
        }
        
        /* App card image styling */
        .app-card .position-relative {
            overflow: hidden;
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
        }

        .app-card .card-img-top {
            transition: transform 0.5s ease;
            width: 100%;
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
        }

        .app-image-container {
            overflow: hidden;
            width: 100%;
            background-color: #f8f9fa;
            background-image: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
            position: relative;
            padding-top: 100%; /* Create a square aspect ratio container (1:1) */
        }
        
        .app-image-container .card-img-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 15px;
        }

        .app-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Add image overlay effect */
        .app-card .position-relative::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.15));
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .app-card:hover .position-relative::after {
            opacity: 1;
        }
        
        .popular-apps-section::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 32, 32, 0.03);
            z-index: 0;
        }
        
        .popular-apps-section::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 32, 32, 0.05);
            z-index: 0;
        }
        
        .popular-apps-section .container {
            position: relative;
            z-index: 1;
        }
        
        /* Update the modal styling for apps */
        #categoryModal .modal-header {
            background-color: #ff2020;
            color: white;
        }
        
        #categoryModal .modal-title {
            color: white;
        }
        
        #categoryModal .btn-close {
            filter: brightness(0) invert(1);
        }
        
        #categoryModal .modal-footer {
            background-color: #f9f9f9;
        }
        
        #categoryModal .btn-outline-secondary {
            border-color: #ff2020;
            color: #ff2020;
        }
        
        #categoryModal .btn-outline-secondary:hover {
            background-color: #ff2020;
            color: white;
        }
        
        /* Spinner color update */
        .spinner-border.text-primary {
            color: #ff2020 !important;
        }

        /* About Section Styling */
        .about-section {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .about-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 32, 32, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: 0;
        }
        
        .about-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 32, 32, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: 0;
        }
        
        .about-content {
            position: relative;
            z-index: 1;
        }
        
        .about-icon-wrapper {
            width: 36px;
            height: 36px;
            background-color: rgba(255, 32, 32, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .feature-card {
            transform: translateY(0);
            transition: all 0.4s ease;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(145deg, #ffffff 0%, #f9f9f9 100%);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
        }
        
        .feature-item {
            transition: all 0.3s ease;
            padding: 10px;
            border-radius: 0.75rem;
        }
        
        .feature-item:hover {
            background-color: rgba(255, 32, 32, 0.05);
        }
        
        .feature-icon {
            box-shadow: 0 5px 15px rgba(255, 32, 32, 0.2);
            transition: all 0.3s ease;
        }
        
        .feature-item:hover .feature-icon {
            transform: scale(1.1);
        }

        /* Footer styling */
        .footer-section {
            background: #0a0a0a !important;
            color: #fff !important;
            padding: 60px 20px !important;
            font-family: Arial, sans-serif !important;
        }
        
        .footer-section h4 {
            color: #ff4b4b !important;
            margin-bottom: 15px !important;
            font-size: 1.4rem !important;
        }

        /* Footer Links */
        .footer-links {
            list-style: none !important;
            padding: 0 !important;
            font-size: 1rem !important;
        }

        .footer-links li {
            margin-bottom: 10px !important;
        }

        .footer-links li a {
            text-decoration: none !important;
            color: #ddd !important;
            transition: color 0.3s ease !important;
        }

        .footer-links li a:hover {
            color: #ff4b4b !important;
        }

        /* Social Icons */
        .social-icons {
            display: flex !important;
            gap: 10px !important;
        }

        .social-icons a {
            font-size: 1.5rem !important;
            color: #fff !important;
            transition: transform 0.3s ease, color 0.3s ease !important;
        }

        .social-icons a:hover {
            color: #ff4b4b !important;
            transform: scale(1.1) !important;
        }

        /* Footer Bottom */
        .footer-bottom {
            text-align: center !important;
            margin-top: 40px !important;
            padding-top: 20px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .footer-bottom p {
            font-size: 0.9rem !important;
            color: #aaa !important;
        }

        .footer-bottom ul {
            list-style: none !important;
            padding: 0 !important;
            display: flex !important;
            justify-content: center !important;
            gap: 15px !important;
            margin-top: 10px !important;
        }

        .footer-bottom ul li a {
            text-decoration: none !important;
            color: #ddd !important;
            font-size: 0.9rem !important;
            transition: color 0.3s ease !important;
        }

        .footer-bottom ul li a:hover {
            color: #ff4b4b !important;
        }

        .footer-logo img {
            padding-bottom: 15px !important;
            width: 180px !important; /* Adjust width as needed */
            height: auto !important; /* Maintains aspect ratio */
            max-width: 100% !important; /* Ensures responsiveness */
            display: block !important;
            margin: 0 auto !important; /* Centers the logo */
        }

        @media (max-width: 768px) {
            .footer-logo img {
                width: 140px !important; /* Smaller size for mobile */
            }
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .footer-section .row {
                flex-direction: column !important;
                text-align: center !important;
            }

            .social-icons {
                justify-content: center !important;
            }

            .footer-bottom ul {
                flex-direction: column !important;
                gap: 10px !important;
            }
            
            /* Responsive app card image adjustments */
            .app-card .card-img-top {
                padding: 10px;
            }
        }
        
        @media (max-width: 767px) {
            /* Ensure app images look good on mobile */
            .app-image-container {
                padding-top: 100%; /* Maintain square ratio on mobile */
            }
        }

        /* Responsive image handling for category cards - ensures proper display on all devices */
        .category-img-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            width: 100%;
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
        }
        
        .category-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s ease;
        }
        
        .category-card:hover .category-img {
            transform: scale(1.1);
        }
        
        .category-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
    </style>
</body>
</html> 