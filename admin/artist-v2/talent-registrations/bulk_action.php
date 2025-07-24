<?php
// Include necessary files
require_once '../../../auth/auth.php';
require_once '../../../includes/db_connect.php';
include_once 'includes/functions.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage talent registrations.";
    exit();
}

// Initialize response
$success = false;
$message = 'Invalid request';

// Check if action and IDs are provided
if (isset($_POST['action']) && isset($_POST['ids'])) {
    $action = $_POST['action'];
    $ids = explode(',', $_POST['ids']);
    
    // Make sure IDs is not empty
    if (empty($ids)) {
        $message = 'No registrations selected';
    } else {
        $successCount = 0;
        $failCount = 0;
        
        // Process based on action
        switch ($action) {
            case 'approve':
            case 'reject':
                // Set status to approve or reject
                $status = ($action === 'approve') ? 'approved' : 'rejected';
                
                // Process each ID
                foreach ($ids as $id) {
                    if (updateTalentRegistrationStatus((int)$id, $status)) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
                
                $actionText = ($action === 'approve') ? 'approved' : 'rejected';
                $success = true;
                $message = "$successCount registrations $actionText successfully" . ($failCount > 0 ? ", $failCount failed" : "");
                break;
                
            case 'delete':
                // Delete each ID
                foreach ($ids as $id) {
                    if (deleteTalentRegistration((int)$id)) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
                
                $success = true;
                $message = "$successCount registrations deleted successfully" . ($failCount > 0 ? ", $failCount failed" : "");
                break;
                
            default:
                $message = 'Invalid action';
                break;
        }
    }
}

// Redirect back to registrations page with message
header("Location: dashboard.php?page=registrations&bulk_success=" . ($success ? "1" : "0") . "&bulk_message=" . urlencode($message));
exit;
?>