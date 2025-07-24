<?php
require_once '../../../includes/db_connect.php';
include_once '../includes/functions.php';

// Get app name from URL
$appName = isset($_GET['app']) ? sanitize($_GET['app']) : '';

// If no app name is provided, redirect to index
if (empty($appName)) {
    header('Location: index.php');
    exit;
}

// Get app details
$appDetails = getAppByName($appName);

// If app not found, redirect to index
if (!$appDetails) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title><?php echo htmlspecialchars($appDetails['name']); ?> - App Details</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts: Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    
    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #444;
            line-height: 1.6;
            background-color: #f5f5f5;
            padding-top: 76px; /* Account for fixed navbar */
        }
        
        .app-details-container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .app-header {
            background-color: #d10000;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        
        .app-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .app-body {
            padding: 2rem;
        }
        
        .app-image {
            width: 100%;
            max-width: 180px;
            height: auto;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background-color: #d10000;
            border-color: #d10000;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #b50000;
            border-color: #b50000;
            transform: translateY(-2px);
        }
        
        .app-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            color: #444;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .app-link:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }
        
        .video-container {
            margin: 1.5rem 0;
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        
        .app-description {
            margin-bottom: 2rem;
            line-height: 1.7;
        }
        
        .alert-info {
            background-color: #e7f5ff;
            border-color: #b8e0ff;
            color: #0056b3;
            border-radius: 0.5rem;
        }
        
        .app-id-box {
            background-color: #f8f9fa;
            border: 1px dashed #ddd;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            text-align: center;
        }
        
        .app-id-value {
            font-weight: 700;
            font-size: 1.25rem;
            color: #d10000;
            letter-spacing: 1px;
        }

        /* Navbar styles */
        .navbar {
            padding: 0.8rem 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
        }

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
    </style>
</head>
<body>
    <!-- Navbar -->
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

            <!-- Navigation Links -->
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

    <div class="container">
        <div class="app-details-container">
            <div class="app-header">
                <h1><?php echo htmlspecialchars($appDetails['name']); ?></h1>
                <p class="mb-0">App Details & Registration Information</p>
            </div>
            <div class="app-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-3 text-center">
                        <img src="<?php echo !empty($appDetails['image_url']) ? $appDetails['image_url'] : '../../assets/img/default-app.jpg'; ?>" 
                             class="app-image" 
                             alt="<?php echo htmlspecialchars($appDetails['name']); ?>"
                             onerror="this.src='../../assets/img/default-app.jpg';">
                    </div>
                    <div class="col-md-9">
                        <h2 class="section-title">About This App</h2>
                        <div class="app-description">
                            <?php echo nl2br(htmlspecialchars($appDetails['description'])); ?>
                        </div>
                        
                        <?php if(!empty($appDetails['app_link'])): ?>
                        <h2 class="section-title">App Link</h2>
                        <div class="mb-3">
                            <a href="<?php echo htmlspecialchars($appDetails['app_link']); ?>" class="app-link" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i> Download/Access App
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Please download/install the app and create your account before proceeding with the registration.
                        </div>
                    </div>
                </div>
                
                <?php if(!empty($appDetails['tutorial_video'])): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="section-title">Tutorial Video</h2>
                        <div class="video-container">
                            <iframe src="<?php echo htmlspecialchars($appDetails['tutorial_video']); ?>" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($appDetails['app_policy'])): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="section-title">App Policy</h2>
                        <div class="p-3 bg-light rounded border">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1">App Terms & Policies</h5>
                                    <p class="mb-2 text-muted small">Please review the app's terms and policies before proceeding.</p>
                                </div>
                                <a href="/<?php echo htmlspecialchars($appDetails['app_policy']); ?>" class="btn btn-outline-danger ms-auto" target="_blank">
                                    <i class="fas fa-download me-2"></i> View Policy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="talent-registration.php?app=<?php echo urlencode($appDetails['name']); ?>&app_id=<?php echo urlencode($appDetails['id']); ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right me-2"></i> Proceed to Registration
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 