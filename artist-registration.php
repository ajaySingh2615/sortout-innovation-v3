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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>Artist Registration - Sortout Innovation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/registration.css">
    
    <style>
        /* Critical navbar visibility fix */
        .navbar .navbar-nav { display: flex !important; }
        .navbar .nav-item { display: block !important; }
        .navbar .nav-link { display: block !important; color: #333 !important; }
        .navbar .navbar-collapse { display: block !important; }
        @media (min-width: 992px) {
            .navbar .navbar-collapse { display: flex !important; }
            .navbar .navbar-nav { flex-direction: row !important; }
        }
        @media (max-width: 991.98px) {
            .navbar .navbar-collapse { display: none !important; }
            .navbar .navbar-collapse.show { display: block !important; }
        }
        
        /* Page specific styles */
        .artist-form-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 1rem 0;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.2);
            backdrop-filter: blur(10px);
            margin: 0 auto;
        }
        
        .form-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 1rem;
            text-align: center;
        }
        
        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .artist-form-container {
                padding: 0.5rem;
            }
            
            .form-card {
                border-radius: 15px;
                margin: 0;
            }
            
            .form-header {
                padding: 1rem;
                border-radius: 15px 15px 0 0;
            }
            
            .form-header h1 {
                font-size: 1.75rem !important;
            }
            
            .form-header .w-20 {
                width: 4rem !important;
                height: 4rem !important;
            }
            
            .form-header .w-20 i {
                font-size: 1.5rem !important;
            }
            
            .p-8 {
                padding: 1rem !important;
            }
            
            .space-y-8 > * + * {
                margin-top: 1.5rem !important;
            }
            
            .bg-red-50 {
                padding: 1rem !important;
                border-radius: 1rem !important;
            }
            
            .grid-cols-1.md\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            .gap-6 {
                gap: 1rem !important;
            }
            
            .text-lg {
                font-size: 1rem !important;
            }
            
            .px-4.py-3 {
                padding: 0.75rem !important;
            }
            
            .rounded-xl {
                border-radius: 0.75rem !important;
            }
            
            .min-h-\[120px\] {
                min-height: 100px !important;
            }
            
            /* Submit button mobile styles */
            .px-8.py-4 {
                padding: 1rem !important;
            }
            
            .text-lg {
                font-size: 1rem !important;
            }
            
            /* Modal mobile styles */
            #successMessage .max-w-md {
                max-width: 90vw !important;
                margin: 1rem !important;
            }
            
            /* City dropdown mobile styles */
            #cityDropdown {
                max-height: 200px !important;
            }
            
            .city-option {
                padding: 0.75rem !important;
            }
            
            /* File input mobile styles */
            .border-2.border-dashed {
                padding: 1rem !important;
            }
            
            .file\:py-2.file\:px-4 {
                font-size: 0.875rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .artist-form-container {
                padding: 0.25rem;
            }
            
            .form-header h1 {
                font-size: 1.5rem !important;
            }
            
            .form-header p {
                font-size: 0.875rem !important;
            }
            
            .p-8 {
                padding: 0.75rem !important;
            }
            
            .bg-red-50 {
                padding: 0.75rem !important;
            }
            
            .px-4.py-3 {
                padding: 0.5rem !important;
            }
            
            .text-sm {
                font-size: 0.75rem !important;
            }
            
            #successMessage .p-8 {
                padding: 1.5rem !important;
            }
            
                         #successMessage .text-2xl {
                 font-size: 1.25rem !important;
             }
             
             /* Touch-friendly improvements */
             input, select, textarea {
                 -webkit-appearance: none;
                 -moz-appearance: none;
                 appearance: none;
                 font-size: 16px !important; /* Prevents zoom on iOS */
             }
             
             button {
                 -webkit-tap-highlight-color: transparent;
                 touch-action: manipulation;
             }
             
             /* Better touch targets */
             .city-option, button, input[type="file"] {
                 min-height: 44px !important;
             }
             
             /* Improve file input on mobile */
             input[type="file"] {
                 padding: 1rem !important;
                 border: 2px dashed #e5e7eb !important;
                 background: #f9fafb !important;
                 text-align: center !important;
             }
             
             /* Better modal positioning */
             #successMessage {
                 padding: 1rem !important;
                 align-items: flex-start !important;
                 padding-top: 2rem !important;
             }
             
             /* Compression status mobile styles */
             .compression-status, .compression-progress, .compression-error {
                 font-size: 0.75rem !important;
                 padding: 0.75rem !important;
                 border-radius: 0.5rem !important;
             }
             
             .compression-status strong, .compression-progress strong, .compression-error strong {
                 font-size: 0.75rem !important;
             }
         }
    </style>
</head>
<body>
    <?php 
    // Set navbar parameters for artist registration page
    $currentPage = 'talent';
    $showCreateProfile = false; // Hide create profile button since this is the form
    include 'components/navbar/navbar.php'; 
    ?>

    <!-- ✅ Success Message Modal -->
    <div id="successMessage" 
         class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]" style="display: none !important; transition: all 0.4s ease-out;">
        <div id="successModalContent" class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform" style="transform: scale(0.95); opacity: 0; transition: all 0.4s ease-out;">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-500"></i>
                </div>

                <!-- Success Message -->
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Profile Submitted Successfully!</h3>
                <p class="text-gray-600 mb-3">Thank you for submitting your artist profile. Our team will review your submission and contact you soon for collaboration opportunities.</p>
                
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

    <!-- Artist Registration Form -->
    <div class="artist-form-container">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="max-w-4xl mx-auto w-full">
                <div class="form-card">
                    <!-- Form Header -->
                    <div class="form-header">
                        <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-white text-3xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold mb-2">Artist Registration</h1>
                        <p class="text-red-100">Join our creative network and showcase your talent</p>
                    </div>

                    <!-- Form Content -->
                    <div class="p-8">
                        <form id="artistRegistrationForm" method="POST" action="admin/add_client.php" enctype="multipart/form-data" class="space-y-8">
                            <!-- Hidden field to indicate this is an artist form -->
                            <input type="hidden" name="professional" value="Artist">

                            <!-- Basic Information Section -->
                            <div class="bg-red-50 rounded-2xl p-6">
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
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                    Location Information
                                </h3>
                                
                                <!-- City -->
                                <div class="form-group">
                                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-city text-gray-400 mr-1"></i>
                                        City *
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="city" 
                                               name="city" 
                                               required 
                                               autocomplete="off"
                                               placeholder="Type your city name (minimum 3 characters)..."
                                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm">
                                        
                                        <!-- Loading indicator -->
                                        <div id="cityLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        </div>
                                        
                                        <!-- Dropdown for city suggestions -->
                                        <div id="cityDropdown" 
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                            <!-- City options will be populated dynamically -->
                                        </div>
                                    </div>
                                    <div id="cityError" class="mt-2 text-sm text-red-600 hidden">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <span id="cityErrorText"></span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                                        <i class="fas fa-info-circle text-red-500 mr-1"></i>
                                        Start typing to search for cities or enter your city manually
                                    </p>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-envelope text-red-500 mr-2"></i>
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

                            <!-- Professional Information -->
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-star text-red-500 mr-2"></i>
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
                                        <input type="text" id="followers" name="followers" 
                                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white shadow-sm"
                                               placeholder="e.g., 10K, 50K, 100K">
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media & Payment Information -->
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fab fa-instagram text-red-500 mr-2"></i>
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
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-briefcase text-red-500 mr-2"></i>
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
                                        <i class="fas fa-info-circle text-red-500 mr-1"></i>
                                        Hold Ctrl/Cmd to select multiple options
                                    </p>
                                </div>
                            </div>

                            <!-- Profile Image -->
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-camera text-red-500 mr-2"></i>
                                    Profile Image
                                </h3>
                                <div class="form-group">
                                    <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-camera text-gray-400 mr-1"></i>
                                        Profile Image *
                                    </label>
                                    <div class="relative">
                                        <input type="file" id="image" name="image" accept="image/*" required
                                               class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 bg-white
                                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                                                      file:bg-gradient-to-r file:from-red-500 file:to-red-600 file:text-white 
                                                      hover:file:from-red-600 hover:file:to-red-700 file:transition-all file:duration-200">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 bg-white p-2 rounded-lg">
                                        <i class="fas fa-info-circle text-red-500 mr-1"></i>
                                        Choose from gallery or take a photo with your camera (9:16 aspect ratio recommended)
                                    </p>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="bg-red-50 rounded-2xl p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-red-500 mr-2"></i>
                                    Additional Information
                                </h3>
                                
                                <!-- Languages -->
                                <div class="form-group">
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
                                        <i class="fas fa-info-circle text-red-500 mr-1"></i>
                                        Hold Ctrl/Cmd to select multiple languages
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button Section -->
                            <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-2xl p-6 mt-8">
                                <div class="form-group">
                                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        <span>Submit Profile</span>
                                        <div class="loading-spinner ml-2 hidden">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const artistForm = document.getElementById('artistRegistrationForm');
        
        if (artistForm) {
            let isSubmitting = false;
            
            artistForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (isSubmitting) {
                    console.log('Form is already being submitted, ignoring...');
                    return false;
                }
                
                // Validate required fields
                const requiredFields = ['name', 'age', 'phone', 'gender', 'city', 'image'];
                let isValid = true;
                
                for (const fieldName of requiredFields) {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field || (field.type === 'file' && field.files.length === 0) || (!field.value && field.type !== 'file')) {
                        isValid = false;
                        if (field) {
                            field.focus();
                            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        alert(`Please fill in the required field: ${fieldName}`);
                        break;
                    }
                }
                
                // Validate languages selection
                const languagesField = document.querySelector('[name="languages[]"]');
                if (languagesField && languagesField.selectedOptions.length === 0) {
                    isValid = false;
                    languagesField.focus();
                    languagesField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    alert('Please select at least one language');
                }
                
                if (!isValid) {
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

                    let result;
                    if (response.ok && response.status === 200) {
                        const text = await response.text();
                        if (!text || text.trim() === '') {
                            result = { status: 'success' };
                        } else {
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
                        // Show success message
                        const successMessage = document.getElementById('successMessage');
                        if (successMessage) {
                            console.log('Showing success message modal');
                            successMessage.style.display = 'flex';
                            successMessage.style.opacity = '0';
                            document.body.classList.add('modal-open');
                            
                            setTimeout(() => {
                                const modalContent = document.getElementById('successModalContent');
                                if (modalContent && successMessage) {
                                    modalContent.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                                    successMessage.style.transition = 'all 0.4s ease-out';
                                    
                                    modalContent.style.transform = 'scale(1)';
                                    modalContent.style.opacity = '1';
                                    successMessage.style.opacity = '1';
                                }
                            }, 50);
                        }

                        // Reset the form
                        this.reset();
                    } else {
                        alert(result.message || 'Error submitting form. Please try again.');
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    alert('Error submitting form. Please try again.');
                } finally {
                    isSubmitting = false;
                    submitButton.disabled = false;
                    buttonText.textContent = 'Submit Profile';
                    if (loadingSpinner) {
                        loadingSpinner.classList.add('hidden');
                    }
                }
                
                return false;
            });
        }
        
        // Mobile keyboard and scroll handling
        function handleMobileKeyboard() {
            // Prevent viewport zoom on input focus for iOS
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // Scroll element into view with offset for mobile keyboards
                    setTimeout(() => {
                        const rect = this.getBoundingClientRect();
                        const isInViewport = rect.top >= 0 && rect.bottom <= window.innerHeight;
                        
                        if (!isInViewport) {
                            this.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center',
                                inline: 'nearest'
                            });
                        }
                    }, 300); // Wait for keyboard animation
                });
                
                // Handle mobile select dropdowns
                if (input.tagName === 'SELECT' && input.hasAttribute('multiple')) {
                    input.addEventListener('touchstart', function(e) {
                        // Improve touch handling for multi-select
                        e.stopPropagation();
                    });
                }
            });
            
            // Handle orientation changes
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    // Re-adjust viewport after orientation change
                    window.scrollTo(0, window.scrollY + 1);
                    window.scrollTo(0, window.scrollY - 1);
                }, 500);
            });
            
            // Improve file input handling on mobile
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Show file name on mobile
                        const label = this.previousElementSibling || this.parentElement.querySelector('label');
                        if (label) {
                            const originalText = label.textContent;
                            label.innerHTML = `${originalText} <span style="color: #059669; font-size: 0.875rem;">(${fileName})</span>`;
                        }
                    }
                });
            });
        }
        
        // Initialize mobile enhancements
        if (window.innerWidth <= 768) {
            handleMobileKeyboard();
        }
        
        // Image compression functionality
        function compressImage(file, maxWidth = 1280, maxHeight = 1280, quality = 0.8) {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                
                img.onload = function() {
                    // Calculate new dimensions while maintaining aspect ratio
                    let { width, height } = calculateDimensions(img.width, img.height, maxWidth, maxHeight);
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    // Draw and compress
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob((blob) => {
                        // Create new file with compressed data
                        const compressedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    }, 'image/jpeg', quality);
                };
                
                img.src = URL.createObjectURL(file);
            });
        }
        
        function calculateDimensions(width, height, maxWidth, maxHeight) {
            if (width <= maxWidth && height <= maxHeight) {
                return { width, height };
            }
            
            const aspectRatio = width / height;
            
            if (width > height) {
                width = maxWidth;
                height = width / aspectRatio;
                if (height > maxHeight) {
                    height = maxHeight;
                    width = height * aspectRatio;
                }
            } else {
                height = maxHeight;
                width = height * aspectRatio;
                if (width > maxWidth) {
                    width = maxWidth;
                    height = width / aspectRatio;
                }
            }
            
            return { width: Math.round(width), height: Math.round(height) };
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function showCompressionStatus(element, originalSize, compressedSize, fileName) {
            const statusDiv = element.parentElement.querySelector('.compression-status') || document.createElement('div');
            statusDiv.className = 'compression-status mt-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm';
            statusDiv.innerHTML = `
                <div class="flex items-center text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="font-semibold">Image Compressed Successfully!</span>
                </div>
                <div class="mt-1 text-green-700">
                    <div><strong>File:</strong> ${fileName}</div>
                    <div><strong>Original:</strong> ${formatFileSize(originalSize)} → <strong>Compressed:</strong> ${formatFileSize(compressedSize)}</div>
                    <div><strong>Saved:</strong> ${formatFileSize(originalSize - compressedSize)} (${Math.round(((originalSize - compressedSize) / originalSize) * 100)}%)</div>
                </div>
            `;
            
            if (!element.parentElement.querySelector('.compression-status')) {
                element.parentElement.appendChild(statusDiv);
            }
        }
        
        function showCompressionProgress(element, show = true) {
            let progressDiv = element.parentElement.querySelector('.compression-progress');
            
            if (show) {
                if (!progressDiv) {
                    progressDiv = document.createElement('div');
                    progressDiv.className = 'compression-progress mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm';
                    element.parentElement.appendChild(progressDiv);
                }
                progressDiv.innerHTML = `
                    <div class="flex items-center text-blue-800">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span class="font-semibold">Compressing image...</span>
                    </div>
                    <div class="mt-1 text-blue-700">Please wait while we optimize your image for faster upload.</div>
                `;
            } else {
                if (progressDiv) {
                    progressDiv.remove();
                }
            }
        }
        
        async function handleImageUpload(input, file) {
            const maxSizeBytes = 5 * 1024 * 1024; // 5MB
            const shouldCompress = file.size > maxSizeBytes || file.size > 1 * 1024 * 1024; // Compress if > 5MB or > 1MB
            
            // Remove previous status messages
            const existingStatus = input.parentElement.querySelectorAll('.compression-status, .compression-progress, .compression-error');
            existingStatus.forEach(el => el.remove());
            
            if (!shouldCompress) {
                // File is small enough, but still show info
                const infoDiv = document.createElement('div');
                infoDiv.className = 'compression-info mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg text-sm';
                infoDiv.innerHTML = `
                    <div class="flex items-center text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="font-semibold">Image ready for upload</span>
                    </div>
                    <div class="mt-1 text-blue-700 text-xs">
                        File size: ${formatFileSize(file.size)} - Optimal size for web
                    </div>
                `;
                input.parentElement.appendChild(infoDiv);
                return file;
            }
            
            try {
                showCompressionProgress(input, true);
                
                // Professional compression settings based on file size
                let maxWidth = 1080, maxHeight = 1920, quality = 0.85; // Default: Full HD portrait
                
                if (file.size > 10 * 1024 * 1024) { // > 10MB - Aggressive compression
                    maxWidth = 720;
                    maxHeight = 1280;
                    quality = 0.75;
                } else if (file.size > 5 * 1024 * 1024) { // > 5MB - Moderate compression
                    maxWidth = 900;
                    maxHeight = 1600;
                    quality = 0.8;
                } else if (file.size > 3 * 1024 * 1024) { // > 3MB - Light compression
                    maxWidth = 1080;
                    maxHeight = 1920;
                    quality = 0.85;
                }
                
                const compressedFile = await compressImage(file, maxWidth, maxHeight, quality);
                
                showCompressionProgress(input, false);
                showCompressionStatus(input, file.size, compressedFile.size, file.name);
                
                return compressedFile;
                
            } catch (error) {
                console.error('Compression failed:', error);
                showCompressionProgress(input, false);
                
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'compression-error mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm';
                errorDiv.innerHTML = `
                    <div class="flex items-center text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span class="font-semibold">Compression failed</span>
                    </div>
                    <div class="mt-1 text-red-700">Using original image. If upload fails, try a smaller image.</div>
                `;
                input.parentElement.appendChild(errorDiv);
                
                return file; // Return original file if compression fails
            }
        }
        
        // Initialize image compression for artist image input
        const artistImageInput = document.getElementById('image');
        if (artistImageInput) {
            artistImageInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const processedFile = await handleImageUpload(this, file);
                    
                    // Replace file in input with compressed version
                    if (processedFile !== file) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(processedFile);
                        this.files = dataTransfer.files;
                    }
                }
            });
        }
        
        // Function to close success message
        window.closeSuccessMessage = function() {
            const successMessage = document.getElementById('successMessage');
            const modalContent = document.getElementById('successModalContent');
            if (successMessage && modalContent) {
                modalContent.style.transform = 'scale(0.85)';
                modalContent.style.opacity = '0';
                modalContent.style.transition = 'all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1)';
                
                successMessage.style.transition = 'all 0.4s ease-out';
                successMessage.style.opacity = '0';
                
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    successMessage.style.opacity = '';
                    modalContent.style.transition = '';
                    document.body.classList.remove('modal-open');
                }, 400);
            }
        }
    });
    </script>

    <!-- City Search Script -->
    <script>
    // Dynamic City Search using GeoDB Cities API (same as employee form)
    document.addEventListener('DOMContentLoaded', function() {
        const cityInput = document.getElementById('city');
        const cityDropdown = document.getElementById('cityDropdown');
        const cityLoading = document.getElementById('cityLoading');
        const cityError = document.getElementById('cityError');
        const cityErrorText = document.getElementById('cityErrorText');
        
        if (!cityInput || !cityDropdown || !cityLoading) {
            console.log('City search elements not found');
            return;
        }
        
        // API configuration
        const API_KEY = '47947e74c2mshca12d801e45fd20p1266f6jsn338754475961';
        const API_HOST = 'wft-geo-db.p.rapidapi.com';
        const API_URL = 'https://wft-geo-db.p.rapidapi.com/v1/geo/cities';
        
        let debounceTimer;
        let currentRequest;
        let isApiWorking = true;
        let rateLimitHit = false;
        let cooldownUntil = 0;
        let requestCount = 0;
        let lastMinuteReset = Date.now();
        
        const MAX_REQUESTS_PER_MINUTE = 3;
        const RATE_LIMIT_COOLDOWN = 180000;
        const DEBOUNCE_DELAY = 800;
        
        function showError(message) {
            if (cityError && cityErrorText) {
                cityErrorText.textContent = message;
                cityError.classList.remove('hidden');
                setTimeout(() => {
                    hideError();
                }, 5000);
            }
        }
        
        function hideError() {
            if (cityError) {
                cityError.classList.add('hidden');
            }
        }
        
        function checkRateLimit() {
            const now = Date.now();
            
            if (rateLimitHit && now < cooldownUntil) {
                const remainingMinutes = Math.ceil((cooldownUntil - now) / 60000);
                showError(`City search is temporarily limited. Please try again in ${remainingMinutes} minute(s) or type manually.`);
                return false;
            }
            
            if (rateLimitHit && now >= cooldownUntil) {
                rateLimitHit = false;
                requestCount = 0;
                lastMinuteReset = now;
            }
            
            if (now - lastMinuteReset >= 60000) {
                requestCount = 0;
                lastMinuteReset = now;
            }
            
            if (requestCount >= MAX_REQUESTS_PER_MINUTE) {
                rateLimitHit = true;
                cooldownUntil = now + RATE_LIMIT_COOLDOWN;
                showError('Too many city searches. Please wait a few minutes or type your city manually.');
                return false;
            }
            
            return true;
        }
        
        async function searchCities(query) {
            if (query.length < 2) {
                hideCityDropdown();
                hideError();
                return;
            }
            
            if (!isApiWorking) {
                return;
            }
            
            if (!checkRateLimit()) {
                hideCityDropdown();
                return;
            }
            
            if (currentRequest) {
                currentRequest.abort();
            }
            
            requestCount++;
            showLoading();
            hideError();
            
            try {
                const controller = new AbortController();
                currentRequest = controller;
                
                const url = `${API_URL}?countryIds=IN&namePrefix=${encodeURIComponent(query)}&limit=10`;
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-RapidAPI-Key': API_KEY,
                        'X-RapidAPI-Host': API_HOST
                    },
                    signal: controller.signal
                });
                
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}`);
                }
                
                const data = await response.json();
                
                if (controller.signal.aborted) {
                    return;
                }
                
                hideLoading();
                displayCities(data.data || []);
                
            } catch (error) {
                hideLoading();
                
                if (error.name === 'AbortError') {
                    return;
                }
                
                console.error('Error fetching cities:', error);
                
                if (error.message.includes('429')) {
                    rateLimitHit = true;
                    cooldownUntil = Date.now() + RATE_LIMIT_COOLDOWN;
                    showError('City search limit reached. Please wait a few minutes or type your city manually.');
                } else if (error.message.includes('500') || error.message.includes('502') || error.message.includes('503')) {
                    showError('City search service is temporarily down. Please type your city manually.');
                } else if (error.message.includes('403') || error.message.includes('401')) {
                    isApiWorking = false;
                    showError('City search is unavailable. Please type your city manually.');
                } else {
                    showError('Unable to search cities. Please check your internet connection or type manually.');
                }
                
                hideCityDropdown();
            }
        }
        
        function displayCities(cities) {
            if (cities.length === 0) {
                const currentValue = cityInput.value.trim();
                cityDropdown.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500 border-b border-gray-100">
                        <i class="fas fa-search mr-2"></i>
                        No cities found for "${currentValue}". 
                        <span class="text-red-600">You can continue typing your city name manually.</span>
                    </div>
                `;
                showCityDropdown();
                return;
            }
            
            let html = '';
            
            const currentValue = cityInput.value.trim();
            if (currentValue.length >= 2) {
                html += `
                    <div class="city-option px-4 py-3 cursor-pointer hover:bg-red-50 border-b border-gray-100 transition-colors duration-200 bg-red-25" 
                         data-city="${currentValue}" 
                         data-manual="true">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-red-500 mr-3"></i>
                            <div>
                                <div class="font-medium text-red-900">Use "${currentValue}"</div>
                                <div class="text-sm text-red-600">Enter manually</div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            cities.forEach(city => {
                const cityName = city.name;
                const region = city.region ? `, ${city.region}` : '';
                const country = city.country ? `, ${city.country}` : '';
                
                html += `
                    <div class="city-option px-4 py-3 cursor-pointer hover:bg-gray-50 border-b border-gray-100 transition-colors duration-200" 
                         data-city="${cityName}">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-green-500 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900">${cityName}</div>
                                ${region ? `<div class="text-sm text-gray-500">${region.substring(2)}${country}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            cityDropdown.innerHTML = html;
            showCityDropdown();
            
            const cityOptions = cityDropdown.querySelectorAll('.city-option');
            cityOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const cityName = this.getAttribute('data-city');
                    selectCity(cityName);
                });
            });
        }
        
        function selectCity(cityName) {
            cityInput.value = cityName;
            hideCityDropdown();
            cityInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        function showLoading() {
            cityLoading.classList.remove('hidden');
        }
        
        function hideLoading() {
            cityLoading.classList.add('hidden');
        }
        
        function showCityDropdown() {
            cityDropdown.classList.remove('hidden');
        }
        
        function hideCityDropdown() {
            cityDropdown.classList.add('hidden');
        }
        
        function debouncedSearch(query) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchCities(query);
            }, DEBOUNCE_DELAY);
        }
        
        cityInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            hideError();
            
            if (query.length >= 3 && isApiWorking && !rateLimitHit) {
                debouncedSearch(query);
            } else if (query.length < 2) {
                hideCityDropdown();
            } else if (query.length >= 2 && (rateLimitHit || !isApiWorking)) {
                displayCities([]);
            }
        });
        
        cityInput.addEventListener('focus', function(e) {
            const query = e.target.value.trim();
            hideError();
            
            if (query.length >= 3 && isApiWorking && !rateLimitHit) {
                debouncedSearch(query);
            } else if (query.length >= 2 && (rateLimitHit || !isApiWorking)) {
                displayCities([]);
            }
        });
        
        cityInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value.length >= 2) {
                hideError();
            }
            setTimeout(() => {
                hideCityDropdown();
            }, 200);
        });
        
        document.addEventListener('click', function(e) {
            if (!cityInput.contains(e.target) && !cityDropdown.contains(e.target)) {
                hideCityDropdown();
            }
        });
    });
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
