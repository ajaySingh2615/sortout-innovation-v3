<?php
// Check if session is available and start it if possible
if (function_exists('session_start')) {
session_start();
} else {
    // If session is not available, create a simple message storage
    if (!isset($GLOBALS['messages'])) {
        $GLOBALS['messages'] = [];
    }
}

require './includes/db_connect.php';

// ✅ Store success message (if any)
$message = "";
if (function_exists('session_start')) {
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "";
unset($_SESSION['message']); // Clear the session message after displaying
} else {
    $message = isset($GLOBALS['messages']['success']) ? $GLOBALS['messages']['success'] : "";
    unset($GLOBALS['messages']['success']); // Clear the message after displaying
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>Models Agency</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS -->
      <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Add these Google Fonts to the head section -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>

  /* ✅ Ensure modals are hidden on page load */
#profileSelectionModal,
#addClientModal {
    display: none; /* Hide initially */
}

/* ✅ Fix modal positioning and ensure it's above all content */
#profileSelectionModal,
#addClientModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999; /* Keep modal always on top */
    background-color: rgba(0, 0, 0, 0.7); /* Dark overlay */
    display: none; /* Hide until triggered */
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
}

/* ✅ Show modal when it's active */
#profileSelectionModal.flex,
#addClientModal.flex {
    display: flex;
    opacity: 1;
    visibility: visible;
}

/* ✅ Ensure modal content is centered */
#profileSelectionModal > div,
#addClientModal > div {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    text-align: center;
    transform: scale(0.9);
    transition: transform 0.3s ease-in-out;
}

/* ✅ Ensure smooth scale-in effect */
#profileSelectionModal.flex > div,
#addClientModal.flex > div {
    transform: scale(1);
}

/* ✅ Button Styling */
.modal-button {
    font-size: 18px;
    font-weight: 600;
    padding: 12px;
    transition: all 0.3s ease-in-out;
    border-radius: 8px;
    width: 100%;
}

/* ✅ Hover Effects */
.modal-button:hover {
    transform: scale(1.05);
}

/* ✅ Close Button */
.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    font-weight: bold;
    color: #e63946;
    cursor: pointer;
}

/* ✅ Prevent background scrolling when modal is open */
body.modal-open {
    overflow: hidden;
}

/* ✅ Responsive Design */
@media (max-width: 768px) {
    #profileSelectionModal > div,
    #addClientModal > div {
        max-width: 90%;
    }
    
    /* ✅ Mobile-specific file input styles */
    #addClientModal input[type="file"] {
        padding: 16px;
        font-size: 16px; /* Prevent zoom on iOS */
        border-radius: 12px;
    }
    
    /* ✅ Enhance camera/gallery selection on mobile */
    #artistImage, #employeeImage {
        padding: 20px !important;
        min-height: 60px;
        text-align: center;
    }
    
    /* ✅ Better touch targets for mobile */
    #addClientModal .form-group label {
        font-size: 16px;
        line-height: 1.5;
    }
}

/* ✅ Additional mobile styles for smaller devices */
@media (max-width: 480px) {
    #addClientModal > div {
        max-width: 95%;
        padding: 20px;
    }
    
    #addClientModal input[type="file"] {
        padding: 18px;
        font-size: 14px;
    }
    
    /* ✅ Adjust file input button styling for mobile */
    #artistImage::-webkit-file-upload-button,
    #employeeImage::-webkit-file-upload-button {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-right: 12px;
    }
}

/* ✅ Center Modal & Enhance Appearance */
#addClientModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
}

/* ✅ Show Modal */
#addClientModal.flex {
    opacity: 1;
    visibility: visible;
}

/* ✅ Stylish Form Container */
#addClientModal > div {
    background: white;
    padding: 28px;
    border-radius: 15px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
    max-width: 450px;
    text-align: center;
    transform: scale(0.9);
    transition: transform 0.3s ease-in-out;
}

/* ✅ Animate Modal Appearance */
#addClientModal.flex > div {
    transform: scale(1);
}

/* ✅ Enhanced Input & Select Fields */
#addClientModal input,
#addClientModal select {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    transition: all 0.3s ease-in-out;
    background-color: white;
    font-size: 14px;
}

/* ✅ Enhanced Focus Effect on Inputs */
#addClientModal input:focus,
#addClientModal select:focus {
    border: 2px solid #ef4444;
    outline: none;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    transform: translateY(-1px);
}

/* ✅ File Input Styling */
#addClientModal input[type="file"] {
    border: 2px dashed #d1d5db;
    background-color: #f9fafb;
    padding: 20px;
}

#addClientModal input[type="file"]:focus {
    border-color: #ef4444;
    background-color: #fef2f2;
}

/* ✅ Multi-select Styling */
#addClientModal select[multiple] {
    min-height: 120px;
    padding: 12px;
}

/* ✅ Section Headers */
.form-section-header {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-left: 4px solid #ef4444;
}

/* ✅ Enhanced Labels */
#addClientModal label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

/* ✅ Info Text Styling */
#addClientModal .text-gray-500 {
    background-color: white;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

/* ✅ Submit Button Styling */
#addClientModal button[type="submit"] {
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    background-color: #28a745;
    color: white;
    border-radius: 8px;
    transition: background 0.3s ease-in-out;
}

#addClientModal button[type="submit"]:hover {
    background-color: #218838;
}

/* ✅ Responsive Design */
@media (max-width: 768px) {
    #addClientModal > div {
        max-width: 90%;
    }
}

  /* Font families */
  .font-poppins { font-family: 'Poppins', sans-serif; }
  .font-montserrat { font-family: 'Montserrat', sans-serif; }
  .font-playfair { font-family: 'Playfair Display', serif; }
  .font-inter { font-family: 'Inter', sans-serif; }
  
  /* Client section styling */
  .clients-section {
    background-color: #f8f9fa;
    padding: 3rem 0;
  }
  
  .clients-section-title {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
    text-align: center;
  }
  
  .clients-section-subtitle {
    font-family: 'Inter', sans-serif;
    font-weight: 400;
    color: #666;
    margin-bottom: 2.5rem;
    text-align: center;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
  }
  
  /* Client card enhancements */
  .client-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .client-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
  
    .client-name {
    font-family: 'Playfair Display', serif;
    }
  
    .client-category {
    font-family: 'Inter', sans-serif;
  }
  
  .client-badge {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
  }
  
  /* Filter styling */
  .filter-label {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 0.875rem;
        color: #555;
    }

  .filter-select {
    font-family: 'Inter', sans-serif;
    transition: all 0.2s ease;
  }
  
  .filter-select:focus {
    border-color: #ff4757;
    box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.2);
  }
  
  /* Toggle button styling */
  .toggle-button {
    font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

  .toggle-button.active {
    background-color: #ff4757;
    color: white;
  }
  
  .toggle-button:not(.active) {
    background-color: #f1f1f1;
    color: #666;
  }
  
  /* Category section styles */
  .categories-section {
    position: relative;
    padding: 6rem 0;
    background-color: #f8f9fa;
    overflow: hidden;
  }
  
  .categories-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('/images/background-image-2.jpg');
    background-size: cover;
    background-position: center;
    filter: brightness(0.2);
    z-index: 0;
  }
  
  .categories-title {
    font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: white;
    margin-bottom: 1rem;
    position: relative;
    display: inline-block;
  }
  
  .categories-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(to right, #ff4757, #ff6b81);
  }
  
  .categories-subtitle {
    font-family: 'Inter', sans-serif;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 3rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }
  
  .category-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  }
  
  .category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, var(--category-color-from), var(--category-color-to));
    opacity: 0.9;
    z-index: 1;
    transition: opacity 0.3s ease;
  }
  
  .category-card:hover::before {
    opacity: 1;
  }
  
  .category-card-content {
    position: relative;
    z-index: 2;
    padding: 1.5rem;
    width: 100%;
  }
  
  .category-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: white;
    transition: transform 0.3s ease;
  }
  
  .category-card:hover .category-icon {
    transform: scale(1.1);
  }
  
  .category-name {
    font-family: 'Poppins', sans-serif;
        font-weight: 600;
    font-size: 1.25rem;
    color: white;
    margin: 0;
  }
  
  .category-count {
    font-family: 'Inter', sans-serif;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.9);
    margin-top: 0.5rem;
  }
  
  /* Trusted clients section styles */
  .trusted-clients-section {
    background: linear-gradient(to right, #f8f9fa, #ffffff, #f8f9fa);
    padding: 5rem 0;
    position: relative;
    overflow: hidden;
  }
  
  .trusted-clients-title {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
        color: #333;
    margin-bottom: 1rem;
    position: relative;
    display: inline-block;
  }
  
  .trusted-clients-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(to right, #ff4757, #ff6b81);
  }
  
  .trusted-clients-subtitle {
    font-family: 'Inter', sans-serif;
    color: #666;
    margin-bottom: 3rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }
  
  .client-logo-container {
        display: flex;
    align-items: center;
        justify-content: center;
    padding: 1.5rem;
    border-radius: 12px;
    background-color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 120px;
    width: 200px;
    position: relative;
    overflow: hidden;
  }
  
  .client-logo-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
  
    .client-logo {
    max-width: 80%;
    max-height: 60px;
    object-fit: contain;
    filter: grayscale(0%);
    opacity: 1;
    transition: all 0.3s ease;
  }
  
  .client-logo-container:hover .client-logo {
    filter: grayscale(0%);
        opacity: 1;
    }

  /* Update the logo scroll container and animation styles */
  .logo-scroll-container {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 1.5rem 0;
    margin: 0 auto;
    max-width: 1200px;
  }
  
  .logo-scroll {
        display: flex;
    animation: scroll 30s linear infinite;
    width: max-content;
  }
  
  .logo-scroll-reverse {
    display: flex;
    animation: scroll-reverse 30s linear infinite;
    width: max-content;
  }
  
  .logo-scroll-item {
    flex: 0 0 auto;
    margin: 0 1.5rem;
  }
  
  @keyframes scroll {
    0% {
      transform: translateX(0);
    }
    100% {
      transform: translateX(calc(-250px * 5));
    }
  }
  
  @keyframes scroll-reverse {
    0% {
      transform: translateX(calc(-250px * 5));
    }
    100% {
      transform: translateX(0);
    }
  }
  
  .logo-scroll-container::before,
  .logo-scroll-container::after {
    content: '';
    position: absolute;
    top: 0;
    width: 100px;
    height: 100%;
    z-index: 2;
  }
  
  .logo-scroll-container::before {
    left: 0;
    background: linear-gradient(to right, #f8f9fa, transparent);
  }
  
  .logo-scroll-container::after {
    right: 0;
    background: linear-gradient(to left, #f8f9fa, transparent);
  }
  
  /* Ensure client logo containers have consistent size */
  .client-logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
    padding: 1.5rem;
    border-radius: 12px;
    background-color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 120px;
    width: 200px;
    position: relative;
    overflow: hidden;
  }
  
  /* Mobile optimizations */
  @media (max-width: 768px) {
    .trusted-clients-section {
      padding: 3rem 0;
    }
    
    .client-logo-container {
      height: 100px;
      padding: 1rem;
    }
    
    .client-logo {
      max-height: 50px;
    }
    
    .client-card {
      -webkit-tap-highlight-color: transparent;
    }
    
    .toggle-button {
      padding: 12px 16px;
      min-height: 48px;
    }
    
    .filter-group {
      margin-bottom: 16px;
    }
    
    select, input, button {
      min-height: 48px;
      font-size: 16px;
    }
    
    .fixed-bottom {
      position: sticky;
      bottom: 0;
      z-index: 10;
    }
    
    .client-overlay {
        padding: 16px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    
    .client-card {
      margin-bottom: 16px;
    }
    
    .client-badge {
      font-size: 10px;
      padding: 4px 8px;
    }
    
    .client-name {
      font-size: 18px;
    }
    
    .pull-refresh-hint {
      text-align: center;
      color: #888;
            padding: 12px;
      font-size: 14px;
      display: none;
    }
    
    .pull-refresh-hint {
      display: block;
    }
  }

  /* Add custom positioning for banner images */
  .banner-image {
      object-fit: cover;
      width: 100%;
      height: 100%;
      object-position: center;
  }
  
  /* Mobile-specific styling to show the center-left part of the image */
  @media (max-width: 768px) {
      .banner-image {
          object-position: 25% center; /* This positions the image 25% from the left */
      }
  }
  
  /* Extra-small devices */
  @media (max-width: 480px) {
      .banner-image {
          object-position: 30% center; /* This positions the image 30% from the left */
      }
  }

  /* Banner container with correct aspect ratio */
  .banner-container {
    position: relative;
    width: 100%;
    /* Maintain the exact 3750:1250 aspect ratio */
    padding-top: 33.33%; /* (1250/3750 * 100%) */
    height: 0;
    overflow: hidden;
  }

  /* Banner images */
  .banner-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Adjust banner position on smaller screens */
    @media (max-width: 768px) {
    .banner-image {
        object-position: 25% center;
    }
  }

  @media (max-width: 480px) {
    .banner-image {
        object-position: 30% center;
    }
  }

  /* Additional mobile fixes */
  @media (max-width: 480px) {
    /* Improve tap targets for navigation arrows */
    .banner-container button {
        -webkit-tap-highlight-color: transparent;
        cursor: pointer;
        touch-action: manipulation;
    }
    
    /* Ensure arrows are above content and have adequate tap area */
    #prevBtn, #nextBtn {
        z-index: 30;
        min-width: 40px;
        min-height: 40px;
    }
    
    /* Fix indicators size and appearance */
    .banner-container .indicator,
    button.indicator,
    .indicator {
        width: 6px !important;
        height: 6px !important;
        min-width: 6px !important;
        min-height: 6px !important;
        max-width: 6px !important;
        max-height: 6px !important;
        border-radius: 50% !important;
        display: block !important;
        padding: 0 !important;
        margin: 0 4px !important;
    }
    
    .banner-container .indicator.active,
    button.indicator.active,
    .indicator.active {
        transform: scale(1.1) !important;
        background-color: white !important;
        width: 6px !important;
        height: 6px !important;
    }
}

/* Additional arrow fixes for mobile */
.nav-arrow {
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

@media (max-width: 480px) {
    .nav-arrow {
        background-color: rgba(0,0,0,0.7) !important;
        width: 36px !important;
        height: 36px !important;
        box-shadow: 0 0 15px rgba(0,0,0,0.5);
    }
}
    
/* Add dropdown styles */
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
    background: white;
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

/* ✅ Notification styles */
.notification {
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

</style>


</head>
<body class="bg-gray-100">

    <!-- ✅ Success Message Modal -->
<div id="successMessage" 
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform scale-95 opacity-0 transition-all duration-300">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-500"></i>
</div>

                <!-- Success Message -->
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Profile Submitted Successfully!</h3>
                <p class="text-gray-600 mb-3">Thank you for submitting your profile. Our admin team will review your submission shortly.</p>
                
                <!-- Status Timeline -->
                <div class="flex items-center justify-center mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div class="h-1 w-12 bg-gray-300"></div>
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="h-1 w-12 bg-gray-300"></div>
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-check text-white"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Status Labels -->
                <div class="grid grid-cols-3 gap-4 text-xs text-gray-500 mb-6">
                    <div>Submitted</div>
                    <div>Under Review</div>
                    <div>Approved</div>
                </div>
                
                <!-- Action Button -->
                <button onclick="closeSuccessMessage()" 
                        class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors duration-200 w-full">
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <!-- ✅ Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 right-0 z-50 font-navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- 🔹 Left: Logo -->
                <div class="flex-shrink-0">
                    <a href="index.php" class="font-logo text-xl font-bold">
                        <img src="logo.png" alt="Logo" class="h-10"> <!-- Change logo.png -->
                    </a>
                </div>

                <!-- 🔹 Center: Navigation Links (Hidden on Mobile) -->
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Home</a>
                    <a href="/pages/about-page/about.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">About</a>
                    <div class="dropdown">
                        <a href="#" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Career <i class="fas fa-chevron-down ml-1"></i></a>
                        <div class="dropdown-menu">
                            <a href="/employee-job/index.php" class="dropdown-item">Employee Jobs</a>
                            <a href="/admin/artist-v2/public/index.php" class="dropdown-item">Artist Jobs</a>
                        </div>
                    </div>
                    <a href="/modal_agency.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Find Talent</a>
                    <a href="/pages/our-services-page/service.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Services</a>
                    <a href="/pages/contact-page/contact-page.html" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Contact</a>
                    <a href="/blog/index.php" class="text-gray-700 hover:text-red-500 transition duration-300 font-medium tracking-wide nav-link">Blog</a>
                </div>

                <!-- 🔹 Right: Add Client Button and Mobile Menu Toggle -->
                <div class="flex items-center">
                    <!-- ✅ Create Profile Button (Hidden on Small Mobile) -->
                    <button id="createProfileBtn" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 hidden sm:block font-semibold tracking-wide">
    Create Profile
</button>

                    <!-- ✅ Hamburger Menu Toggle for Mobile -->
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

        <!-- ✅ Mobile Menu (Hidden by Default) -->
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-4 py-3 space-y-3">
                <a href="index.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Home</a>
                <a href="/pages/about-page/about.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">About</a>
                <div class="dropdown">
                    <a href="#" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Career <i class="fas fa-chevron-down ml-1"></i></a>
                    <div class="dropdown-menu">
                        <a href="/employee-job/index.php" class="dropdown-item">Employee Jobs</a>
                        <a href="/admin/artist-v2/public/index.php" class="dropdown-item">Artist Jobs</a>
                    </div>
                </div>
                <a href="/modal_agency.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Find Talent</a>
                <a href="/pages/our-services-page/service.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Services</a>
                <a href="/pages/contact-page/contact-page.html" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Contact</a>
                <a href="/blog/index.php" class="block text-gray-700 hover:text-red-500 transition duration-300 py-2 font-medium tracking-wide">Blog</a>
                
                <!-- Create Profile Button for Mobile -->
                <div class="pt-4 mt-2 border-t border-gray-200">
                    <button id="mobileCreateProfileBtn" class="w-full bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition duration-300 font-semibold tracking-wide text-center">
                        Create Profile
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Add space to prevent content from being hidden under the fixed navbar -->
    <div class="h-16"></div>

    <!-- Hero Banner Section -->
    <div class="relative w-full overflow-hidden banner-container" id="heroBanner">
        <!-- Banner Slides -->
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-100" id="slide1">
            <img src="/images/agency-banner/first-banner.jpg" alt="Agency Banner 1" class="banner-image">
        </div>
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-0" id="slide2">
            <img src="/images/agency-banner/banner-2.jpg" alt="Agency Banner 2" class="banner-image">
        </div>
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-0" id="slide3">
            <img src="/images/agency-banner/banner-3.jpg" alt="Agency Banner 3" class="banner-image">
        </div>
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-0" id="slide4">
            <img src="/images/agency-banner/banner-4.jpg" alt="Agency Banner 4" class="banner-image">
        </div>
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-0" id="slide5">
            <img src="/images/agency-banner/banner-5.jpg" alt="Agency Banner 5" class="banner-image">
        </div>
        <div class="absolute inset-0 w-full h-full transition-opacity duration-1000 opacity-0" id="slide6">
            <img src="/images/agency-banner/banner-1.jpg" alt="Agency Banner 6" class="banner-image">
        </div>
        
        
        <!-- Navigation Arrows -->
        <button class="absolute z-30 flex items-center justify-center w-10 h-10 text-white transform -translate-y-1/2 rounded-full md:w-12 md:h-12 bg-black/60 left-2 md:left-4 top-1/2 hover:bg-black/70 focus:outline-none nav-arrow" id="prevBtn">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button class="absolute z-30 flex items-center justify-center w-10 h-10 text-white transform -translate-y-1/2 rounded-full md:w-12 md:h-12 bg-black/60 right-2 md:right-4 top-1/2 hover:bg-black/70 focus:outline-none nav-arrow" id="nextBtn">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        
        <!-- Indicators -->
        <div class="absolute bottom-4 left-0 right-0 z-10 flex justify-center space-x-2 md:space-x-2 space-x-1">
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator active" data-slide="0"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="1"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="2"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="3"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="4"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="5"></button>
            <button class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 indicator" data-slide="6"></button>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = [
        document.getElementById('slide1'),
        document.getElementById('slide2'),
        document.getElementById('slide3'),
        document.getElementById('slide4'),
        document.getElementById('slide5'),
        document.getElementById('slide6')
    ];
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    let currentSlide = 0;
    let interval;
    
    // Show slide
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('opacity-100');
            slide.classList.add('opacity-0');
        });
        
        // Update indicators
        indicators.forEach(indicator => {
            indicator.classList.remove('active', 'bg-white');
            indicator.classList.add('bg-white/50');
        });
        
        // Show selected slide
        slides[index].classList.remove('opacity-0');
        slides[index].classList.add('opacity-100');
        
        // Update indicator
        indicators[index].classList.add('active', 'bg-white');
        indicators[index].classList.remove('bg-white/50');
        
        currentSlide = index;
    }
    
    // Start slideshow
    function startSlideshow() {
        interval = setInterval(() => {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }, 5000);
    }
    
    // Stop slideshow
    function stopSlideshow() {
        clearInterval(interval);
    }
    
    // Next slide
    function nextSlide() {
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
        
        // Reset timer
        stopSlideshow();
        startSlideshow();
    }
    
    // Previous slide
    function prevSlide() {
        let prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
        
        // Reset timer
        stopSlideshow();
        startSlideshow();
    }
    
    // Set up event listeners
    prevBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        prevSlide();
    }, { passive: false });

    nextBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        nextSlide();
    }, { passive: false });

    // Also add touch events specifically for arrow buttons
    prevBtn.addEventListener('touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        prevSlide();
    }, { passive: false });

    nextBtn.addEventListener('touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        nextSlide();
    }, { passive: false });
    
    // Indicator click
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            stopSlideshow();
            startSlideshow();
        });
    });
    
    // Touch support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    const heroBanner = document.getElementById('heroBanner');
    
    heroBanner.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    heroBanner.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        
        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - next slide
            nextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - previous slide
            prevSlide();
        }
    }
    
    // Start the slideshow
    startSlideshow();
});
</script>

   <!-- ✅ Create Profile Selection Modal -->
<div id="profileSelectionModal" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm md:max-w-md lg:max-w-lg transform scale-95 transition duration-300">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Choose Profile Type</h2>

        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <button id="artistButton" class="w-full md:w-1/2 px-6 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 transition-all duration-200">
                🎭 Artist
            </button>
            <button id="employeeButton" class="w-full md:w-1/2 px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition-all duration-200">
                💼 Employee
            </button>
        </div>

        <button id="closeSelectionBtn" class="mt-6 w-full text-red-500 font-semibold hover:underline text-center">Cancel</button>
    </div>
</div>

 <!-- ✅ Profile Submission Form Modal -->
<div id="addClientModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative border border-gray-100">
        <button id="closeClientFormBtn" class="absolute top-6 right-6 text-gray-400 hover:text-red-500 text-3xl font-bold transition-colors duration-200 z-10">&times;</button>
            
            <!-- Enhanced Header -->
            <div class="text-center mb-8 pt-2">
                <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Create Your Profile</h2>
                <p class="text-gray-600">Join our talent network and showcase your skills</p>
            </div>

            <form id="addClientForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Professional Type (Hidden) -->
            <input type="hidden" id="professionalField" name="professional" value="">

            <!-- Basic Information Section -->
            <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-red-500 mr-2"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card text-gray-400 mr-1"></i>
                            Full Name *
                        </label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                               placeholder="Enter your full name">
                    </div>

                    <!-- Age -->
                    <div class="form-group">
                        <label for="age" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake text-gray-400 mr-1"></i>
                            Age *
                        </label>
                        <input type="number" id="age" name="age" required min="18" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                               placeholder="Enter your age">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                            Phone Number *
                        </label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                               placeholder="Enter 10-digit phone number">
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-venus-mars text-gray-400 mr-1"></i>
                            Gender *
                        </label>
                        <select id="gender" name="gender" required 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-blue-50 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                    Location Information
                </h3>
                
                <!-- City -->
                <div class="form-group">
                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-city text-gray-400 mr-1"></i>
                        City *
                    </label>
                    <select id="city" name="city" required 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                    <option value="">Select City</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Bangalore">Bangalore</option>
                    <option value="Hyderabad">Hyderabad</option>
                    <option value="Ahmedabad">Ahmedabad</option>
                    <option value="Chennai">Chennai</option>
                    <option value="Kolkata">Kolkata</option>
                    <option value="Surat">Surat</option>
                    <option value="Pune">Pune</option>
                    <option value="Jaipur">Jaipur</option>
                    <option value="Lucknow">Lucknow</option>
                    <option value="Kanpur">Kanpur</option>
                    <option value="Nagpur">Nagpur</option>
                    <option value="Indore">Indore</option>
                    <option value="Thane">Thane</option>
                    <option value="Bhopal">Bhopal</option>
                    <option value="Visakhapatnam">Visakhapatnam</option>
                    <option value="Pimpri and Chinchwad">Pimpri and Chinchwad</option>
                    <option value="Patna">Patna</option>
                    <option value="Vadodara">Vadodara</option>
                    <option value="Ghaziabad">Ghaziabad</option>
                    <option value="Ludhiana">Ludhiana</option>
                    <option value="Agra">Agra</option>
                    <option value="Nashik">Nashik</option>
                    <option value="Faridabad">Faridabad</option>
                    <option value="Meerut">Meerut</option>
                    <option value="Rajkot">Rajkot</option>
                    <option value="Kalyan and Dombivali">Kalyan and Dombivali</option>
                    <option value="Vasai Virar">Vasai Virar</option>
                    <option value="Varanasi">Varanasi</option>
                    <option value="Srinagar">Srinagar</option>
                    <option value="Aurangabad">Aurangabad</option>
                    <option value="Dhanbad">Dhanbad</option>
                    <option value="Amritsar">Amritsar</option>
                    <option value="Navi Mumbai">Navi Mumbai</option>
                    <option value="Allahabad">Allahabad</option>
                    <option value="Ranchi">Ranchi</option>
                    <option value="Gwalior">Gwalior</option>
                    <option value="Jabalpur">Jabalpur</option>
                    <option value="Coimbatore">Coimbatore</option>
                    <option value="Vijayawada">Vijayawada</option>
                    <option value="Jodhpur">Jodhpur</option>
                    <option value="Madurai">Madurai</option>
                    <option value="Raipur">Raipur</option>
                    <option value="Kota">Kota</option>
                    <option value="Chandigarh">Chandigarh</option>
                    <option value="Guwahati">Guwahati</option>
                    <option value="Solapur">Solapur</option>
                    <option value="Hubli and Dharwad">Hubli and Dharwad</option>
                    <option value="Bareilly">Bareilly</option>
                    <option value="Mysore">Mysore</option>
                    <option value="Moradabad">Moradabad</option>
                    <option value="Gurgaon">Gurgaon</option>
                    <option value="Aligarh">Aligarh</option>
                    <option value="Jalandhar">Jalandhar</option>
                    <option value="Tiruchirappalli">Tiruchirappalli</option>
                    <option value="Bhubaneswar">Bhubaneswar</option>
                    <option value="Salem">Salem</option>
                    <option value="Mira and Bhayander">Mira and Bhayander</option>
                    <option value="Thiruvananthapuram">Thiruvananthapuram</option>
                    <option value="Bhiwandi">Bhiwandi</option>
                    <option value="Saharanpur">Saharanpur</option>
                    <option value="Gorakhpur">Gorakhpur</option>
                    <option value="Guntur">Guntur</option>
                    <option value="Amravati">Amravati</option>
                    <option value="Bikaner">Bikaner</option>
                    <option value="Noida">Noida</option>
                    <option value="Jamshedpur">Jamshedpur</option>
                    <option value="Bhilai Nagar">Bhilai Nagar</option>
                    <option value="Warangal">Warangal</option>
                    <option value="Cuttack">Cuttack</option>
                    <option value="Firozabad">Firozabad</option>
                    <option value="Kochi">Kochi</option>
                    <option value="Bhavnagar">Bhavnagar</option>
                    <option value="Dehradun">Dehradun</option>
                    <option value="Durgapur">Durgapur</option>
                    <option value="Asansol">Asansol</option>
                    <option value="Nanded Waghala">Nanded Waghala</option>
                    <option value="Kolapur">Kolapur</option>
                    <option value="Ajmer">Ajmer</option>
                    <option value="Gulbarga">Gulbarga</option>
                    <option value="Jamnagar">Jamnagar</option>
                    <option value="Ujjain">Ujjain</option>
                    <option value="Loni">Loni</option>
                    <option value="Siliguri">Siliguri</option>
                    <option value="Jhansi">Jhansi</option>
                    <option value="Ulhasnagar">Ulhasnagar</option>
                    <option value="Nellore">Nellore</option>
                    <option value="Jammu">Jammu</option>
                    <option value="Sangli Miraj Kupwad">Sangli Miraj Kupwad</option>
                    <option value="Belgaum">Belgaum</option>
                    <option value="Mangalore">Mangalore</option>
                    <option value="Ambattur">Ambattur</option>
                    <option value="Tirunelveli">Tirunelveli</option>
                    <option value="Malegaon">Malegaon</option>
                    <option value="Gaya">Gaya</option>
                    <option value="Jalgaon">Jalgaon</option>
                    <option value="Udaipur">Udaipur</option>
                    <option value="Maheshtala">Maheshtala</option>
                    <option value="Tiruppur">Tiruppur</option>
                    <option value="Davanagere">Davanagere</option>
                    <option value="Kozhikode">Kozhikode</option>
                    <option value="Kurnool">Kurnool</option>
                </select>
                </div>
            </div>

            <!-- Artist-specific Fields -->
            <div id="artistFields" style="display: none;">
                <!-- Contact Information for Artists -->
                <div class="bg-purple-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-envelope text-purple-500 mr-2"></i>
                        Contact Information
                    </h3>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-at text-gray-400 mr-1"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                               placeholder="Enter your email address">
                    </div>
                </div>

                <!-- Professional Information for Artists -->
                <div class="bg-green-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-star text-green-500 mr-2"></i>
                        Professional Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tags text-gray-400 mr-1"></i>
                                Category
                            </label>
                            <select id="category" name="category" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                        <option value="">Select Category</option>
                        <option value="Dating App Host">Dating App Host</option>
                        <option value="Video Live Streamers">Video Live Streamers</option>
                        <option value="Voice Live Streamers">Voice Live Streamers</option>
                        <option value="Singer">Singer</option>
                        <option value="Dancer">Dancer</option>
                        <option value="Actor / Actress">Actor / Actress</option>
                        <option value="Model">Model</option>
                        <option value="Artist / Painter">Artist / Painter</option>
                        <option value="Social Media Influencer">Social Media Influencer</option>
                        <option value="Content Creator">Content Creator</option>
                        <option value="Vlogger">Vlogger</option>
                        <option value="Gamer / Streamer">Gamer / Streamer</option>
                        <option value="YouTuber">YouTuber</option>
                        <option value="Anchor / Emcee / Host">Anchor / Emcee / Host</option>
                        <option value="DJ / Music Producer">DJ / Music Producer</option>
                        <option value="Photographer / Videographer">Photographer / Videographer</option>
                        <option value="Makeup Artist / Hair Stylist">Makeup Artist / Hair Stylist</option>
                        <option value="Fashion Designer / Stylist">Fashion Designer / Stylist</option>
                        <option value="Fitness Trainer / Yoga Instructor">Fitness Trainer / Yoga Instructor</option>
                        <option value="Motivational Speaker / Life Coach">Motivational Speaker / Life Coach</option>
                        <option value="Chef / Culinary Artist">Chef / Culinary Artist</option>
                        <option value="Child Artist">Child Artist</option>
                        <option value="Pet Performer / Pet Model">Pet Performer / Pet Model</option>
                        <option value="Instrumental Musician">Instrumental Musician</option>
                        <option value="Director / Scriptwriter / Editor">Director / Scriptwriter / Editor</option>
                        <option value="Voice Over Artist">Voice Over Artist</option>
                        <option value="Magician / Illusionist">Magician / Illusionist</option>
                        <option value="Stand-up Comedian">Stand-up Comedian</option>
                        <option value="Mimicry Artist">Mimicry Artist</option>
                        <option value="Poet / Storyteller">Poet / Storyteller</option>
                        <option value="Language Trainer / Public Speaking Coach">Language Trainer / Public Speaking Coach</option>
                        <option value="Craft Expert / DIY Creator">Craft Expert / DIY Creator</option>
                        <option value="Travel Blogger / Explorer">Travel Blogger / Explorer</option>
                        <option value="Astrologer / Tarot Reader">Astrologer / Tarot Reader</option>
                        <option value="Educator / Subject Matter Expert">Educator / Subject Matter Expert</option>
                        <option value="Tech Reviewer / Gadget Expert">Tech Reviewer / Gadget Expert</option>
                        <option value="Unboxing / Product Reviewer">Unboxing / Product Reviewer</option>
                        <option value="Business Coach / Startup Mentor">Business Coach / Startup Mentor</option>
                        <option value="Health & Wellness Coach">Health & Wellness Coach</option>
                        <option value="Event Anchor / Wedding Host">Event Anchor / Wedding Host</option>
                    </select>
                </div>

                        <!-- Types of Influencers Based on Category -->
                        <div class="form-group">
                            <label for="influencer_category" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-users text-gray-400 mr-1"></i>
                                Influencer Category
                            </label>
                            <select id="influencer_category" name="influencer_category" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                        <option value="">Select Influencer Category</option>
                        <option value="Video Content Creators">Video Content Creators</option>
                        <option value="Fashion Influencers">Fashion Influencers</option>
                        <option value="Beauty Model for shooting">Beauty Model for shooting</option>
                        <option value="Fitness and Health Influencers">Fitness and Health Influencers</option>
                        <option value="Lifestyle Influencers">Lifestyle Influencers</option>
                        <option value="Travel Influencers">Travel Influencers</option>
                        <option value="Food Influencers">Food Influencers</option>
                        <option value="Gaming Influencers">Gaming Influencers</option>
                        <option value="Tech Influencers">Tech Influencers</option>
                        <option value="Mobile Live Streaming Model">Mobile Live Streaming Model</option>
                        <option value="Music and Performing Arts Influencers">Music and Performing Arts Influencers</option>
                        <option value="Motivational Speakers and Self-Improvement Influencers">Motivational Speakers and Self-Improvement Influencers</option>
                        <option value="Comedy and Entertainment Influencers">Comedy and Entertainment Influencers</option>
                        <option value="Parenting and Family Influencers">Parenting and Family Influencers</option>
                        <option value="Art and Design Influencers">Art and Design Influencers</option>
                        <option value="Activists and Advocates">Activists and Advocates</option>
                        <option value="Niche Influencers">Niche Influencers</option>
                        <option value="Night Club Model">Night Club Model</option>
                        <option value="Party Welcome Model">Party Welcome Model</option>
                        <option value="Party Waiter Girls">Party Waiter Girls</option>
                    </select>
                </div>

                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 mt-6">
                        <!-- Types of Influencers by Follower Count -->
                        <div class="form-group">
                            <label for="influencer_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-chart-line text-gray-400 mr-1"></i>
                                Influencer Type by Followers
                            </label>
                            <select id="influencer_type" name="influencer_type" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                                <option value="">Select Influencer Type</option>
                                <option value="Mega-influencers – with more than a million followers (think celebrities)">Mega-influencers – with more than a million followers (think celebrities)</option>
                                <option value="Macro-influencers – with 500K to 1 million followers">Macro-influencers – with 500K to 1 million followers</option>
                                <option value="Mid-tier influencers – with 50K to 500K followers">Mid-tier influencers – with 50K to 500K followers</option>
                                <option value="Micro-influencers – with 10K to 50K followers">Micro-influencers – with 10K to 50K followers</option>
                                <option value="Nano-influencers – with 1K to 10K followers">Nano-influencers – with 1K to 10K followers</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="followers" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heart text-gray-400 mr-1"></i>
                                Number of Followers
                            </label>
                            <input type="text" id="followers" name="followers" min="0" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                                   placeholder="e.g., 10K, 50K, 100K">
                        </div>
                    </div>
                </div>

                <!-- Social Media & Payment Information -->
                <div class="bg-yellow-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fab fa-instagram text-yellow-500 mr-2"></i>
                        Social Media & Payment
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">

                        <!-- Instagram Profile Link -->
                        <div class="form-group">
                            <label for="instagram_profile" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fab fa-instagram text-gray-400 mr-1"></i>
                                Instagram Profile Link
                            </label>
                            <input type="url" id="instagram_profile" name="instagram_profile" 
                                   placeholder="https://instagram.com/yourusername" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                        </div>

                        <!-- Expected Payment for One Video -->
                        <div class="form-group">
                            <label for="expected_payment" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-rupee-sign text-gray-400 mr-1"></i>
                                Expected Payment for One Video
                            </label>
                            <select id="expected_payment" name="expected_payment" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                        <option value="">Select Payment Range</option>
                        <option value="Rs 500 to 1000">Rs 500 to 1000</option>
                        <option value="Rs 1k to 2k">Rs 1k to 2k</option>
                        <option value="Rs 2k to 3k">Rs 2k to 3k</option>
                        <option value="Rs 3k to 4k">Rs 3k to 4k</option>
                        <option value="Rs 5k to 10k">Rs 5k to 10k</option>
                        <option value="Rs 10k to 20k">Rs 10k to 20k</option>
                        <option value="Rs 20k to 50k">Rs 20k to 50k</option>
                        <option value="Rs 50k to 70k">Rs 50k to 70k</option>
                        <option value="Rs 70k to 1L">Rs 70k to 1L</option>
                        <option value="Rs 1L +">Rs 1L +</option>
                    </select>
                </div>

                    </div>
                </div>

                <!-- Work Preferences -->
                <div class="bg-indigo-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-briefcase text-indigo-500 mr-2"></i>
                        Work Preferences
                    </h3>
                    
                    <!-- What Type of Product Would You Like to Work On -->
                    <div class="form-group">
                        <label for="work_type_preference" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tasks text-gray-400 mr-1"></i>
                            What type of product would you like to work on?
                        </label>
                        <select id="work_type_preference" name="work_type_preference" multiple 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm min-h-[120px]">
                        <option value="Vlogs (Video Blogs)">Vlogs (Video Blogs)</option>
                        <option value="Tutorials and How-Tos">Tutorials and How-Tos</option>
                        <option value="Product Reviews and Unboxings">Product Reviews and Unboxings</option>
                        <option value="Challenges and Trends">Challenges and Trends</option>
                        <option value="Q&A Sessions">Q&A Sessions</option>
                        <option value="Brand Collaborations">Brand Collaborations</option>
                        <option value="Educational and Informative Content">Educational and Informative Content</option>
                        <option value="Entertainment and Comedy Skits">Entertainment and Comedy Skits</option>
                        <option value="Mobile App Live Streams">Mobile App Live Streams</option>
                        <option value="Storytelling and Narratives">Storytelling and Narratives</option>
                        <option value="Event Coverage">Event Coverage</option>
                        <option value="Fitness and Workout Videos">Fitness and Workout Videos</option>
                        <option value="Short-Form Content (Reels, TikToks, Shorts)">Short-Form Content (Reels, TikToks, Shorts)</option>
                        <option value="Motivational and Inspirational Videos">Motivational and Inspirational Videos</option>
                        <option value="Virtual Tours and Experiences">Virtual Tours and Experiences</option>
                        <option value="1v1 Calling Dating App">1v1 Calling Dating App</option>
                        <option value="Hot Bold Video Content">Hot Bold Video Content</option>
                        <option value="Bikini Photoshoot">Bikini Photoshoot</option>
                        <option value="Night Club Model girls">Night Club Model girls</option>
                        <option value="Other">Other</option>
                    </select>
                        <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Hold Ctrl/Cmd to select multiple options
                        </p>
                    </div>
                </div>

                <!-- Profile Image for Artists (Camera + Gallery) -->
                <div class="bg-pink-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-camera text-pink-500 mr-2"></i>
                        Profile Image
                    </h3>
                    <div class="form-group">
                        <label for="artistImage" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-camera text-gray-400 mr-1"></i>
                            Profile Image *
                        </label>
                        <div class="relative">
                            <input type="file" id="artistImage" name="image" accept="image/*" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                                          file:bg-gradient-to-r file:from-red-500 file:to-pink-500 file:text-white 
                                          hover:file:from-red-600 hover:file:to-pink-600 file:transition-all file:duration-200">
                        </div>
                        <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Choose from gallery or take a photo with your camera (9:16 aspect ratio recommended)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Employee-specific Fields -->
            <div id="employeeFields" style="display: none;">
                <!-- Professional Information for Employees -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-briefcase text-blue-500 mr-2"></i>
                        Professional Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user-tie text-gray-400 mr-1"></i>
                                Role *
                            </label>
                            <select id="role" name="role" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                        <option value="">Select Role</option>
                        <option value="General Manager (GM)">General Manager (GM)</option>
                        <option value="Business Development Manager (BDM)">Business Development Manager (BDM)</option>
                        <option value="Project Manager">Project Manager</option>
                        <option value="Human Resources Manager (HR Manager)">Human Resources Manager (HR Manager)</option>
                        <option value="Talent Acquisition Manager">Talent Acquisition Manager</option>
                        <option value="Recruitment Specialist">Recruitment Specialist</option>
                        <option value="Sales Manager">Sales Manager</option>
                        <option value="Marketing Manager">Marketing Manager</option>
                        <option value="Digital Marketing Manager">Digital Marketing Manager</option>
                        <option value="Social Media Manager">Social Media Manager</option>
                        <option value="Brand Manager">Brand Manager</option>
                        <option value="Public Relations Manager">Public Relations Manager</option>
                        <option value="Content Marketing Manager">Content Marketing Manager</option>
                        <option value="Financial Analyst">Financial Analyst</option>
                        <option value="Investment Banker">Investment Banker</option>
                        <option value="Chartered Accountant (CA)">Chartered Accountant (CA)</option>
                        <option value="Risk Manager">Risk Manager</option>
                        <option value="Wealth Manager">Wealth Manager</option>
                        <option value="Software Engineer">Software Engineer</option>
                        <option value="Data Scientist">Data Scientist</option>
                        <option value="Cloud Architect">Cloud Architect</option>
                        <option value="Cyber Security Analyst">Cyber Security Analyst</option>
                        <option value="AI & Machine Learning Engineer">AI & Machine Learning Engineer</option>
                        <option value="IT Manager">IT Manager</option>
                        <option value="Web Developer">Web Developer</option>
                        <option value="UI/UX Designer">UI/UX Designer</option>
                        <option value="Product Manager">Product Manager</option>
                        <option value="Operations Manager">Operations Manager</option>
                        <option value="Supply Chain Manager">Supply Chain Manager</option>
                        <option value="Logistics Manager">Logistics Manager</option>
                        <option value="Quality Assurance Manager">Quality Assurance Manager</option>
                        <option value="Compliance Manager">Compliance Manager</option>
                        <option value="Legal Advisor">Legal Advisor</option>
                        <option value="Corporate Lawyer">Corporate Lawyer</option>
                        <option value="Judge/Magistrate">Judge/Magistrate</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Pharmacist">Pharmacist</option>
                        <option value="Physiotherapist">Physiotherapist</option>
                        <option value="Dietitian/Nutritionist">Dietitian/Nutritionist</option>
                        <option value="Psychologist">Psychologist</option>
                        <option value="Civil Engineer">Civil Engineer</option>
                        <option value="Mechanical Engineer">Mechanical Engineer</option>
                        <option value="Electrical Engineer">Electrical Engineer</option>
                        <option value="Robotics Engineer">Robotics Engineer</option>
                        <option value="Aerospace Engineer">Aerospace Engineer</option>
                        <option value="Professor/Lecturer">Professor/Lecturer</option>
                        <option value="School Principal">School Principal</option>
                        <option value="Corporate Trainer">Corporate Trainer</option>
                        <option value="Educational Consultant">Educational Consultant</option>
                        <option value="Film Director">Film Director</option>
                        <option value="Actor/Actress">Actor/Actress</option>
                        <option value="Content Creator">Content Creator</option>
                        <option value="Journalist">Journalist</option>
                        <option value="Video Editor">Video Editor</option>
                        <option value="Photographer">Photographer</option>
                        <option value="Event Planner">Event Planner</option>
                        <option value="Interior Designer">Interior Designer</option>
                        <option value="Fashion Designer">Fashion Designer</option>
                        <option value="Graphic Designer">Graphic Designer</option>
                        <option value="Customer Support Executive">Customer Support Executive</option>
                        <option value="Telecaller">Telecaller</option>
                        <option value="Office Administrator">Office Administrator</option>
                        <option value="Executive Assistant">Executive Assistant</option>
                </select>
            </div>

                        <div class="form-group">
                            <label for="experience" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                Years of Experience
                            </label>
                            <input type="number" id="experience" name="experience" min="0" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                                   placeholder="Enter years of experience">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 mt-6">
                        <div class="form-group">
                            <label for="current_salary" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-rupee-sign text-gray-400 mr-1"></i>
                                Current Salary (₹)
                            </label>
                            <input type="text" id="current_salary" name="current_salary" placeholder="e.g. 50000" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                            <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                Enter your current monthly salary in INR
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resume Upload Section for Employees -->
                <div class="bg-green-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-upload text-green-500 mr-2"></i>
                        Resume Upload
                    </h3>

                    <!-- Resume Upload Field -->
                    <div class="form-group">
                        <label for="resume" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-pdf text-gray-400 mr-1"></i>
                            Resume/CV *
                        </label>
                        <div class="relative">
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                                          file:bg-gradient-to-r file:from-red-500 file:to-pink-500 file:text-white 
                                          hover:file:from-red-600 hover:file:to-pink-600 file:transition-all file:duration-200">
                        </div>
                        <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Please upload your resume in PDF, DOC, or DOCX format (max 5MB)
                        </p>
                        <div id="resumeError" class="mt-2 text-sm text-red-600 hidden bg-red-50 p-2 rounded-lg"></div>
                    </div>
                </div>

                <!-- Profile Image for Employees (Camera Only) -->
                <div class="bg-teal-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-camera text-teal-500 mr-2"></i>
                        Profile Image
                    </h3>
                    <div class="form-group">
                        <label for="employeeImage" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-camera text-gray-400 mr-1"></i>
                            Profile Image *
                        </label>
                        <div class="relative">
                            <input type="file" id="employeeImage" name="image" accept="image/*" capture="camera" 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                                          file:bg-gradient-to-r file:from-red-500 file:to-pink-500 file:text-white 
                                          hover:file:from-red-600 hover:file:to-pink-600 file:transition-all file:duration-200">
                        </div>
                        <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Please take a new photo with your camera (9:16 aspect ratio recommended)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="bg-orange-50 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                    Additional Information
                </h3>
                
                <!-- Languages -->
                <div class="form-group mb-6">
                    <label for="languages" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-language text-gray-400 mr-1"></i>
                        Languages Known *
                    </label>
                    <select id="languages" name="languages[]" multiple required 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm min-h-[120px]">
                        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Bengali">Bengali</option>
                        <option value="Telugu">Telugu</option>
                        <option value="Tamil">Tamil</option>
                        <option value="Marathi">Marathi</option>
                        <option value="Gujarati">Gujarati</option>
                        <option value="Kannada">Kannada</option>
                        <option value="Malayalam">Malayalam</option>
                        <option value="Punjabi">Punjabi</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Hold Ctrl/Cmd to select multiple languages
                    </p>
                </div>
            </div>

            <!-- Submit Button Section -->
            <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl p-6 mt-8">
                <div class="form-group">
                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <span>Submit Profile</span>
                        <div class="loading-spinner ml-2 hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </div>
                
                <button id="cancelClientFormBtn" class="mt-4 w-full text-gray-500 hover:text-red-500 font-semibold py-2 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Simple modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const createProfileBtn = document.getElementById('createProfileBtn');
    const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
    const profileSelectionModal = document.getElementById('profileSelectionModal');
    const addClientModal = document.getElementById('addClientModal');
    const artistButton = document.getElementById('artistButton');
    const employeeButton = document.getElementById('employeeButton');
    const closeSelectionBtn = document.getElementById('closeSelectionBtn');
    const closeClientFormBtn = document.getElementById('closeClientFormBtn');
    const cancelClientFormBtn = document.getElementById('cancelClientFormBtn');

    // Show profile selection modal
    function showProfileModal() {
        console.log('Opening profile selection modal');
        if (profileSelectionModal) {
            // Close mobile menu if open
            const mobileMenu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            const closeIcon = document.getElementById('closeIcon');
            
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                document.body.style.overflow = ''; // Reset body overflow
            }
            
            profileSelectionModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            // Add animation to the modal
            setTimeout(function() {
                const modalContent = profileSelectionModal.querySelector('.transform');
                if (modalContent) {
                    modalContent.style.transform = 'scale(1)';
                    modalContent.style.opacity = '1';
                }
            }, 10);
        }
    }

    // Hide profile selection modal
    function hideProfileModal() {
        profileSelectionModal.classList.remove('show');
        if (!addClientModal.classList.contains('show')) {
            document.body.classList.remove('modal-open');
        }
    }

    // Show client form modal
    function showClientForm(type) {
        hideProfileModal();
        addClientModal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Set professional type
        const professionalField = document.getElementById('professionalField');
        if (professionalField) {
            professionalField.value = type;
        }
        
        // Get image input fields
        const artistImageField = document.getElementById('artistImage');
        const employeeImageField = document.getElementById('employeeImage');
        
        // Toggle fields visibility and enable/disable inputs
        const artistFields = document.getElementById('artistFields');
        const employeeFields = document.getElementById('employeeFields');
        
        if (artistFields && employeeFields) {
            if (type === 'Employee') {
                artistFields.style.display = 'none';
                employeeFields.style.display = 'block';
                
                // Disable artist field and enable employee field
                if (artistImageField) {
                    artistImageField.disabled = true;
                    artistImageField.removeAttribute('required');
                }
                if (employeeImageField) {
                    employeeImageField.disabled = false;
                    employeeImageField.setAttribute('required', 'required');
                }
            } else {
                artistFields.style.display = 'block';
                employeeFields.style.display = 'none';
                
                // Enable artist field and disable employee field
                if (artistImageField) {
                    artistImageField.disabled = false;
                    artistImageField.setAttribute('required', 'required');
                }
                if (employeeImageField) {
                    employeeImageField.disabled = true;
                    employeeImageField.removeAttribute('required');
                }
            }
        }
    }

    // Hide client form modal
    function hideClientForm() {
        addClientModal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    // Add event listeners
    if (createProfileBtn) {
        createProfileBtn.addEventListener('click', showProfileModal);
    }

    if (mobileCreateProfileBtn) {
        mobileCreateProfileBtn.addEventListener('click', showProfileModal);
    }

    if (artistButton) {
        artistButton.addEventListener('click', () => showClientForm('Artist'));
    }

    if (employeeButton) {
        employeeButton.addEventListener('click', () => showClientForm('Employee'));
    }

    if (closeSelectionBtn) {
        closeSelectionBtn.addEventListener('click', hideProfileModal);
    }

    if (closeClientFormBtn) {
        closeClientFormBtn.addEventListener('click', hideClientForm);
    }

    if (cancelClientFormBtn) {
        cancelClientFormBtn.addEventListener('click', hideClientForm);
    }
    
    // Add form validation
    const addClientForm = document.getElementById('addClientForm');
    if (addClientForm) {
        let isSubmitting = false; // Flag to prevent double submission
        
        addClientForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Prevent double submission
            if (isSubmitting) {
                console.log('Form is already being submitted, ignoring...');
                return false;
            }
            
            // Check which professional type is selected
            const professionalType = document.getElementById('professionalField').value;
            let imageField = null;
            
            if (professionalType === 'Artist') {
                imageField = document.getElementById('artistImage');
            } else if (professionalType === 'Employee') {
                imageField = document.getElementById('employeeImage');
            }
            
            // Validate image field
            if (!imageField || !imageField.files || imageField.files.length === 0) {
                alert('Please select a profile image before submitting.');
                if (imageField) {
                    imageField.focus();
                    imageField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            // Set submitting flag
            isSubmitting = true;
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const buttonText = submitButton.querySelector('span');
            const loadingSpinner = submitButton.querySelector('.loading-spinner');
            
            submitButton.disabled = true;
            buttonText.textContent = 'Submitting...';
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }
            
            // Create FormData object for AJAX submission
            const formData = new FormData(this);
            
            try {
                const response = await fetch('admin/add_client.php', {
                    method: 'POST',
                    body: formData
                });

                // Handle response properly - empty response means success
                let result;
                if (response.ok && response.status === 200) {
                    const text = await response.text();
                    // If no text (empty response), it's success
                    if (!text || text.trim() === '') {
                        result = { status: 'success' };
                    } else {
                        // Try to parse as JSON (error response)
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            result = { status: 'error', message: 'Unknown server error' };
                        }
                    }
                } else {
                    result = { status: 'error', message: 'Server error: ' + response.status };
                }
                
                console.log('Submission result:', result);

                if (result.status === 'success') {
                    // Hide the form modal
                    const addClientModal = document.getElementById('addClientModal');
                    if (addClientModal) {
                        addClientModal.style.display = 'none';
                    }

                    // Show success message
                    const successMessage = document.getElementById('successMessage');
                    if (successMessage) {
                        successMessage.style.display = 'flex';
                        document.body.classList.add('modal-open');
                        
                        // Animate the modal in
                        setTimeout(() => {
                            const modalContent = successMessage.querySelector('.bg-white');
                            if (modalContent) {
                                modalContent.style.transform = 'scale(1)';
                                modalContent.style.opacity = '1';
                            }
                        }, 10);
                    }

                    // Reset the form
                    this.reset();
                } else {
                    // Show error message
                    alert(result.message || 'Error submitting form. Please try again.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                alert('Error submitting form. Please try again.');
            } finally {
                // Reset submission flag and button state
                isSubmitting = false;
                submitButton.disabled = false;
                buttonText.textContent = 'Submit Profile';
                if (loadingSpinner) {
                    loadingSpinner.classList.add('hidden');
                }
            }
            
            return false; // Additional safeguard
        });
    }

    // Function to show notifications
    function showNotification(type, message) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-[100000] px-6 py-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto">
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === profileSelectionModal) {
            hideProfileModal();
        }
        if (event.target === addClientModal) {
            hideClientForm();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideProfileModal();
            hideClientForm();
        }
    });

    // Function to close success message
    window.closeSuccessMessage = function() {
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            const modalContent = successMessage.querySelector('.bg-white');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95)';
                modalContent.style.opacity = '0';
            }
            
            setTimeout(() => {
                successMessage.style.display = 'none';
                document.body.classList.remove('modal-open');
            }, 300);
        }
    }
    });
</script>






<!-- ✅ Clients Section -->
<section class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-2 text-gray-900 font-[Montserrat]">Our Clients</h2>
        <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">Discover our talented pool of artists and professionals</p>
        
        <!-- Professional Type Toggle -->
        <div class="clients-section bg-gray-50 py-12">
            <div class="container mx-auto px-4">
                <!-- Clients Header -->
                <div class="clients-header text-center mb-10">
                    <!-- This will be populated by JavaScript -->
    </div>

                <!-- Professional Type Toggle -->
                <div class="flex justify-center gap-4 mb-8">
                    <button id="artistFilterBtn" class="toggle-button px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-star"></i>Artists
                    </button>
                    <button id="employeeFilterBtn" class="toggle-button active px-8 py-3 rounded-full font-semibold text-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-briefcase"></i>Employees
                    </button>
        </div>
    </div>
</div>

        <!-- Filters Container -->
        <div class="filters-container bg-white rounded-2xl shadow-lg p-6 mb-8">
            <!-- Artist Filters -->
            <div id="artistFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="filterCategory" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Categories</option>
                        <option value="Dating App Host">Dating App Host</option>
                        <option value="Video Live Streamers">Video Live Streamers</option>
                        <option value="Voice Live Streamers">Voice Live Streamers</option>
                        <option value="Singer">Singer</option>
                        <option value="Dancer">Dancer</option>
                        <option value="Actor / Actress">Actor / Actress</option>
                        <option value="Model">Model</option>
                        <option value="Artist / Painter">Artist / Painter</option>
                        <option value="Social Media Influencer">Social Media Influencer</option>
                        <option value="Content Creator">Content Creator</option>
                        <option value="Vlogger">Vlogger</option>
                        <option value="Gamer / Streamer">Gamer / Streamer</option>
                        <option value="YouTuber">YouTuber</option>
                        <option value="Anchor / Emcee / Host">Anchor / Emcee / Host</option>
                        <option value="DJ / Music Producer">DJ / Music Producer</option>
                        <option value="Photographer / Videographer">Photographer / Videographer</option>
                        <option value="Makeup Artist / Hair Stylist">Makeup Artist / Hair Stylist</option>
                        <option value="Fashion Designer / Stylist">Fashion Designer / Stylist</option>
                        <option value="Fitness Trainer / Yoga Instructor">Fitness Trainer / Yoga Instructor</option>
                        <option value="Motivational Speaker / Life Coach">Motivational Speaker / Life Coach</option>
                        <option value="Chef / Culinary Artist">Chef / Culinary Artist</option>
                        <option value="Child Artist">Child Artist</option>
                        <option value="Pet Performer / Pet Model">Pet Performer / Pet Model</option>
                        <option value="Instrumental Musician">Instrumental Musician</option>
                        <option value="Director / Scriptwriter / Editor">Director / Scriptwriter / Editor</option>
                        <option value="Voice Over Artist">Voice Over Artist</option>
                        <option value="Magician / Illusionist">Magician / Illusionist</option>
                        <option value="Stand-up Comedian">Stand-up Comedian</option>
                        <option value="Mimicry Artist">Mimicry Artist</option>
                        <option value="Poet / Storyteller">Poet / Storyteller</option>
                        <option value="Language Trainer / Public Speaking Coach">Language Trainer / Public Speaking Coach</option>
                        <option value="Craft Expert / DIY Creator">Craft Expert / DIY Creator</option>
                        <option value="Travel Blogger / Explorer">Travel Blogger / Explorer</option>
                        <option value="Astrologer / Tarot Reader">Astrologer / Tarot Reader</option>
                        <option value="Educator / Subject Matter Expert">Educator / Subject Matter Expert</option>
                        <option value="Tech Reviewer / Gadget Expert">Tech Reviewer / Gadget Expert</option>
                        <option value="Unboxing / Product Reviewer">Unboxing / Product Reviewer</option>
                        <option value="Business Coach / Startup Mentor">Business Coach / Startup Mentor</option>
                        <option value="Health & Wellness Coach">Health & Wellness Coach</option>
                        <option value="Event Anchor / Wedding Host">Event Anchor / Wedding Host</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Followers</label>
                    <select id="filterFollowers" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Followers</option>
                        <option value="0K-10K">0 - 10K</option>
                        <option value="10K-50K">10K - 50K</option>
                        <option value="50K-100K">50K - 100K</option>
                        <option value="100K-500K">100K - 500K</option>
                        <option value="500K+">500K+</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select id="filterGender" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Genders</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                    <select id="filterAge" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Ages</option>
        <option value="18-25">18-25</option>
        <option value="26-35">26-35</option>
        <option value="36-45">36-45</option>
        <option value="46+">46+</option>
    </select>
        </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select id="filterLanguage" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Languages</option>
                        <option value="English">English</option>
        <option value="Hindi">Hindi</option>
                        <option value="Bengali">Bengali</option>
                        <option value="Telugu">Telugu</option>
                        <option value="Tamil">Tamil</option>
                        <option value="Marathi">Marathi</option>
                        <option value="Gujarati">Gujarati</option>
                        <option value="Kannada">Kannada</option>
                        <option value="Malayalam">Malayalam</option>
                        <option value="Punjabi">Punjabi</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <select id="filterCity" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Cities</option>
                        <option value="Mumbai">Mumbai</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Bangalore">Bangalore</option>
                        <option value="Hyderabad">Hyderabad</option>
                        <option value="Chennai">Chennai</option>
                        <option value="Kolkata">Kolkata</option>
                        <option value="Pune">Pune</option>
                        <option value="Ahmedabad">Ahmedabad</option>
                        <option value="Jaipur">Jaipur</option>
                        <option value="Lucknow">Lucknow</option>
                    </select>
    </div>
</div>

            <!-- Employee Filters -->
            <div id="employeeFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" style="display: none;">
                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="filterRole" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Roles</option>
                        <option value="General Manager (GM)">General Manager (GM)</option>
                        <option value="Business Development Manager (BDM)">Business Development Manager (BDM)</option>
                        <option value="Project Manager">Project Manager</option>
                        <option value="Human Resources Manager (HR Manager)">Human Resources Manager (HR Manager)</option>
                        <option value="Talent Acquisition Manager">Talent Acquisition Manager</option>
                        <option value="Recruitment Specialist">Recruitment Specialist</option>
                        <option value="Sales Manager">Sales Manager</option>
                        <option value="Marketing Manager">Marketing Manager</option>
                        <option value="Digital Marketing Manager">Digital Marketing Manager</option>
                        <option value="Social Media Manager">Social Media Manager</option>
                        <option value="Brand Manager">Brand Manager</option>
                        <option value="Public Relations Manager">Public Relations Manager</option>
                        <option value="Content Marketing Manager">Content Marketing Manager</option>
                        <option value="Financial Analyst">Financial Analyst</option>
                        <option value="Investment Banker">Investment Banker</option>
                        <option value="Chartered Accountant (CA)">Chartered Accountant (CA)</option>
                        <option value="Risk Manager">Risk Manager</option>
                        <option value="Wealth Manager">Wealth Manager</option>
                        <option value="Software Engineer">Software Engineer</option>
                        <option value="Data Scientist">Data Scientist</option>
                        <option value="Cloud Architect">Cloud Architect</option>
                        <option value="Cyber Security Analyst">Cyber Security Analyst</option>
                        <option value="AI & Machine Learning Engineer">AI & Machine Learning Engineer</option>
                        <option value="IT Manager">IT Manager</option>
                        <option value="Web Developer">Web Developer</option>
                        <option value="UI/UX Designer">UI/UX Designer</option>
                        <option value="Product Manager">Product Manager</option>
                        <option value="Operations Manager">Operations Manager</option>
                        <option value="Supply Chain Manager">Supply Chain Manager</option>
                        <option value="Logistics Manager">Logistics Manager</option>
                        <option value="Quality Assurance Manager">Quality Assurance Manager</option>
                        <option value="Compliance Manager">Compliance Manager</option>
                        <option value="Legal Advisor">Legal Advisor</option>
                        <option value="Corporate Lawyer">Corporate Lawyer</option>
                        <option value="Judge/Magistrate">Judge/Magistrate</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Pharmacist">Pharmacist</option>
                        <option value="Physiotherapist">Physiotherapist</option>
                        <option value="Dietitian/Nutritionist">Dietitian/Nutritionist</option>
                        <option value="Psychologist">Psychologist</option>
                        <option value="Civil Engineer">Civil Engineer</option>
                        <option value="Mechanical Engineer">Mechanical Engineer</option>
                        <option value="Electrical Engineer">Electrical Engineer</option>
                        <option value="Robotics Engineer">Robotics Engineer</option>
                        <option value="Aerospace Engineer">Aerospace Engineer</option>
                        <option value="Professor/Lecturer">Professor/Lecturer</option>
                        <option value="School Principal">School Principal</option>
                        <option value="Corporate Trainer">Corporate Trainer</option>
                        <option value="Educational Consultant">Educational Consultant</option>
                        <option value="Film Director">Film Director</option>
                        <option value="Actor/Actress">Actor/Actress</option>
                        <option value="Content Creator">Content Creator</option>
                        <option value="Journalist">Journalist</option>
                        <option value="Video Editor">Video Editor</option>
                        <option value="Photographer">Photographer</option>
                        <option value="Event Planner">Event Planner</option>
                        <option value="Interior Designer">Interior Designer</option>
                        <option value="Fashion Designer">Fashion Designer</option>
                        <option value="Graphic Designer">Graphic Designer</option>
                        <option value="Customer Support Executive">Customer Support Executive</option>
                        <option value="Telecaller">Telecaller</option>
                        <option value="Office Administrator">Office Administrator</option>
                        <option value="Executive Assistant">Executive Assistant</option>
                </select>
            </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                    <select id="filterExperience" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Experience</option>
                        <option value="0-2">0-2 years</option>
                        <option value="3-5">3-5 years</option>
                        <option value="6-10">6-10 years</option>
                        <option value="10+">10+ years</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select id="filterEmployeeGender" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Genders</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
                        <option value="Other">Other</option>
    </select>
</div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                    <select id="filterEmployeeAge" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Ages</option>
        <option value="18-25">18-25</option>
        <option value="26-35">26-35</option>
        <option value="36-45">36-45</option>
        <option value="46+">46+</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select id="filterEmployeeLanguage" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Languages</option>
        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
        <option value="Bengali">Bengali</option>
        <option value="Telugu">Telugu</option>
                        <option value="Tamil">Tamil</option>
                        <option value="Marathi">Marathi</option>
                        <option value="Gujarati">Gujarati</option>
                        <option value="Kannada">Kannada</option>
                        <option value="Malayalam">Malayalam</option>
                        <option value="Punjabi">Punjabi</option>
    </select>
                </div>

                <div class="filter-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <select id="filterEmployeeCity" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                        <option value="">All Cities</option>
                        <option value="Mumbai">Mumbai</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Bangalore">Bangalore</option>
                        <option value="Hyderabad">Hyderabad</option>
                        <option value="Chennai">Chennai</option>
                        <option value="Kolkata">Kolkata</option>
                        <option value="Pune">Pune</option>
                        <option value="Ahmedabad">Ahmedabad</option>
                        <option value="Jaipur">Jaipur</option>
                        <option value="Lucknow">Lucknow</option>
    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex justify-end items-center gap-4 mt-6 pt-4 border-t border-gray-200">
                <button onclick="resetFilters()" class="px-6 py-2 rounded-lg text-gray-600 hover:text-gray-800 font-medium transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-undo"></i>Reset
                </button>
                <button onclick="applyFilters()" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-filter"></i>Apply Filters
                </button>
            </div>
</div>

        <!-- Clients Grid Container -->
        <div id="clientsList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
            <!-- Client cards will be loaded here -->
            <div class="col-span-full text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-red-500 mb-4"></i>
                <p class="mt-2 text-gray-600 font-inter">Loading our talented professionals...</p>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="paginationControls" class="mt-12 flex justify-center items-center gap-3 py-4">
            <!-- Pagination buttons will be dynamically inserted here -->
    </div>
    </div>
</section>

<!-- ✅ Categories Showcase Section -->
<!-- <section class="categories-section">
    <div class="relative z-10 text-center max-w-6xl mx-auto px-6">
        <h2 class="categories-title text-4xl md:text-5xl">Explore Our Categories</h2>
        <p class="categories-subtitle text-base md:text-lg">Discover our diverse range of talent categories and find the perfect match for your next project</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="category-card" style="--category-color-from: #ff4757; --category-color-to: #ff6b81;">
                <div class="category-card-content">
                    <i class="fas fa-video category-icon"></i>
                    <h3 class="category-name">Live Streaming Host</h3>
                    <p class="category-count">24 Talents Available</p>
                </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #2e86de; --category-color-to: #54a0ff;">
                <div class="category-card-content">
                    <i class="fab fa-youtube category-icon"></i>
                    <h3 class="category-name">YouTubers</h3>
                    <p class="category-count">36 Talents Available</p>
                </div>
</div>

            <div class="category-card" style="--category-color-from: #ffa502; --category-color-to: #ffcc00;">
                <div class="category-card-content">
                    <i class="fas fa-hashtag category-icon"></i>
                    <h3 class="category-name">Social Media Influencers</h3>
                    <p class="category-count">42 Talents Available</p>
        </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #20bf6b; --category-color-to: #26de81;">
                <div class="category-card-content">
                    <i class="fas fa-film category-icon"></i>
                    <h3 class="category-name">Hollywood Artist</h3>
                    <p class="category-count">18 Talents Available</p>
    </div>
</div>

            <div class="category-card" style="--category-color-from: #8e44ad; --category-color-to: #9b59b6;">
                <div class="category-card-content">
                    <i class="fas fa-gamepad category-icon"></i>
                    <h3 class="category-name">Mobile/PC Gamers</h3>
                    <p class="category-count">29 Talents Available</p>
                </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #f39c12; --category-color-to: #f1c40f;">
                <div class="category-card-content">
                    <i class="fas fa-video category-icon"></i>
                    <h3 class="category-name">Short Video Creators</h3>
                    <p class="category-count">31 Talents Available</p>
                </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #e84393; --category-color-to: #fd79a8;">
                <div class="category-card-content">
                    <i class="fas fa-microphone category-icon"></i>
                    <h3 class="category-name">Podcast Hosts</h3>
                    <p class="category-count">15 Talents Available</p>
                </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #6c5ce7; --category-color-to: #a29bfe;">
                <div class="category-card-content">
                    <i class="fas fa-blog category-icon"></i>
                    <h3 class="category-name">Lifestyle Bloggers</h3>
                    <p class="category-count">27 Talents Available</p>
                </div>
            </div>
            
            <div class="category-card" style="--category-color-from: #00b894; --category-color-to: #55efc4;">
                <div class="category-card-content">
                    <i class="fas fa-dumbbell category-icon"></i>
                    <h3 class="category-name">Fitness Influencers</h3>
                    <p class="category-count">22 Talents Available</p>
                </div>
            </div>
        </div>
        
        <div class="mt-12">
            <a href="#" class="inline-block px-8 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-full hover:from-red-600 hover:to-pink-600 transition-all duration-300 font-poppins font-semibold shadow-md">
                View All Categories <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section> -->

<!-- ✅ Trusted Clients Section -->
<section class="trusted-clients-section">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="trusted-clients-title text-3xl md:text-4xl">Our Trusted Clients</h2>
            <p class="trusted-clients-subtitle text-base md:text-lg">Partnering with leading brands and organizations to deliver exceptional talent solutions</p>
        </div>
        
        <!-- Infinite Scroll for All Screen Sizes -->
        <div>
            <!-- First row - left to right -->
            <div class="logo-scroll-container">
                <div class="logo-scroll">
                    <!-- First set of logos -->
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/nykaa.png" alt="Chingari" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/paytm.png" alt="Disco" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/phone-pay.png" alt="Josh" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/Reliance-Retail.png" alt="ME" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/sbi-bank.png" alt="Meesho" class="client-logo">
                        </div>
    </div>

                    <!-- Duplicate set for seamless scrolling -->
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/amazon.png" alt="Chingari" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/capmine.png" alt="Disco" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/congnizant.png" alt="Josh" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/hcl.png" alt="ME" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/ibm.png" alt="Meesho" class="client-logo">
                        </div>
                    </div>
                </div>
</div>

            <!-- Second row - right to left (reverse) -->
            <div class="logo-scroll-container mt-8">
                <div class="logo-scroll-reverse">
                    <!-- First set of logos -->
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/infoses.png" alt="Meesho" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/tata.png" alt="ME" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/it-images/techMahindra.png" alt="Josh" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/axis-bank.png" alt="Disco" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/bajaj.png" alt="Chingari" class="client-logo">
                        </div>
    </div>

                    <!-- Duplicate set for seamless scrolling -->
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/big-basket.png" alt="Meesho" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/flipkart.png" alt="ME" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/google-pay.png" alt="Josh" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/hdfc.png" alt="Disco" class="client-logo">
                        </div>
                    </div>
                    <div class="logo-scroll-item">
                        <div class="client-logo-container">
                            <img src="/images/company-images/improved-logos/banking-logo/ICICI.png" alt="Chingari" class="client-logo">
                        </div>
                    </div>
        </div>
    </div>
    </div>

        <!-- Call to Action -->
        <!-- <div class="text-center mt-12">
            <a href="#" class="inline-block px-8 py-3 bg-white text-gray-800 border border-gray-300 rounded-full hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-poppins font-semibold shadow-sm">
                View All Partners <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div> -->
</div>
</section>

<!-- ✅ Fully Responsive About Section -->
<div class="relative w-full h-auto lg:h-[80vh] flex flex-col lg:flex-row items-center bg-cover bg-center" 
    style="background-image: url('/images/section-background-1.jpg');">
    
    <!-- ✅ Right Side Black Overlay (Covers Entire Background on Mobile) -->
    <div class="absolute inset-0 bg-black bg-opacity-70 lg:w-1/2 lg:right-0 lg:inset-auto"></div>

    <!-- ✅ Content Section (Now Centered on Mobile, Left on Large Screens) -->
    <div class="relative z-10 w-full lg:w-1/2 ml-auto px-6 lg:px-16 py-12 text-white text-center lg:text-left">
        <h2 class="text-4xl lg:text-5xl font-bold font-[Montserrat]">About Us</h2>
        <p class="text-red-400 text-xl lg:text-2xl italic font-[Dancing Script] mt-2">Something You Need to Know</p>

        <p class="mt-4 text-md lg:text-lg font-light leading-relaxed font-[Poppins]">
            We are helping models, influencers, and artists showcase their potential and collaborate with top brands.  
            With a vast network in the entertainment & fashion industry, we bring talent closer to their dreams.
        </p>

        <p class="mt-4 text-md lg:text-lg font-light font-[Poppins]">
            Our agency believes in creativity, excellence, and building long-lasting relationships with artists. Whether you're a rising star or an experienced professional, we provide the right platform to grow and shine.
        </p>

        <!-- ✅ About Us Button -->
        <button class="mt-6 px-6 lg:px-8 py-3 bg-red-500 text-white text-md lg:text-lg font-semibold rounded-full shadow-lg transition-all duration-300 hover:scale-105 hover:bg-red-600">
            ABOUT US
        </button>
    </div>
</div>

<!-- ✅ Engaging Call-to-Action (CTA) Section -->
<div class="relative bg-gradient-to-r from-red-600 to-pink-500 text-white py-20 px-8 text-center">
    <h2 class="text-5xl font-extrabold tracking-wide leading-tight font-montserrat animate-fade-in">
        🚀 Show Your Talent?  
        <br> Call Us Now to Know How!
    </h2>
    
    <p class="mt-4 text-lg font-light max-w-3xl mx-auto font-poppins animate-slide-up">
        Ready to take your career to the next level? Whether you're an influencer, model, or artist, we help you get discovered by top brands!  
        Don't miss out on amazing opportunities.
    </p>

    <!-- ✅ Call Now Button with Animation -->
    <button onclick="callNow()" class="mt-6 px-8 py-4 bg-white text-red-600 font-semibold text-xl rounded-full shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-2xl animate-bounce">
        📞 Call Us Now
    </button>

    <!-- ✅ Floating Call Icon -->
    <div class="absolute bottom-6 right-6 hidden md:block animate-pulse">
        <a href="tel:+911234567890" class="bg-white text-red-600 p-4 rounded-full shadow-xl hover:scale-110 transition">
            📞
        </a>
    </div>
</div>

    <script>
    function callNow() {
        window.location.href = "tel:+911234567890"; // Replace with your actual phone number
        }
    </script>

<!-- ✅ Footer Section -->
<footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
            <!-- Column 1: Company Info -->
            <div class="lg:col-span-1">
                <div class="flex flex-col items-center lg:items-start">
                    <img src="/images/sortoutInnovation-icon/Sortout innovation.jpg" alt="Sortout Innovation" class="h-16 w-auto mb-4" />
                    <p class="text-center lg:text-left text-gray-300 text-sm">
                        Empowering businesses with top-notch solutions in digital, IT,
                        and business services.
                    </p>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="lg:col-span-1">
                <h4 class="text-xl font-semibold mb-4 text-center lg:text-left">Quick Links</h4>
                <ul class="space-y-2 text-center lg:text-left">
                    <li><a href="index.html" class="text-gray-300 hover:text-white transition duration-300">Home</a></li>
                    <li><a href="/pages/about-page/about.html" class="text-gray-300 hover:text-white transition duration-300">About Us</a></li>
                    <li><a href="/pages/contact-page/contact-page.html" class="text-gray-300 hover:text-white transition duration-300">Contact</a></li>
                    <li><a href="/pages/career.html" class="text-gray-300 hover:text-white transition duration-300">Careers</a></li>
                    <li><a href="/pages/our-services-page/service.html" class="text-gray-300 hover:text-white transition duration-300">Services</a></li>
                    <li><a href="/blog/index.php" class="text-gray-300 hover:text-white transition duration-300">Blogs</a></li>
                    <li><a href="/auth/register.php" class="text-gray-300 hover:text-white transition duration-300">Register</a></li>
                    <li><a href="/modal_agency.php" class="text-gray-300 hover:text-white transition duration-300">talent</a></li>
                </ul>
            </div>

            <!-- Column 3: Our Services -->
            <div class="lg:col-span-1">
                <h4 class="text-xl font-semibold mb-4 text-center lg:text-left">Our Services</h4>
                <ul class="space-y-2 text-center lg:text-left">
                    <li><a href="/pages/services/socialMediaInfluencers.html" class="text-gray-300 hover:text-white transition duration-300">Digital Marketing</a></li>
                    <li><a href="/pages/services/itServices.html" class="text-gray-300 hover:text-white transition duration-300">IT Support</a></li>
                    <li><a href="/pages/services/caServices.html" class="text-gray-300 hover:text-white transition duration-300">CA Services</a></li>
                    <li><a href="/pages/services/hrServices.html" class="text-gray-300 hover:text-white transition duration-300">HR Services</a></li>
                    <li><a href="/pages/services/courierServices.html" class="text-gray-300 hover:text-white transition duration-300">Courier Services</a></li>
                    <li><a href="/pages/services/shipping.html" class="text-gray-300 hover:text-white transition duration-300">Shipping & Fulfillment</a></li>
                    <li><a href="/pages/services/stationeryServices.html" class="text-gray-300 hover:text-white transition duration-300">Stationery Services</a></li>
                    <li><a href="/pages/services/propertyServices.html" class="text-gray-300 hover:text-white transition duration-300">Real Estate & Property</a></li>
                    <li><a href="/pages/services/event-managementServices.html" class="text-gray-300 hover:text-white transition duration-300">Event Management</a></li>
                    <li><a href="/pages/services/designAndCreative.html" class="text-gray-300 hover:text-white transition duration-300">Design & Creative</a></li>
                    <li><a href="/pages/services/designAndCreative.html" class="text-gray-300 hover:text-white transition duration-300">Web & App Development</a></li>
                    <li><a href="/pages/talent.page/talent.html" class="text-gray-300 hover:text-white transition duration-300">Find Talent</a></li>
                </ul>
            </div>

            <!-- Column 4: Contact Info -->
            <div class="lg:col-span-1">
                <h4 class="text-xl font-semibold mb-4 text-center lg:text-left">Contact Us</h4>
                <ul class="space-y-3 text-center lg:text-left">
                    <li class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-phone mr-2 text-gray-400"></i>
                        <a href="tel:+919818559036" class="text-gray-300 hover:text-white transition duration-300">+91 9818559036</a>
                    </li>
                    <li class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        <a href="mailto:info@sortoutinnovation.com" class="text-gray-300 hover:text-white transition duration-300">info@sortoutinnovation.com</a>
                    </li>
                    <li class="flex items-center justify-center lg:justify-start">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                        <span class="text-gray-300">Spaze i-Tech Park, Gurugram, India</span>
                    </li>
                </ul>
            </div>

            <!-- Column 5: Social Media -->
            <div class="lg:col-span-1">
                <h4 class="text-xl font-semibold mb-4 text-center lg:text-left">Follow Us</h4>
                <div class="flex justify-center lg:justify-start space-x-4">
                    <a href="https://www.facebook.com/profile.php?id=61556452066209" class="text-gray-400 hover:text-white transition duration-300 text-2xl">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://youtu.be/tw-xk-Pb-zA?si=QMTwuvhEuTegpqDr" class="text-gray-400 hover:text-white transition duration-300 text-2xl">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/sortout-innovation/" class="text-gray-400 hover:text-white transition duration-300 text-2xl">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="https://www.instagram.com/sortoutinnovation" class="text-gray-400 hover:text-white transition duration-300 text-2xl">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright & Legal Links -->
        <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-center md:text-left mb-4 md:mb-0">&copy; 2025 Sortout Innovation. All Rights Reserved.</p>
            <ul class="flex space-x-6">
                <li><a href="/privacy-policy" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                <li><a href="/terms" class="text-gray-400 hover:text-white transition duration-300">Terms & Conditions</a></li>
            </ul>
        </div>
    </div>
</footer>

<script>
    // Mobile Menu Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');
        const createProfileBtn = document.getElementById('createProfileBtn');
        const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
        
        // Toggle mobile menu
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
        
        // Ensure both profile buttons trigger the same action
        mobileCreateProfileBtn.addEventListener('click', function() {
            // Trigger the same action as the main create profile button
            createProfileBtn.click();
            // Close the mobile menu after clicking
            mobileMenu.classList.add('hidden');
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        });
        
        // Close mobile menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                mobileMenu.classList.add('hidden');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        });
    });
</script>

<script>
    // Client fetching and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        let currentPage = 1;
        let currentProfessional = 'Employee';
        let filters = {};
        
        // Get filter toggle buttons
        const artistFilterBtn = document.getElementById('artistFilterBtn');
        const employeeFilterBtn = document.getElementById('employeeFilterBtn');
        const artistFilters = document.getElementById('artistFilters');
        const employeeFilters = document.getElementById('employeeFilters');
        
        // Add event listeners to toggle buttons
        artistFilterBtn.addEventListener('click', function() {
            toggleProfessionalType('Artist');
        });
        
        employeeFilterBtn.addEventListener('click', function() {
            toggleProfessionalType('Employee');
        });
        
        // Function to toggle between Artist and Employee filters
        function toggleProfessionalType(type) {
            currentProfessional = type;
            
            // Update active button
            if (type === 'Artist') {
                artistFilterBtn.classList.add('active');
                employeeFilterBtn.classList.remove('active');
                artistFilters.style.display = 'grid';
                employeeFilters.style.display = 'none';
            } else {
                artistFilterBtn.classList.remove('active');
                employeeFilterBtn.classList.add('active');
                artistFilters.style.display = 'none';
                employeeFilters.style.display = 'grid';
            }
            
            // Reset filters and fetch clients
            resetFilters();
            fetchClients();
        }
        
        // Function to apply filters
        window.applyFilters = function() {
            filters = {};
            
            // Get filter values based on current professional type
            if (currentProfessional === 'Artist') {
                const category = document.getElementById('filterCategory').value;
                const followers = document.getElementById('filterFollowers').value;
                const gender = document.getElementById('filterGender').value;
                const age = document.getElementById('filterAge').value;
                const language = document.getElementById('filterLanguage').value;
                const city = document.getElementById('filterCity').value;
                
                if (category) filters.category = category;
                if (followers) filters.followers = followers;
                if (gender) filters.gender = gender;
                if (age) filters.age = age;
                if (language) filters.language = language;
                if (city) filters.city = city;
                } else {
                const role = document.getElementById('filterRole').value;
                const experience = document.getElementById('filterExperience').value;
                const gender = document.getElementById('filterEmployeeGender').value;
                const age = document.getElementById('filterEmployeeAge').value;
                const language = document.getElementById('filterEmployeeLanguage').value;
                const city = document.getElementById('filterEmployeeCity').value;
                
                if (role) filters.role = role;
                if (experience) filters.experience = experience;
                if (gender) filters.gender = gender;
                if (age) filters.age = age;
                if (language) filters.language = language;
                if (city) filters.city = city;
            }
            
            // Reset to first page and fetch clients
            currentPage = 1;
            fetchClients();
        };
        
        // Function to reset filters
        window.resetFilters = function() {
            // Reset filter dropdowns based on current professional type
            if (currentProfessional === 'Artist') {
                document.getElementById('filterCategory').value = '';
                document.getElementById('filterFollowers').value = '';
                document.getElementById('filterGender').value = '';
                document.getElementById('filterAge').value = '';
                document.getElementById('filterLanguage').value = '';
                document.getElementById('filterCity').value = '';
            } else {
                document.getElementById('filterRole').value = '';
                document.getElementById('filterExperience').value = '';
                document.getElementById('filterEmployeeGender').value = '';
                document.getElementById('filterEmployeeAge').value = '';
                document.getElementById('filterEmployeeLanguage').value = '';
                document.getElementById('filterEmployeeCity').value = '';
            }
            
            // Clear filters object and fetch clients
            filters = {};
            currentPage = 1;
            fetchClients();
        };
        
        // Function to fetch clients from the server
        async function fetchClients() {
            try {
                // Show loading state
                document.getElementById('clientsList').innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-red-500"></i><p class="mt-2 text-gray-600">Loading clients...</p></div>';
                
                // Build query parameters
                let queryParams = `?page=${currentPage}&professional=${currentProfessional}`;
                
                // Add filters to query parameters
                for (const [key, value] of Object.entries(filters)) {
                    queryParams += `&${key}=${encodeURIComponent(value)}`;
                }
                
                // Fetch clients from the server
                const response = await fetch(`fetch_clients.php${queryParams}`);
                const data = await response.json();

                if (data.status === 'success') {
                    // Debug: Log client data to check if city field exists
                    console.log('Client data:', data.clients);
                    if (data.clients.length > 0) {
                        console.log('First client:', data.clients[0]);
                        console.log('City field exists:', 'city' in data.clients[0]);
                        console.log('City value:', data.clients[0].city);
                    }
                    
                    // Render clients
                    renderClients(data.clients);
                    
                    // Render pagination
                    renderPagination(data.total, data.page, data.items_per_page);
                } else {
                    // Show error message
                    document.getElementById('clientsList').innerHTML = `<div class="col-span-full text-center py-8"><i class="fas fa-exclamation-circle text-3xl text-red-500"></i><p class="mt-2 text-gray-600">${data.message || 'Error fetching clients'}</p></div>`;
                }
            } catch (error) {
                console.error('Error fetching clients:', error);
                document.getElementById('clientsList').innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-exclamation-circle text-3xl text-red-500"></i><p class="mt-2 text-gray-600">Error fetching clients. Please try again later.</p></div>';
            }
        }
        
        // Function to render clients
        function renderClients(clients) {
            const clientsList = document.getElementById('clientsList');
            
            if (clients.length === 0) {
                clientsList.innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-search text-3xl text-gray-400"></i><p class="mt-2 text-gray-600 font-inter">No clients found. Try different filters.</p></div>';
                return;
            }
            
            let html = '';
            
            clients.forEach(client => {
                html += `
                <div class="client-card relative overflow-hidden rounded-xl shadow-md bg-white cursor-pointer hover:shadow-xl transition-all duration-300">
                    <!-- Image Container with 9:16 aspect ratio -->
                    <div class="relative w-full pb-[177.78%] overflow-hidden">
                        <img 
                            src="${client.image_url || '/images/default-profile.jpg'}" 
                            alt="${client.name}" 
                            class="absolute inset-0 h-full w-full object-cover"
                            loading="lazy"
                            onerror="this.src='/images/default-profile.jpg'"
                        >
                        
                        <!-- Category Badge -->
                        <div class="client-badge absolute top-3 right-3 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">
                            ${client.professional}
                                    </div>
                                </div>
                    
                    <!-- Overlay Content - Hidden by default, shown on click -->
                    <div class="client-overlay hidden absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-b from-black/90 to-black/80 p-5 z-10 backdrop-blur-sm">
                        <div class="space-y-2 mb-4">
                            <p class="text-base text-white/90"><i class="fas fa-map-marker-alt mr-2"></i>${client.city || 'Location not specified'}</p>
                            <p class="text-base text-white/90"><i class="fas fa-venus-mars mr-2"></i>${client.gender || 'N/A'}</p>
                            <p class="text-base text-white/90"><i class="fas fa-birthday-cake mr-2"></i>${client.age || 'N/A'} years old</p>
                            
                            ${client.professional === 'Artist' ? 
                                `<p class="text-base text-white/90"><i class="fas fa-users mr-2"></i>${client.followers || '0'} followers</p>
                                 <p class="text-base text-white/90"><i class="fas fa-language mr-2"></i>${client.language || 'N/A'}</p>` : 
                                `<p class="text-base text-white/90"><i class="fas fa-clock mr-2"></i>${client.experience || '0'} years experience</p>
                                 <p class="text-base text-white/90"><i class="fas fa-language mr-2"></i>${client.language || 'N/A'}</p>
                                 <p class="text-base text-white/90"><i class="fas fa-rupee-sign mr-2"></i>${client.current_salary ? client.current_salary + ' INR/month' : 'Salary not specified'}</p>`
                            }
                                </div>
                        
                        <a href="https://docs.google.com/forms/d/e/1FAIpQLSc7vZq_7h4GENbpSP6jPCVv1a2o32yK_H0Zz21xrcZ9QIgvyw/viewform" 
                           onclick="window.open(this.href, '_blank'); return false;"
                           class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-300 font-semibold shadow-md flex items-center justify-center cursor-pointer">
                            ${client.professional === 'Employee' ? 'Hire Now' : 'Book Now'} <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                            </div>
                    
                    <!-- Default Content -->
                    <div class="p-4">
                        <h3 class="client-name text-lg font-bold text-gray-800 mb-1">${client.name}</h3>
                        <p class="client-category text-sm text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-1.5 text-red-500"></i>${client.city || 'Location not specified'}</p>
                        ${client.professional === 'Artist' ? 
                            `<p class="client-category text-sm text-gray-600 mb-1"><i class="fas fa-tag mr-1.5 text-red-500"></i>${client.category || 'N/A'}</p>
                             <p class="client-category text-xs text-gray-500"><i class="fas fa-users mr-1.5 text-red-500"></i>${client.followers || '0'} followers</p>` : 
                            `<p class="client-category text-sm text-gray-600 mb-1"><i class="fas fa-briefcase mr-1.5 text-red-500"></i>${client.role || 'N/A'}</p>
                             <p class="client-category text-xs text-gray-500"><i class="fas fa-clock mr-1.5 text-red-500"></i>${client.experience || '0'} years experience</p>
                             </p>`
                        }
                    </div>
                </div>`;
            });
            
            clientsList.innerHTML = html;
            
            // Initialize click handlers for the newly added client cards
            setupClientCards();
        }
        
        // Function to render pagination
        function renderPagination(total, currentPage, itemsPerPage) {
            const paginationControls = document.getElementById('paginationControls');
            const totalPages = Math.ceil(total / itemsPerPage);
            
            if (totalPages <= 1) {
                paginationControls.innerHTML = '';
                return;
            }
            
            let html = '';
            
            // Previous button
            html += `<button class="h-10 w-10 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-300 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:border-red-500 hover:text-red-500'}" ${currentPage === 1 ? 'disabled' : 'onclick="changePage(' + (currentPage - 1) + ')"'}><i class="fas fa-chevron-left"></i></button>`;
            
            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += `<button class="h-10 w-10 flex items-center justify-center rounded-md bg-gradient-to-r from-red-500 to-pink-500 text-white font-medium shadow-sm" onclick="changePage(${i})">${i}</button>`;
                } else {
                    html += `<button class="h-10 w-10 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-red-500 hover:text-red-500 transition-colors duration-300" onclick="changePage(${i})">${i}</button>`;
                }
            }
            
            // Next button
            html += `<button class="h-10 w-10 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 transition-colors duration-300 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:border-red-500 hover:text-red-500'}" ${currentPage === totalPages ? 'disabled' : 'onclick="changePage(' + (currentPage + 1) + ')"'}><i class="fas fa-chevron-right"></i></button>`;
            
            paginationControls.innerHTML = html;
        }
        
        // Function to change page
        window.changePage = function(page) {
            currentPage = page;
            fetchClients();
            
            // Scroll to top of clients section
            document.querySelector('.filters-container').scrollIntoView({ behavior: 'smooth' });
        };
        
        // Initialize by fetching clients
        fetchClients();
        
        // Ensure Employee filter is selected by default
        toggleProfessionalType('Employee');
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary elements
    const createProfileBtn = document.getElementById('createProfileBtn');
    const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
    const profileSelectionModal = document.getElementById('profileSelectionModal');
    const addClientModal = document.getElementById('addClientModal');
    const closeSelectionBtn = document.getElementById('closeSelectionBtn');
    const closeClientFormBtn = document.getElementById('closeClientFormBtn');
    const cancelClientFormBtn = document.getElementById('cancelClientFormBtn');

    // Add click event listeners to both Create Profile buttons
    if (createProfileBtn) {
        createProfileBtn.addEventListener('click', showSelectionModal);
    }
    if (mobileCreateProfileBtn) {
        mobileCreateProfileBtn.addEventListener('click', showSelectionModal);
    }

    // Function to show selection modal
    function showSelectionModal() {
        if (profileSelectionModal) {
            profileSelectionModal.classList.remove('hidden');
            profileSelectionModal.classList.add('flex');
            document.body.classList.add('modal-open');
        }
    }

    // Function to close selection modal
    window.closeSelectionModal = function() {
        if (profileSelectionModal) {
            profileSelectionModal.classList.add('hidden');
            profileSelectionModal.classList.remove('flex');
            document.body.classList.remove('modal-open');
        }
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === profileSelectionModal) {
            closeSelectionModal();
        }
        if (event.target === addClientModal) {
            closeAddClientForm();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSelectionModal();
            closeAddClientForm();
        }
    });
});
</script>

<script>
// Complete rewrite of modal functionality with fixes
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded - Modal fix v2');
    
    // Get elements
    const createProfileBtn = document.getElementById('createProfileBtn');
    const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
    const profileSelectionModal = document.getElementById('profileSelectionModal');
    const addClientModal = document.getElementById('addClientModal');
    const closeSelectionBtn = document.getElementById('closeSelectionBtn');
    const closeClientFormBtn = document.getElementById('closeClientFormBtn');
    const cancelClientFormBtn = document.getElementById('cancelClientFormBtn');
    const artistBtn = document.querySelector('button[data-type="Artist"]');
    const employeeBtn = document.querySelector('button[data-type="Employee"]');
    
    // Log elements for debugging
    console.log('Create Profile button:', createProfileBtn);
    console.log('Profile Selection Modal:', profileSelectionModal);
    console.log('Artist button:', artistBtn);
    console.log('Employee button:', employeeBtn);
    
    // Show profile selection modal
    function showProfileModal() {
        if (profileSelectionModal) {
            profileSelectionModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            console.log('Profile selection modal opened');
        }
    }
    
    // Close profile selection modal
    function closeProfileModal() {
        if (profileSelectionModal) {
            profileSelectionModal.style.display = 'none';
            document.body.style.overflow = '';
            console.log('Profile selection modal closed');
        }
    }
    
    // Show client form modal
    function showClientForm(type) {
        console.log('Opening client form for:', type);
        closeProfileModal();
        
        if (addClientModal) {
            addClientModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            console.log('Client form opened for:', type);
            
            // Set professional type
            const professionalField = document.getElementById('professionalField');
            if (professionalField) {
                professionalField.value = type;
                console.log('Set professional field to:', type);
        } else {
                console.log('Professional field not found');
            }
            
            // Toggle fields visibility
            const artistFields = document.getElementById('artistFields');
            const employeeFields = document.getElementById('employeeFields');
            
            if (artistFields && employeeFields) {
                if (type === 'Employee') {
                    artistFields.style.display = 'none';
                    employeeFields.style.display = 'block';
                } else {
                    artistFields.style.display = 'block';
                    employeeFields.style.display = 'none';
                }
                console.log('Toggled fields visibility for:', type);
            } else {
                console.log('Artist or Employee fields not found');
            }
        } else {
            console.log('Client form modal not found');
        }
    }
    
    // Close client form modal
    function closeClientForm() {
        if (addClientModal) {
            addClientModal.style.display = 'none';
            document.body.style.overflow = '';
            console.log('Client form closed');
        }
    }
    
    // Add event listeners
    if (createProfileBtn) {
        createProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showProfileModal();
        });
        console.log('Added event listener to Create Profile button');
    }
    
    if (mobileCreateProfileBtn) {
        mobileCreateProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showProfileModal();
        });
        console.log('Added event listener to Mobile Create Profile button');
    }
    
    if (closeSelectionBtn) {
        closeSelectionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeProfileModal();
        });
        console.log('Added event listener to Close Selection button');
    }
    
    if (closeClientFormBtn) {
        closeClientFormBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeClientForm();
        });
        console.log('Added event listener to Close Client Form button');
    }
    
    if (cancelClientFormBtn) {
        cancelClientFormBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeClientForm();
        });
        console.log('Added event listener to Cancel Client Form button');
    }
    
    // Direct event listeners for Artist/Employee buttons
    if (artistBtn) {
        artistBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Artist button clicked');
            showClientForm('Artist');
            return false;
        };
        console.log('Added direct onclick to Artist button');
    }
    
    if (employeeBtn) {
        employeeBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Employee button clicked');
            showClientForm('Employee');
            return false;
        };
        console.log('Added direct onclick to Employee button');
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === profileSelectionModal) {
            closeProfileModal();
        }
        
        if (event.target === addClientModal) {
            closeClientForm();
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProfileModal();
            closeClientForm();
        }
    });
    
    // Ensure body scrolling is restored if modals are closed
    window.addEventListener('mouseup', function() {
        if (profileSelectionModal && profileSelectionModal.style.display === 'none' &&
            addClientModal && addClientModal.style.display === 'none') {
            document.body.style.overflow = '';
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Initializing modals');
    
    // Get modal elements
    const profileSelectionModal = document.getElementById('profileSelectionModal');
    const addClientModal = document.getElementById('addClientModal');
    const artistButton = document.getElementById('artistButton');
    const employeeButton = document.getElementById('employeeButton');
    const createProfileBtn = document.getElementById('createProfileBtn');
    const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
    const closeSelectionBtn = document.getElementById('closeSelectionBtn');
    const closeClientFormBtn = document.getElementById('closeClientFormBtn');
    const cancelClientFormBtn = document.getElementById('cancelClientFormBtn');

    // Initially hide both modals
    if (profileSelectionModal) profileSelectionModal.style.display = 'none';
    if (addClientModal) addClientModal.style.display = 'none';

    // Show profile selection modal
    function showProfileModal() {
        console.log('Opening profile selection modal');
        if (profileSelectionModal) {
            profileSelectionModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            // Add animation to the modal
            setTimeout(function() {
                const modalContent = profileSelectionModal.querySelector('.transform');
                if (modalContent) {
                    modalContent.style.transform = 'scale(1)';
                    modalContent.style.opacity = '1';
                }
            }, 10);
        }
    }

    // Hide profile selection modal
    function hideProfileModal() {
        console.log('Closing profile selection modal');
        if (profileSelectionModal) {
            profileSelectionModal.style.display = 'none';
            if (!addClientModal || addClientModal.style.display === 'none') {
                document.body.classList.remove('modal-open');
            }
        }
    }

    // Show client form modal
    function showClientForm(type) {
        console.log('Opening client form for:', type);
        hideProfileModal();
        
        if (addClientModal) {
            addClientModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            // Set professional type
            const professionalField = document.getElementById('professionalField');
            if (professionalField) {
                professionalField.value = type;
                console.log('Set professional type to:', type);
            }
            
            // Toggle fields visibility
            const artistFields = document.getElementById('artistFields');
            const employeeFields = document.getElementById('employeeFields');
            
            if (artistFields && employeeFields) {
                artistFields.style.display = type === 'Artist' ? 'block' : 'none';
                employeeFields.style.display = type === 'Employee' ? 'block' : 'none';
                console.log('Toggled fields for:', type);
            }
        }
    }

    // Hide client form modal
    function hideClientForm() {
        console.log('Closing client form');
        if (addClientModal) {
            addClientModal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }

    // Event Listeners
    if (createProfileBtn) {
        createProfileBtn.addEventListener('click', showProfileModal);
    }

    if (mobileCreateProfileBtn) {
        mobileCreateProfileBtn.addEventListener('click', showProfileModal);
    }

    if (artistButton) {
        artistButton.addEventListener('click', () => showClientForm('Artist'));
    }

    if (employeeButton) {
        employeeButton.addEventListener('click', () => showClientForm('Employee'));
    }

    if (closeSelectionBtn) {
        closeSelectionBtn.addEventListener('click', hideProfileModal);
    }

    if (closeClientFormBtn) {
        closeClientFormBtn.addEventListener('click', hideClientForm);
    }

    if (cancelClientFormBtn) {
        cancelClientFormBtn.addEventListener('click', hideClientForm);
    }

    // Close on outside click
    window.addEventListener('click', function(event) {
        if (event.target === profileSelectionModal) {
            hideProfileModal();
        }
        if (event.target === addClientModal) {
            hideClientForm();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideProfileModal();
            hideClientForm();
        }
    });
});
</script>

<!-- Add this script to handle dynamic client cards -->
<script>
// Function to initialize touch events for client cards
function initializeClientCards() {
    const clientCards = document.querySelectorAll('.client-card');
    
    clientCards.forEach(card => {
        // Remove existing event listeners to prevent duplicates
        card.removeEventListener('touchstart', handleTouchStart);
        card.removeEventListener('touchend', handleTouchEnd);
        
        // Add new event listeners
        card.addEventListener('touchstart', handleTouchStart, { passive: false });
        card.addEventListener('touchend', handleTouchEnd);
    });
}

// Touch start handler
function handleTouchStart(e) {
    this.touchStartTime = Date.now();
    // Don't prevent default to allow scrolling
}

// Touch end handler
function handleTouchEnd(e) {
    const touchEndTime = Date.now();
    const touchDuration = touchEndTime - (this.touchStartTime || 0);
    
    if (touchDuration < 200) {
        const overlay = this.querySelector('.client-overlay');
        if (overlay) {
            // Toggle overlay visibility
            if (overlay.classList.contains('opacity-0')) {
                // Show overlay
                overlay.classList.remove('opacity-0', 'invisible');
                overlay.classList.add('opacity-100', 'visible');
                this.setAttribute('data-overlay-visible', 'true');
            } else {
                // Hide overlay
                overlay.classList.remove('opacity-100', 'visible');
                overlay.classList.add('opacity-0', 'invisible');
                this.setAttribute('data-overlay-visible', 'false');
            }
        }
    }
}

// Handle click outside to close all overlays
document.addEventListener('click', function(e) {
    const visibleCards = document.querySelectorAll('.client-card[data-overlay-visible="true"]');
    
    visibleCards.forEach(card => {
        if (!card.contains(e.target)) {
            const overlay = card.querySelector('.client-overlay');
            if (overlay) {
                overlay.classList.remove('opacity-100', 'visible');
                overlay.classList.add('opacity-0', 'invisible');
                card.setAttribute('data-overlay-visible', 'false');
            }
        }
    });
});

// Call this function after loading clients or whenever new cards are added
document.addEventListener('DOMContentLoaded', function() {
    // Initial setup
    initializeClientCards();
    
    // Set up a MutationObserver to detect when new cards are added
    const clientsContainer = document.getElementById('clientsGrid');
    if (clientsContainer) {
        const observer = new MutationObserver(function(mutations) {
            initializeClientCards();
        });
        
        observer.observe(clientsContainer, { childList: true, subtree: true });
    }
});
</script>

<!-- Modify the fetchClients function to use Tailwind classes -->
<script>
// Modify your existing fetchClients function to use these Tailwind classes
function createClientCard(client) {
    // Function to get the base URL for resume files
    function getBaseUrl() {
        const pathArray = window.location.pathname.split('/');
        const baseSegments = pathArray.slice(0, pathArray.indexOf('Frontent') + 1);
        return window.location.origin + baseSegments.join('/');
    }

    // Function to get the full resume URL
    function getResumeUrl(resumePath) {
        if (!resumePath) return '';
        if (resumePath.startsWith('http')) return resumePath; // Handle legacy Cloudinary URLs
        return getBaseUrl() + '/' + resumePath;
    }

    return `
        <div class="client-card relative overflow-hidden rounded-lg shadow-md bg-white cursor-pointer">
            <!-- Image Container -->
            <div class="relative h-[300px] w-full overflow-hidden">
                <img 
                    src="${client.image_url || 'path/to/default-image.jpg'}" 
                    alt="${client.name}"
                    class="h-full w-full object-cover"
                    loading="lazy"
                >
                
                <!-- Category Badge -->
                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    ${client.category || client.professional}
                </div>
        </div>

            <!-- Overlay Content - Hidden by default, shown on click -->
            <div class="client-overlay hidden absolute inset-0 flex flex-col items-center justify-center bg-black/80 p-4 z-10">
                <h3 class="text-xl font-bold text-white mb-2">${client.name}</h3>
                <p class="text-sm text-white/80 mb-2"><i class="fas fa-map-marker-alt mr-1"></i> ${client.city || 'Location not specified'}</p>
                
                ${client.professional === 'Artist' ? 
                    `<p class="text-base text-white/90 mb-2">${client.category || 'N/A'}</p>
                     <p class="text-sm text-white/80 mb-3">Followers: <span class="font-semibold">${client.followers || '0'}</span></p>` : 
                    `<p class="text-base text-white/90 mb-2">${client.role || 'N/A'}</p>
                     <p class="text-sm text-white/80 mb-3">Experience: <span class="font-semibold">${client.experience || '0'} years</span></p>
                     ${client.resume_url ? 
                        `<a href="${getResumeUrl(client.resume_url)}" class="mb-3 px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors flex items-center" target="_blank" download>
                            <i class="fas fa-file-download mr-1.5"></i> Download Resume
                         </a>` : ''}`
                }
                
                
                ${client.description ? `<p class="text-sm text-white/70 mb-4 text-center">${client.description}</p>` : ''}
                
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSf4iUw2dXlqh8goIVK7AgQg3vPHSjmPZNotk6-CKojShrulVg/viewform" 
                   onclick="window.open(this.href, '_blank'); return false;"
                   class="mt-2 px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-300 font-semibold shadow-md flex items-center justify-center cursor-pointer">
                    ${client.professional === 'Employee' ? 'Hire Now' : 'Book Now'} <i class="fas fa-arrow-right ml-2"></i>
                </a>
        </div>

            <!-- Default Content -->
            <div class="p-4">
                <h3 class="text-lg font-bold text-gray-800">${client.name}</h3>
                <p class="text-xs text-gray-500 flex items-center"><i class="fas fa-map-marker-alt mr-1 text-red-500"></i> ${client.city || 'Location not specified'}</p>
                
                ${client.professional === 'Artist' ? 
                    `<p class="text-sm text-gray-600">${client.category || 'N/A'}</p>
                     <p class="text-xs text-gray-500">Followers: ${client.followers || '0'}</p>` : 
                    `<p class="text-sm text-gray-600">${client.role || 'N/A'}</p>
                     <p class="text-xs text-gray-500">Experience: ${client.experience || '0'} years</p>
                     ${client.resume_url ? 
                        `<a href="${getResumeUrl(client.resume_url)}" class="mt-1 text-xs text-blue-500 hover:text-blue-700 flex items-center" target="_blank" download>
                            <i class="fas fa-file-download mr-1"></i> Resume
                         </a>` : ''}`
                }
            </div>
        </div>
    `;
}
// ... existing code ...
</script>

<!-- Add this script for client card functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up click handlers for client cards
    setupClientCards();
});

function setupClientCards() {
    // Get all client cards
    const clientCards = document.querySelectorAll('.client-card');
    
    // Add click event to each card
    clientCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Prevent default behavior
            e.preventDefault();
            
            // Don't trigger if clicking on a link or button
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            // Get the overlay element
            const overlay = this.querySelector('.client-overlay');
            
            // Toggle overlay visibility
            if (overlay.classList.contains('hidden')) {
                // Hide all other overlays first
                document.querySelectorAll('.client-overlay:not(.hidden)').forEach(el => {
                    el.classList.add('hidden');
                });
                
                // Show this overlay
                overlay.classList.remove('hidden');
            } else {
                // Hide this overlay
                overlay.classList.add('hidden');
            }
        });
    });
    
    // Close overlay when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.client-card')) {
            document.querySelectorAll('.client-overlay:not(.hidden)').forEach(overlay => {
                overlay.classList.add('hidden');
            });
        }
    });
}

// Call this function after loading new client cards
window.initializeClientCards = setupClientCards;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const dropdowns = document.querySelectorAll('.dropdown');

    // Toggle mobile menu
    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    // Handle dropdowns on mobile
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        link.addEventListener('click', (e) => {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            }
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            mobileMenu.classList.add('hidden');
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    const dropdowns = document.querySelectorAll('.dropdown');

    // Toggle mobile menu
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    }

    // Handle dropdowns on mobile
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('a');
        if (link) {
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('active');
                }
            });
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (mobileMenu && !mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            mobileMenu.classList.add('hidden');
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });

    // Prevent mobile menu from closing when clicking inside it
    if (mobileMenu) {
        mobileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const profileSelectionModal = document.getElementById('profileSelectionModal');
    const addClientModal = document.getElementById('addClientModal');
    const artistButton = document.getElementById('artistButton');
    const employeeButton = document.getElementById('employeeButton');
    const createProfileBtn = document.getElementById('createProfileBtn');
    const mobileCreateProfileBtn = document.getElementById('mobileCreateProfileBtn');
    const closeSelectionBtn = document.getElementById('closeSelectionBtn');
    const closeClientFormBtn = document.getElementById('closeClientFormBtn');
    
    // Function to show profile selection modal
    function showProfileModal() {
        console.log('Opening profile selection modal');
        if (profileSelectionModal) {
            profileSelectionModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            // Add animation to the modal
            setTimeout(function() {
                const modalContent = profileSelectionModal.querySelector('.transform');
                if (modalContent) {
                    modalContent.style.transform = 'scale(1)';
                    modalContent.style.opacity = '1';
                }
            }, 10);
        }
    }
    
    // Function to close profile selection modal
    function closeProfileModal() {
        console.log('Closing profile selection modal');
        if (profileSelectionModal) {
            const modalContent = profileSelectionModal.querySelector('.transform');
            if (modalContent) {
                modalContent.style.transform = 'scale(0.95)';
                modalContent.style.opacity = '0';
            }
            
            setTimeout(function() {
                profileSelectionModal.style.display = 'none';
                if (!addClientModal || addClientModal.style.display === 'none') {
                    document.body.classList.remove('modal-open');
                }
            }, 300);
        }
    }
    
    // Function to show client form modal
    function showClientForm(type) {
        console.log('Opening client form for:', type);
        closeProfileModal();
        
        if (addClientModal) {
            addClientModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            // Set professional type
            const professionalField = document.getElementById('professionalField');
            if (professionalField) {
                professionalField.value = type;
                console.log('Set professional type to:', type);
            }
            
            // Toggle fields visibility if artist/employee-specific fields exist
            const artistFields = document.getElementById('artistFields');
            const employeeFields = document.getElementById('employeeFields');
            
            if (artistFields && employeeFields) {
                artistFields.style.display = type === 'Artist' ? 'block' : 'none';
                employeeFields.style.display = type === 'Employee' ? 'block' : 'none';
                console.log('Toggled fields for:', type);
            }
        }
    }
    
    // Function to close client form modal
    function closeClientForm() {
        console.log('Closing client form');
        if (addClientModal) {
            addClientModal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    }

    // Check if URL has query parameter to open modal directly
    const urlParams = new URLSearchParams(window.location.search);
    const openModal = urlParams.get('openModal');
    
    if (openModal === 'true') {
        // Open the selection modal automatically when query parameter is present
        setTimeout(function() {
            showProfileModal();
        }, 500);
    }
    
    // Event Listeners
    if (createProfileBtn) {
        createProfileBtn.addEventListener('click', showProfileModal);
    }
    
    if (artistButton) {
        artistButton.addEventListener('click', function() {
            showClientForm('Artist');
        });
    }
    
    if (employeeButton) {
        employeeButton.addEventListener('click', function() {
            showClientForm('Employee');
        });
    }
    
    if (closeSelectionBtn) {
        closeSelectionBtn.addEventListener('click', closeProfileModal);
    }
    
    if (closeClientFormBtn) {
        closeClientFormBtn.addEventListener('click', closeClientForm);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === profileSelectionModal) {
            closeProfileModal();
        }
        
        if (event.target === addClientModal) {
            closeClientForm();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProfileModal();
            closeClientForm();
        }
    });
});
</script>

<script>
// Add this script at the end of the file, before the closing </body> tag
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to both artist and employee image inputs
    const artistImageInput = document.getElementById('artistImage');
    const employeeImageInput = document.getElementById('employeeImage');
    
    if (artistImageInput) {
        artistImageInput.addEventListener('change', function(e) {
            handleImageSelection(e);
        });
    }
    
    if (employeeImageInput) {
        employeeImageInput.addEventListener('change', function(e) {
            handleImageSelection(e);
        });
    }
    
    // Function to handle image selection
    function handleImageSelection(e) {
        const file = e.target.files[0];
        if (file) {
            // Check if file is larger than 5MB
            if (file.size > 5 * 1024 * 1024) {
                // Compress the image before upload
                compressImage(file, function(compressedFile) {
                    // Replace the file in the input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    e.target.files = dataTransfer.files;
                });
            }
        }
    }
    
    // Function to compress image
    function compressImage(file, callback) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Calculate new dimensions (maintain aspect ratio)
                let width = img.width;
                let height = img.height;
                const MAX_WIDTH = 1280;
                const MAX_HEIGHT = 1280;
                
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                
                canvas.width = width;
                canvas.height = height;
                
                // Draw image on canvas with new dimensions
                ctx.drawImage(img, 0, 0, width, height);
                
                // Convert to Blob with reduced quality
                canvas.toBlob(function(blob) {
                    // Create a new file from the blob
                    const compressedFile = new File([blob], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    
                    callback(compressedFile);
                }, 'image/jpeg', 0.7); // Adjust quality (0.7 = 70% quality)
            };
        };
    }
});
</script>



</body>
</html>


