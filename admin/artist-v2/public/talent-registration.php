<?php
require_once '../../../includes/db_connect.php';
include_once '../includes/functions.php';
include_once '../talent-registrations/includes/functions.php';

// Get active categories
$categories = getTalentCategories(true);

// Get app name from URL if provided
$appName = isset($_GET['app']) ? sanitize($_GET['app']) : '';
$appId = isset($_GET['app_id']) ? sanitize($_GET['app_id']) : '';

// Handle form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $fullName = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $contactNumber = isset($_POST['contact_number']) ? sanitize($_POST['contact_number']) : '';
    $gender = isset($_POST['gender']) ? sanitize($_POST['gender']) : '';
    $talentCategory = isset($_POST['talent_category']) ? sanitize($_POST['talent_category']) : '';
    $appName = isset($_POST['app_name']) ? sanitize($_POST['app_name']) : '';
    $appId = isset($_POST['app_id']) ? sanitize($_POST['app_id']) : '';
    
    // Basic validation
    if (empty($fullName)) {
        $error = 'Full name is required.';
    } elseif (empty($contactNumber)) {
        $error = 'Contact number is required.';
    } elseif (empty($gender)) {
        $error = 'Gender is required.';
    } elseif (empty($talentCategory)) {
        $error = 'Talent category is required.';
    } elseif (empty($appId)) {
        $error = 'App ID is required.';
    } else {
        // All validation passed, insert registration
        $data = [
            'full_name' => $fullName,
            'contact_number' => $contactNumber,
            'gender' => $gender,
            'talent_category' => $talentCategory,
            'app_name' => $appName,
            'app_id' => $appId,
            'status' => 'pending'
        ];
        
        $registrationId = addTalentRegistration($data);
        
        if ($registrationId) {
            $success = true;
        } else {
            $error = 'An error occurred while submitting your registration. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link
      rel="icon"
      type="image/png"
      href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif"
    />
    <title>Talent Registration - SortOut Innovation</title>
    
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
        }
        
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .form-header {
            background-color: #d10000;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        
        .form-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
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
        
        .required {
            color: #d10000;
        }
        
        .category-select {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .success-message {
            text-align: center;
            padding: 2rem;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        
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
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            width: calc(100% - 1.5rem);
            height: 2px;
            bottom: 0;
            left: 50%;
            background: linear-gradient(90deg, #d10000, #ff4b4b);
            transform: translateX(-50%);
            border-radius: 2px;
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
            background: linear-gradient(45deg, rgba(209, 0, 0, 0.05), rgba(255, 75, 75, 0.05));
            color: #d10000;
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

        /* Responsive Form Styles */
        @media (max-width: 768px) {
            .form-container {
                margin: 20px 15px;
                width: auto;
            }
            
            .form-header {
                padding: 1.5rem 1rem;
            }
            
            .form-header h1 {
                font-size: 1.5rem;
            }
            
            .form-body {
                padding: 1.5rem 1rem;
            }
            
            .gender-options {
                flex-direction: column;
                gap: 0.5rem !important;
            }
            
            .gender-options .form-check {
                margin-right: 0;
            }
        }
        
        /* Footer Section */
        .footer-section {
            background: #0a0a0a;
            color: #fff;
            padding: 60px 20px;
            font-family: Arial, sans-serif;
            margin-top: 40px;
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

        /* Responsive Design for Footer */
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

    <!-- Spacer for fixed navbar -->
    <div style="height: 76px;"></div>
    
    <div class="container">
        <div class="form-container">
            <?php if ($success): ?>
                <!-- Success Message -->
                <div class="success-message">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h2>Registration Successful!</h2>
                    <p class="lead">Thank you for registering with us. Your application has been received.</p>
                    <p>We will review your information and get back to you soon.</p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Registration Form -->
                <div class="form-header">
                    <h1>Talent Registration</h1>
                    <p>Join our talent pool and showcase your skills</p>
                </div>
                
                <div class="form-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" id="registrationForm">
                        <!-- Hidden app name field if provided in URL -->
                        <?php if (!empty($appName)): ?>
                            <input type="hidden" name="app_name" value="<?php echo htmlspecialchars($appName); ?>">
                        <?php endif; ?>
                        
                        <!-- Basic Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="form-section-title">Basic Details</h3>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Profile Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contactNumber" class="form-label">Contact Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="contactNumber" name="contact_number" placeholder="Enter contact number" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="appId" class="form-label">App ID <span class="required">*</span></label>
                                <input type="text" class="form-control" id="appId" name="app_id" placeholder="Enter App ID" required>
                                <small class="form-text text-muted">Please enter the App ID for reference.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="required">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" selected disabled>Select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Talent Category -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="form-section-title">Talent Details</h3>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="talentCategory" class="form-label">Talent Category <span class="required">*</span></label>
                                <select class="form-select category-select" id="talentCategory" name="talent_category" required>
                                    <option value="" selected disabled>Select your talent category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">Submit Registration</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
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
              <a href="https://www.facebook.com/profile.php?id=61556452066209" target="_blank"
                ><i class="fab fa-facebook"></i
              ></a>
              <a href="https://youtu.be/tw-xk-Pb-zA?si=QMTwuvhEuTegpqDr" target="_blank"
                ><i class="fab fa-youtube"></i
              ></a>
              <a href="https://www.linkedin.com/company/sortout-innovation/" target="_blank"
                ><i class="fab fa-linkedin"></i
              ></a>
              <a href="https://www.instagram.com/sortout_innovation" target="_blank"
                ><i class="fab fa-instagram"></i
              ></a>
            </div>
          </div>
        </div>

        <!-- Copyright & Legal Links -->
        <div class="footer-bottom">
          <p>&copy; 2025 SortOut Innovation. All Rights Reserved.</p>
          <ul>
            <li><a href="/privacy-policy">Privacy Policy</a></li>
            <li><a href="/terms">Terms & Conditions</a></li>
          </ul>
        </div>
      </div>
    </footer>
    
    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Registration</h5>
                    <p>By submitting your information through this form, you are registering to be considered as a talent for our platform.</p>
                    
                    <h5>2. Eligibility</h5>
                    <p>You must be at least 18 years old to register as a talent unless applying for the "Child Artist" category, which requires parental consent.</p>
                    
                    <h5>3. Accuracy of Information</h5>
                    <p>You certify that all information provided is accurate and complete. Providing false information may result in rejection of your application.</p>
                    
                    <h5>4. Privacy Policy</h5>
                    <p>Your personal information will be handled in accordance with our Privacy Policy. We may contact you using the details provided.</p>
                    
                    <h5>5. No Guarantee of Selection</h5>
                    <p>Registration does not guarantee selection. We reserve the right to select talents based on our criteria and requirements.</p>
                    
                    <h5>6. Changes to Terms</h5>
                    <p>We reserve the right to modify these terms at any time. Continued use of our services implies acceptance of updated terms.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
    </script>
</body>
</html> 