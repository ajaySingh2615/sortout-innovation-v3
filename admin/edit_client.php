<?php
require_once '../includes/db_connect.php';
require_once '../includes/config.php'; // Include Cloudinary configuration
use Cloudinary\Api\Upload\UploadApi;

// Ensure headers are set before any output
header('Content-Type: application/json');

// Enable error reporting for debugging but don't display errors in production
error_reporting(E_ALL);
ini_set('display_errors', 0); // Changed to 0 to prevent PHP errors in JSON output

// Function to sanitize input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

// Wrap everything in a try-catch block
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // First, get the current client data to check their professional type
        $checkQuery = "SELECT * FROM clients WHERE id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to execute query");
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $clientData = mysqli_fetch_assoc($result);
        
        if (!$clientData) {
            throw new Exception("Client not found");
        }

        // Common fields for both Artist and Employee
        $name = sanitize($conn, $_POST['name']);
        $age = intval($_POST['age']);
        $gender = sanitize($conn, $_POST['gender']);
        $city = sanitize($conn, $_POST['city']);
        $language = isset($_POST['language']) ? $_POST['language'] : '';
        $phone = isset($_POST['phone']) ? sanitize($conn, $_POST['phone']) : $clientData['phone'];
        $professional = isset($_POST['professional']) ? $_POST['professional'] : '';

        // Get professional-specific fields
        $followers = isset($_POST['followers']) ? $_POST['followers'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        $experience = isset($_POST['experience']) ? $_POST['experience'] : '';
        $current_salary = isset($_POST['current_salary']) ? $_POST['current_salary'] : '';

        // Handle image upload if a new image is provided
        $image_url = $clientData['image_url']; // Keep existing image URL by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadApi = new UploadApi();
                $result = $uploadApi->upload($_FILES['image']['tmp_name'], [
                    'folder' => 'client_images',
                    'resource_type' => 'image'
                ]);
                $image_url = $result['secure_url'];
            } catch (Exception $e) {
                // Log error but continue with existing image
                error_log("Image upload error: " . $e->getMessage());
            }
        }

        // Initialize update arrays
        $updateFields = [];
        $params = [];
        $types = "";

        // Add common fields for both types
        $updateFields = [
            "name = ?",
            "age = ?",
            "gender = ?",
            "city = ?",
            "language = ?",
            "phone = ?",
            "image_url = ?",
            "current_salary = ?"
        ];
        
        $params = [$name, $age, $gender, $city, $language, $phone, $image_url, $current_salary];
        $types = "sissssss";

        if ($clientData['professional'] === 'Artist') {
            // Artist specific fields
            $updateFields[] = "category = ?";
            $updateFields[] = "followers = ?";
            $updateFields[] = "email = ?";
            $updateFields[] = "influencer_category = ?";
            $updateFields[] = "influencer_type = ?";
            $updateFields[] = "instagram_profile = ?";
            $updateFields[] = "expected_payment = ?";
            $updateFields[] = "work_type_preference = ?";
            
            $params[] = $category;
            $params[] = $followers;
            $params[] = isset($_POST['email']) ? sanitize($conn, $_POST['email']) : '';
            $params[] = isset($_POST['influencer_category']) ? sanitize($conn, $_POST['influencer_category']) : '';
            $params[] = isset($_POST['influencer_type']) ? sanitize($conn, $_POST['influencer_type']) : '';
            $params[] = isset($_POST['instagram_profile']) ? sanitize($conn, $_POST['instagram_profile']) : '';
            $params[] = isset($_POST['expected_payment']) ? sanitize($conn, $_POST['expected_payment']) : '';
            $params[] = isset($_POST['work_type_preference']) ? sanitize($conn, $_POST['work_type_preference']) : '';
            $types .= "ssssssss";
        } else {
            // Employee specific fields
            // Handle resume upload if provided
            $resume_url = $clientData['resume_url']; // Keep existing resume URL by default
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/resumes/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $upload_path)) {
                    $resume_url = 'uploads/resumes/' . $new_filename;
                }
            }
            
            $updateFields[] = "role = ?";
            $updateFields[] = "experience = ?";
            $updateFields[] = "resume_url = ?";
            
            $params[] = $role;
            $params[] = $experience; 
            $params[] = $resume_url;
            $types .= "sss";
        }

        // Add ID for WHERE clause
        $params[] = $id;
        $types .= "i";

        // Construct and execute the update query
        $updateQuery = "UPDATE clients SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        
        // Debug information
        error_log("Types string: " . $types);
        error_log("Number of params: " . count($params));
        error_log("Update query: " . $updateQuery);
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Client updated successfully'
            ]);
        } else {
            throw new Exception("Error updating client: " . mysqli_error($conn));
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Handle GET request - this is for fetching client data
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$id) {
            throw new Exception("Invalid client ID");
        }
        
    $query = "SELECT * FROM clients WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to execute query");
        }
        
    $result = mysqli_stmt_get_result($stmt);
    $client = mysqli_fetch_assoc($result);

    if (!$client) {
            throw new Exception("Client not found");
        }
        
        // Professional-specific data
        if ($client['professional'] === 'Artist') {
            // No need to reassign these values as they are already in $client array
            // Just ensure they are included in the response
            $client = array_merge($client, [
                'category' => $client['category'] ?? '',
                'followers' => $client['followers'] ?? '',
                'email' => $client['email'] ?? '',
                'influencer_category' => $client['influencer_category'] ?? '',
                'influencer_type' => $client['influencer_type'] ?? '',
                'instagram_profile' => $client['instagram_profile'] ?? '',
                'expected_payment' => $client['expected_payment'] ?? '',
                'work_type_preference' => $client['work_type_preference'] ?? ''
            ]);
            
            // Debug log
            error_log("Artist data being returned: " . json_encode([
                'influencer_category' => $client['influencer_category'],
                'influencer_type' => $client['influencer_type']
            ]));
        } else {
            $client['role'] = $client['role'] ?? '';
            $client['experience'] = $client['experience'] ?? '';
            $client['current_salary'] = $client['current_salary'] ?? '';
            $client['resume_url'] = $client['resume_url'] ?? '';
        }
        
        echo json_encode([
            'status' => 'success',
            'client' => $client
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method'
        ]);
    }
} catch (Exception $e) {
    // Log the error to a file instead of displaying it
    error_log("Error in edit_client.php: " . $e->getMessage());
    
    // Return a clean JSON error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Error loading client data. Please try again.'
    ]);
} finally {
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>
