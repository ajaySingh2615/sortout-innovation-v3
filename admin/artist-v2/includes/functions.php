<?php
// Database connection is already included in the files that use these functions

/**
 * Function to sanitize user input
 * @param string $data - The data to sanitize
 * @return string - The sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Function to handle image upload to local storage
 * @param array $file - The file data from $_FILES
 * @param string $type - The type of image (apps or categories)
 * @return string|false - The image URL or false on failure
 */
function uploadImage($file, $type = 'apps') {
    if ($file['error'] !== 0) {
        error_log('File upload error code: ' . $file['error']);
        return false;
    }
    
    // Check if the file is an image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        error_log('Invalid file type: ' . $file['type'] . '. Allowed types: ' . implode(', ', $allowedTypes));
        return false;
    }
    
    // Determine both admin and root upload directories
    $adminUploadDir = __DIR__ . '/../../assets/img/' . $type . '/';
    $rootUploadDir = __DIR__ . '/../../../assets/img/' . $type . '/';
    
    // Create directories if they don't exist
    if (!file_exists($adminUploadDir)) {
        if (!mkdir($adminUploadDir, 0777, true)) {
            error_log('Failed to create admin upload directory: ' . $adminUploadDir);
        }
    }
    
    if (!file_exists($rootUploadDir)) {
        if (!mkdir($rootUploadDir, 0777, true)) {
            error_log('Failed to create root upload directory: ' . $rootUploadDir);
        }
    }
    
    // Generate a unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $adminTargetPath = $adminUploadDir . $filename;
    $rootTargetPath = $rootUploadDir . $filename;
    
    // Move the uploaded file to admin directory
    $adminSuccess = move_uploaded_file($file['tmp_name'], $adminTargetPath);
    
    if ($adminSuccess) {
        // Copy the file to root directory
        if (copy($adminTargetPath, $rootTargetPath)) {
            // Ensure permissions are correct
            chmod($adminTargetPath, 0644);
            chmod($rootTargetPath, 0644);
        } else {
            error_log('Failed to copy to root directory: ' . $rootTargetPath);
        }
        
        // Return the path that will be stored in the database
        return 'assets/img/' . $type . '/' . $filename;
    } else {
        error_log('Failed to upload file: ' . error_get_last()['message']);
        return false;
    }
}

/**
 * Function to ensure absolute URLs
 * @param string $url - The URL to check
 * @return string - The absolute URL
 */
function ensureAbsoluteUrl($url) {
    if (empty($url)) {
        return '';
    }
    
    // If it's already an absolute URL, return it
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    
    // Get the current domain
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    
    // Remove leading slashes
    $url = ltrim($url, '/');
    
    // Create the absolute URL
    return $protocol . $host . '/' . $url;
}

/**
 * Get all categories
 * @param bool $activeOnly - Whether to get only active categories
 * @return array - The categories
 */
function getCategories($activeOnly = false) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_categories";
    if ($activeOnly) {
        $sql .= " WHERE status = 1";
    }
    $sql .= " ORDER BY name ASC";
    
    $result = $conn->query($sql);
    $categories = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure image URL is absolute
            if (!empty($row['image_url'])) {
                $row['image_url'] = ensureAbsoluteUrl($row['image_url']);
            }
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Get a specific category by ID
 * @param int $id - The category ID
 * @return array|false - The category or false if not found
 */
function getCategoryById($id) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $category = $result->fetch_assoc();
        // Ensure image URL is absolute
        if (!empty($category['image_url'])) {
            $category['image_url'] = ensureAbsoluteUrl($category['image_url']);
        }
        return $category;
    }
    
    return false;
}

/**
 * Get all apps
 * @param bool $activeOnly - Whether to get only active apps
 * @return array - The apps
 */
function getApps($activeOnly = false) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_apps";
    if ($activeOnly) {
        $sql .= " WHERE status = 1";
    }
    $sql .= " ORDER BY name ASC";
    
    $result = $conn->query($sql);
    $apps = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure image URL is absolute
            if (!empty($row['image_url'])) {
                $row['image_url'] = ensureAbsoluteUrl($row['image_url']);
            }
            $apps[] = $row;
        }
    }
    
    return $apps;
}

/**
 * Get a specific app by ID
 * @param int $id - The app ID
 * @return array|false - The app or false if not found
 */
function getAppById($id) {
    global $conn;
    
    $sql = "SELECT * FROM artist_v2_apps WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $app = $result->fetch_assoc();
        // Ensure image URL is absolute
        if (!empty($app['image_url'])) {
            $app['image_url'] = ensureAbsoluteUrl($app['image_url']);
        }
        return $app;
    }
    
    return false;
}

/**
 * Get categories for a specific app
 * @param int $appId - The app ID
 * @return array - The categories
 */
function getCategoriesForApp($appId) {
    global $conn;
    
    $sql = "SELECT c.id FROM artist_v2_categories c
            JOIN artist_v2_app_categories ac ON c.id = ac.category_id
            WHERE ac.app_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $categoryIds = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categoryIds[] = $row['id'];
        }
    }
    
    return $categoryIds;
}

/**
 * Get apps for a specific category
 * @param int $categoryId - The category ID
 * @param bool $activeOnly - Whether to get only active apps
 * @return array - The apps
 */
function getAppsForCategory($categoryId, $activeOnly = false) {
    global $conn;
    
    $sql = "SELECT a.* FROM artist_v2_apps a
            JOIN artist_v2_app_categories ac ON a.id = ac.app_id
            WHERE ac.category_id = ?";
    
    if ($activeOnly) {
        $sql .= " AND a.status = 1";
    }
    
    $sql .= " ORDER BY a.name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $apps = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure image URL is absolute
            if (!empty($row['image_url'])) {
                $row['image_url'] = ensureAbsoluteUrl($row['image_url']);
            }
            $apps[] = $row;
        }
    }
    
    return $apps;
}

/**
 * Display a success message
 * @param string $message - The message to display
 */
function showSuccess($message) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

/**
 * Display an error message
 * @param string $message - The message to display
 */
function showError($message) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

/**
 * Create default image if it doesn't exist
 * @param string $path - The path to create the image
 * @param string $text - The text to display on the image
 * @return bool - Whether the image was created successfully
 */
function createDefaultImage($path, $text) {
    // Create directory if it doesn't exist
    $dir = dirname($path);
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0777, true)) {
            return false;
        }
    }
    
    // Check if GD is available
    if (!extension_loaded('gd')) {
        return false;
    }
    
    // Create the image
    $img = imagecreatetruecolor(300, 300);
    $bgColor = imagecolorallocate($img, 41, 128, 185); // Blue
    $textColor = imagecolorallocate($img, 255, 255, 255); // White
    
    // Fill the background
    imagefilledrectangle($img, 0, 0, 300, 300, $bgColor);
    
    // Add text
    $fontWidth = imagefontwidth(5);
    $textWidth = $fontWidth * strlen($text);
    $x = (300 - $textWidth) / 2;
    
    imagestring($img, 5, $x, 140, $text, $textColor);
    
    // Save the image
    $result = imagejpeg($img, $path, 90);
    imagedestroy($img);
    
    if ($result) {
        chmod($path, 0644);
    }
    
    return $result;
}

/**
 * Get a specific app by name
 * @param string $name - The app name
 * @return array|false - The app or false if not found
 */
function getAppByName($name) {
    global $conn;
    
    // First try exact match
    $sql = "SELECT * FROM artist_v2_apps WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $app = $result->fetch_assoc();
        // Ensure image URL is absolute
        if (!empty($app['image_url'])) {
            $app['image_url'] = ensureAbsoluteUrl($app['image_url']);
        }
        return $app;
    }
    
    // If no exact match, try case-insensitive match
    $sql = "SELECT * FROM artist_v2_apps WHERE LOWER(name) = LOWER(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $app = $result->fetch_assoc();
        // Ensure image URL is absolute
        if (!empty($app['image_url'])) {
            $app['image_url'] = ensureAbsoluteUrl($app['image_url']);
        }
        return $app;
    }
    
    return false;
}
?> 