<?php
// Include database connection and functions
require_once '../../../includes/db_connect.php';
include_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if category_id is provided
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Category ID is required'
    ]);
    exit;
}

$categoryId = (int)$_GET['category_id'];

// Get apps for this category
$apps = getAppsForCategory($categoryId, true);

// Return response
echo json_encode([
    'success' => true,
    'apps' => $apps
]);
?>