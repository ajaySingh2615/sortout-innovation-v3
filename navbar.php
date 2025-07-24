<?php
session_start();
?>
<nav class="bg-white shadow-md fixed top-0 left-0 right-0 z-50 font-navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="index.php" class="font-logo text-xl font-bold">
                    <img src="logo.png" alt="Logo" class="h-10">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Home</a>
                <a href="/pages/about-page/about.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">About</a>
                <div class="dropdown">
                    <a href="#" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Career <i class="fas fa-chevron-down ml-1"></i></a>
                    <div class="dropdown-menu">
                        <a href="/employee-job/index.php" class="dropdown-item">Employee Jobs</a>
                        <a href="/artist-job/index.php" class="dropdown-item">Artist Jobs</a>
                    </div>
                </div>
                <a href="/registration.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Find Talent</a>
                <a href="/pages/our-services-page/service.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Services</a>
                <a href="/pages/contact-page/contact-page.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Contact</a>
                <a href="/blog/index.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Blog</a>
            </div>

            <!-- Right Side: Create Profile Button and Mobile Menu Toggle -->
            <div class="flex items-center">
                <!-- Create Profile Button (Hidden on Small Mobile) -->
                <div class="hidden sm:flex space-x-2">
                    <a href="/create-artist-profile.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 font-semibold tracking-wide">Create Artist Profile</a>
                    <a href="/create-employee-profile.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold tracking-wide">Create Employee Profile</a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button id="mobileMenuToggle" class="md:hidden ml-4 focus:outline-none">
                    <svg id="hamburgerIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200">
        <div class="px-4 py-3 space-y-3">
            <a href="index.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Home</a>
            <a href="/pages/about-page/about.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">About</a>
            <div class="dropdown">
                <a href="#" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Career <i class="fas fa-chevron-down ml-1"></i></a>
                <div class="dropdown-menu">
                    <a href="/employee-job/index.php" class="dropdown-item">Employee Jobs</a>
                    <a href="/artist-job/index.php" class="dropdown-item">Artist Jobs</a>
                </div>
            </div>
            <a href="/registration.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Find Talent</a>
            <a href="/pages/our-services-page/service.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Services</a>
            <a href="/pages/contact-page/contact-page.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Contact</a>
            <a href="/blog/index.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Blog</a>
            
            <!-- Mobile Profile Creation Links -->
            <div class="pt-2 border-t border-gray-200">
                <a href="/create-artist-profile.php" class="block bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 font-semibold tracking-wide text-center mb-2">Create Artist Profile</a>
                <a href="/create-employee-profile.php" class="block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold tracking-wide text-center">Create Employee Profile</a>
            </div>
        </div>
    </div>
</nav>

<!-- Add space to prevent content from being hidden under the fixed navbar -->
<div class="h-16"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });
    }
    
    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (link && menu) {
            // Desktop hover behavior
            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768) { // Only on desktop
                    menu.style.display = 'block';
                }
            });
            
            dropdown.addEventListener('mouseleave', function() {
                if (window.innerWidth > 768) { // Only on desktop
                    menu.style.display = 'none';
                }
            });
            
            // Mobile click behavior
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) { // Only on mobile
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.classList.remove('active');
                        }
                    });
                    
                    dropdown.classList.toggle('active');
                }
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (mobileMenu && !mobileMenu.classList.contains('hidden') && 
            !e.target.closest('#mobileMenu') && 
            !e.target.closest('#mobileMenuToggle')) {
            mobileMenu.classList.add('hidden');
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Reset mobile menu state
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                document.body.style.overflow = '';
            }
            
            // Reset dropdown states
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
});
</script> 