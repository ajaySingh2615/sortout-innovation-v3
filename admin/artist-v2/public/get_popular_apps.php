<?php
// Include database connection and functions
require_once '../../../includes/db_connect.php';
include_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get all active apps (for now, we'll just return all active apps as "popular")
// In a real scenario, you might have a popularity metric to sort by
$apps = getApps(true);

// Limit to 8 apps for the popular section
$popularApps = array_slice($apps, 0, 8);

// Return response
echo json_encode([
    'success' => true,
    'apps' => $popularApps
]);
?> 