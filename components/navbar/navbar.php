<?php
/**
 * Reusable Navbar Component
 * Uses Bootstrap 5 classes with minimal custom CSS
 * 
 * Parameters:
 * $currentPage - Current active page for highlighting
 * $showCreateProfile - Show Create Profile button (default: false)
 * $createProfileText - Text for Create Profile button (default: 'Create Profile')
 * $createProfileAction - JavaScript action for Create Profile button
 * $createProfileId - ID for Create Profile button (default: 'createProfileBtn')
 */

// Set default values for parameters
$currentPage = $currentPage ?? '';
$showCreateProfile = $showCreateProfile ?? false;
$createProfileText = $createProfileText ?? 'Create Profile';
$createProfileAction = $createProfileAction ?? 'javascript:void(0)';
$createProfileId = $createProfileId ?? 'createProfileBtn';
$createProfileClasses = $createProfileClasses ?? 'btn btn-danger rounded-pill px-4 fw-semibold';
?>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap Navbar with modern styling -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center py-2" href="/">
            <img src="/logo.png" alt="Sortout Innovation" class="navbar-logo me-2">
            <span class="brand-text d-none d-md-inline">Sortout Innovation</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'home') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/" style="display: block !important; visibility: visible !important; color: #333 !important;">Home</a>
                </li>
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'about') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/pages/about-page/about.php" style="display: block !important; visibility: visible !important; color: #333 !important;">About</a>
                </li>
                <li class="nav-item dropdown" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'career') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false" style="display: block !important; visibility: visible !important; color: #333 !important;">
                        Career
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg rounded-4 mt-2">
                        <li>
                            <a class="dropdown-item rounded-3 fw-medium py-2 px-3" href="/employee-job/index.php">
                                Employee Jobs
                            </a>
                        </li>
                        <li><hr class="dropdown-divider mx-3 opacity-25"></li>
                        <li>
                            <a class="dropdown-item rounded-3 fw-medium py-2 px-3" href="/admin/artist-v2/public/index.php">
                                Artist Jobs
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'talent') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/registration.php" style="display: block !important; visibility: visible !important; color: #333 !important;">Find Talent</a>
                </li>
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'services') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/pages/our-services-page/service.php" style="display: block !important; visibility: visible !important; color: #333 !important;">Services</a>
                </li>
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'contact') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/pages/contact-page/contact-page.php" style="display: block !important; visibility: visible !important; color: #333 !important;">Contact</a>
                </li>
                <li class="nav-item" style="display: block !important; visibility: visible !important;">
                    <a class="nav-link px-3 py-2 rounded-pill text-dark position-relative <?php echo ($currentPage == 'blog') ? 'active fw-semibold bg-danger bg-opacity-10 text-danger' : 'fw-medium hover-effect'; ?>" href="/blog/index.php" style="display: block !important; visibility: visible !important; color: #333 !important;">Blog</a>
                </li>
                
                <?php if ($showCreateProfile): ?>
                <!-- Create Profile Button (Desktop) -->
                <li class="nav-item ms-2 d-none d-lg-block">
                    <button class="<?php echo $createProfileClasses; ?>" 
                            id="<?php echo $createProfileId; ?>" 
                            onclick="<?php echo $createProfileAction; ?>"
                            type="button" style="visibility: visible !important;">
                        <?php echo $createProfileText; ?>
                    </button>
                </li>
                
                <!-- Mobile Create Profile Button -->
                <li class="nav-item d-lg-none mt-2" style="visibility: visible !important;">
                    <button class="btn btn-danger w-100 rounded-pill fw-semibold" 
                            id="<?php echo $createProfileId; ?>Mobile" 
                            onclick="<?php echo $createProfileAction; ?>"
                            type="button" style="visibility: visible !important;">
                        <?php echo $createProfileText; ?>
                    </button>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Modern navbar styling -->
<style>
/* Navbar base style */
.navbar {
    font-family: 'Montserrat', sans-serif;
    transition: all 0.3s ease;
    background-color: #ffffff !important;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    min-height: 70px;
    padding: 0.5rem 0;
}

.navbar .container-fluid {
    flex-wrap: nowrap;
    align-items: center;
}

.navbar-brand {
    margin-right: 1rem;
    flex-shrink: 0;
}

.navbar.scrolled {
    padding-top: 0.3rem !important;
    padding-bottom: 0.3rem !important;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1) !important;
}

/* Logo styling */
.navbar-logo {
    height: 40px;
    width: auto;
    max-width: none;
    object-fit: contain;
}

/* Brand styling */
.brand-text {
    font-weight: 700;
    color: #d10000;
    font-size: 1.1rem;
    letter-spacing: -0.5px;
}

/* Nav links styling */
.nav-link {
    font-size: 0.85rem;
    font-weight: 500;
    letter-spacing: 0.2px;
    transition: all 0.3s ease;
    color: #333 !important;
    white-space: nowrap;
}

/* Ensure navbar nav doesn't overflow */
.navbar-nav {
    flex-wrap: nowrap;
}

/* Modern hover effect */
.hover-effect {
    transition: all 0.3s ease;
}

.hover-effect:hover {
    background-color: rgba(209, 0, 0, 0.1);
    color: #d10000 !important;
}

/* Active link styling */
.nav-link.active {
    background-color: rgba(209, 0, 0, 0.1) !important;
    color: #d10000 !important;
    font-weight: 600;
}

/* Dropdown enhancements */
.dropdown-menu {
    border-radius: 1rem;
    margin-top: 0.5rem !important;
}

.dropdown-item {
    padding: 0.7rem 1.2rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    margin: 0.2rem;
}

.dropdown-item:hover {
    background-color: rgba(209, 0, 0, 0.08);
    color: #d10000;
    transform: translateX(3px);
}

.dropdown-item:active {
    background-color: rgba(209, 0, 0, 0.12);
    color: #d10000;
}

/* Navbar toggler animation */
.navbar-toggler {
    transition: transform 0.3s ease;
    border: none !important;
    padding: 0.5rem;
}

.navbar-toggler:hover {
    transform: scale(1.05);
}

.navbar-toggler:focus {
    box-shadow: none !important;
    outline: none;
}

.navbar-toggler-icon {
    width: 1.2em;
    height: 1.2em;
}

/* Enhanced shadow effect */
.navbar.shadow-custom {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
}

/* Create Profile Button Styling */
#createProfileBtn, 
#createProfileBtnMobile {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(209, 0, 0, 0.2);
    font-size: 0.8rem;
    letter-spacing: 0.2px;
    padding: 0.5rem 0.75rem;
    white-space: nowrap;
}

#createProfileBtn:hover, 
#createProfileBtnMobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(209, 0, 0, 0.3);
    background-color: #b30000 !important;
}

#createProfileBtn:active, 
#createProfileBtnMobile:active {
    transform: translateY(0);
}

/* Body spacing */
body {
    padding-top: 75px;
}

/* Mobile optimizations */
@media (max-width: 991.98px) {
    .navbar-collapse {
        background: #ffffff;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .navbar-nav {
        flex-direction: column;
        width: 100%;
    }
    
    .nav-item {
        width: 100%;
        margin-bottom: 0.25rem;
    }
    
    .nav-link {
        padding: 0.75rem 1rem !important;
        font-size: 0.95rem !important;
        width: 100%;
        text-align: left;
        border-radius: 0.5rem !important;
    }
    
    .brand-text {
        font-size: 1rem !important;
    }
    
    /* Ensure dropdown works on mobile */
    .dropdown-menu {
        position: static !important;
        transform: none !important;
        margin-top: 0.5rem !important;
        border: none;
        box-shadow: none;
        background: rgba(0,0,0,0.02);
    }
    
    .navbar-logo {
        height: 35px;
    }
}

/* Extra small screens */
@media (max-width: 575.98px) {
    .navbar-logo {
        height: 30px;
    }
    
    .brand-text {
        font-size: 0.9rem !important;
    }
    
    .nav-link {
        font-size: 0.9rem !important;
        padding: 0.6rem 0.8rem !important;
    }
}

/* Enhanced dropdown styling */
@media (min-width: 992px) {
    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-menu {
        display: block !important;
        margin-top: 20px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        border-radius: 1rem;
    }

    .dropdown:hover .nav-link {
        background-color: rgba(209, 0, 0, 0.08);
        color: #d10000 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScrollY = window.scrollY;

    // Adjust body padding based on navbar height
    function adjustBodyPadding() {
        const navbarHeight = navbar.offsetHeight;
        document.body.style.paddingTop = navbarHeight + 'px';
    }

    adjustBodyPadding();
    window.addEventListener('resize', adjustBodyPadding);

    // Enhanced scroll behavior (simplified - no auto-hide)
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.add('shadow-custom', 'scrolled');
        } else {
            navbar.classList.remove('shadow-custom', 'scrolled');
        }
        lastScrollY = window.scrollY;
    });
});
</script> 