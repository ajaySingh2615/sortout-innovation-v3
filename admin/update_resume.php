<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    // Include database connection
    require_once '../includes/db_connect.php';

    // Check if all required data is present
    if (!isset($_POST['client_id']) || !isset($_FILES['resume'])) {
        throw new Exception('Missing required data');
    }

    $client_id = (int)$_POST['client_id'];
    $resume = $_FILES['resume'];

    // Validate file type
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $file_type = $resume['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only PDF and Word documents are allowed.');
    }

    // Validate file size (5MB max)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($resume['size'] > $max_size) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/resumes';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($resume['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('resume_') . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . '/' . $unique_filename;

    // Move uploaded file
    if (!move_uploaded_file($resume['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file');
    }

    // Get the old resume URL to delete later
    $query = "SELECT resume_url FROM clients WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_resume = $result->fetch_assoc();

    // Update database with new resume URL
    $resume_url = 'uploads/resumes/' . $unique_filename;
    $query = "UPDATE clients SET resume_url = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $resume_url, $client_id);
    
    if (!$stmt->execute()) {
        // If database update fails, delete the uploaded file
        unlink($upload_path);
        throw new Exception('Failed to update database');
    }

    // Delete old resume file if it exists
    if ($old_resume && $old_resume['resume_url']) {
        $old_file = '../' . $old_resume['resume_url'];
        if (file_exists($old_file)) {
            unlink($old_file);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Resume updated successfully',
        'resume_url' => $resume_url
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 