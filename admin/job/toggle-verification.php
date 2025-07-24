<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    $response = [
        'success' => false,
        'message' => 'Access Denied! Only super admins can verify jobs.'
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

// First, get the current verification status
$checkStmt = $conn->prepare("SELECT is_verified FROM jobs WHERE id = ?");
$checkStmt->bind_param("i", $jobId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    $response = [
        'success' => false,
        'message' => 'Job not found.'
    ];
    echo json_encode($response);
    $checkStmt->close();
    exit();
}

$job = $result->fetch_assoc();
$currentStatus = $job['is_verified'];
$newStatus = $currentStatus ? 0 : 1;
$checkStmt->close();

// Update the verification status
$updateStmt = $conn->prepare("UPDATE jobs SET is_verified = ? WHERE id = ?");
$updateStmt->bind_param("ii", $newStatus, $jobId);

if ($updateStmt->execute()) {
    $statusText = $newStatus ? 'verified' : 'unverified';
    $response = [
        'success' => true,
        'message' => "Job marked as $statusText successfully.",
        'newStatus' => $newStatus
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Error updating job status: ' . $updateStmt->error
    ];
}

$updateStmt->close();
echo json_encode($response);
?> 