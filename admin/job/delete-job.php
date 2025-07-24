<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    $response = [
        'success' => false,
        'message' => 'Access Denied! Only super admins can delete jobs.'
    ];
    echo json_encode($response);
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if job ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $response = [
        'success' => false,
        'message' => 'Job ID is required.'
    ];
    echo json_encode($response);
    exit();
}

$jobId = intval($_POST['id']);

// Delete the job
$stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
$stmt->bind_param("i", $jobId);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'message' => 'Job deleted successfully.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Error deleting job: ' . $stmt->error
    ];
}

$stmt->close();
echo json_encode($response);
?> 