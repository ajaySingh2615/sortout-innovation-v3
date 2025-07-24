<?php
require_once '../../../auth/auth.php';
require_once '../../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage apps and categories.";
    exit();
}

// Function to create a default image
function createDefaultImage($path, $text, $width = 512, $height = 512) {
    // Create the directory if it doesn't exist
    $dir = dirname($path);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Create image resource
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg_color = imagecolorallocate($image, 52, 58, 64); // Dark gray background
    $text_color = imagecolorallocate($image, 255, 255, 255); // White text
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Add text
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Save the image
    imagejpeg($image, $path, 90);
    
    // Free up memory
    imagedestroy($image);
    
    return file_exists($path);
}

// Define paths for default images
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/';
$localPath = dirname(__DIR__) . '/';

// Create default app image in both root and admin directories
$defaultAppImageRoot = $rootPath . 'assets/img/default-app.jpg';
$defaultAppImageAdmin = $localPath . 'assets/img/default-app.jpg';

// Create admin assets directory if it doesn't exist
if (!file_exists($localPath . 'assets/img')) {
    mkdir($localPath . 'assets/img', 0777, true);
}

echo "<h1>Creating Default Images</h1>";

// Create default app image
if (!file_exists($defaultAppImageRoot)) {
    if (createDefaultImage($defaultAppImageRoot, 'Default App')) {
        echo "<p>Created default app image at: {$defaultAppImageRoot}</p>";
    } else {
        echo "<p>Failed to create default app image at: {$defaultAppImageRoot}</p>";
    }
}

if (!file_exists($defaultAppImageAdmin)) {
    if (createDefaultImage($defaultAppImageAdmin, 'Default App')) {
        echo "<p>Created default app image at: {$defaultAppImageAdmin}</p>";
    } else {
        echo "<p>Failed to create default app image at: {$defaultAppImageAdmin}</p>";
    }
}

// Create default category image in both root and admin directories
$defaultCategoryImageRoot = $rootPath . 'assets/img/default-category.jpg';
$defaultCategoryImageAdmin = $localPath . 'assets/img/default-category.jpg';

if (!file_exists($defaultCategoryImageRoot)) {
    if (createDefaultImage($defaultCategoryImageRoot, 'Default Category')) {
        echo "<p>Created default category image at: {$defaultCategoryImageRoot}</p>";
    } else {
        echo "<p>Failed to create default category image at: {$defaultCategoryImageRoot}</p>";
    }
}

if (!file_exists($defaultCategoryImageAdmin)) {
    if (createDefaultImage($defaultCategoryImageAdmin, 'Default Category')) {
        echo "<p>Created default category image at: {$defaultCategoryImageAdmin}</p>";
    } else {
        echo "<p>Failed to create default category image at: {$defaultCategoryImageAdmin}</p>";
    }
}

// Create apps directory in both root and admin
$appsRootDir = $rootPath . 'assets/img/apps/';
$appsAdminDir = $localPath . 'assets/img/apps/';

if (!file_exists($appsRootDir)) {
    if (mkdir($appsRootDir, 0777, true)) {
        echo "<p>Created apps image directory at: {$appsRootDir}</p>";
    } else {
        echo "<p>Failed to create apps image directory at: {$appsRootDir}</p>";
    }
}

if (!file_exists($appsAdminDir)) {
    if (mkdir($appsAdminDir, 0777, true)) {
        echo "<p>Created apps image directory at: {$appsAdminDir}</p>";
    } else {
        echo "<p>Failed to create apps image directory at: {$appsAdminDir}</p>";
    }
}

// Create categories directory in both root and admin
$categoriesRootDir = $rootPath . 'assets/img/categories/';
$categoriesAdminDir = $localPath . 'assets/img/categories/';

if (!file_exists($categoriesRootDir)) {
    if (mkdir($categoriesRootDir, 0777, true)) {
        echo "<p>Created categories image directory at: {$categoriesRootDir}</p>";
    } else {
        echo "<p>Failed to create categories image directory at: {$categoriesRootDir}</p>";
    }
}

if (!file_exists($categoriesAdminDir)) {
    if (mkdir($categoriesAdminDir, 0777, true)) {
        echo "<p>Created categories image directory at: {$categoriesAdminDir}</p>";
    } else {
        echo "<p>Failed to create categories image directory at: {$categoriesAdminDir}</p>";
    }
}

// Add support for checking and creating hero image
$heroImageRoot = $rootPath . 'assets/img/apps/hero-image.jpg';
$heroImageAdmin = $localPath . 'assets/img/apps/hero-image.jpg';

if (!file_exists($heroImageRoot)) {
    if (createDefaultImage($heroImageRoot, 'App Directory', 1200, 600)) {
        echo "<p>Created hero image at: {$heroImageRoot}</p>";
    } else {
        echo "<p>Failed to create hero image at: {$heroImageRoot}</p>";
    }
}

if (!file_exists($heroImageAdmin)) {
    if (createDefaultImage($heroImageAdmin, 'App Directory', 1200, 600)) {
        echo "<p>Created hero image at: {$heroImageAdmin}</p>";
    } else {
        echo "<p>Failed to create hero image at: {$heroImageAdmin}</p>";
    }
}

echo "<p><a href='../artist_dashboard.php?page=setup'>Return to Setup</a></p>";
?> 