<?php
// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger m-4">Registration ID is required!</div>';
    exit;
}

// Get registration by ID
$registrationId = (int)$_GET['id'];
$registration = getTalentRegistrationById($registrationId);

if (!$registration) {
    echo '<div class="alert alert-danger m-4">Registration not found!</div>';
    exit;
}

// Handle status update
if (isset($_POST['update_status'])) {
    $newStatus = $_POST['status'];
    $notes = $_POST['notes'];
    
    if (updateTalentRegistrationStatus($registrationId, $newStatus, $notes)) {
        showSuccess("Registration status updated successfully!");
        // Refresh registration data
        $registration = getTalentRegistrationById($registrationId);
    } else {
        showError("Error updating registration status: " . $conn->error);
    }
}

// Handle delete action
if (isset($_POST['delete_registration'])) {
    if (deleteTalentRegistration($registrationId)) {
        showSuccess("Registration deleted successfully!");
        echo '<script>setTimeout(function() { window.location.href = "?page=registrations"; }, 2000);</script>';
        exit;
    } else {
        showError("Error deleting registration: " . $conn->error);
    }
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registration Details</h1>
        <div>
            <a href="?page=registrations" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Registrations
            </a>
            <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-2"></i> Delete Registration
            </button>
        </div>
    </div>
    
    <div class="row">
        <!-- Registration Details Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Registration Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td><?php echo $registration['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td><?php echo $registration['full_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td><?php echo $registration['contact_number']; ?></td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?php echo $registration['gender']; ?></td>
                            </tr>
                            <tr>
                                <th>Talent Category</th>
                                <td><?php echo $registration['talent_category']; ?></td>
                            </tr>
                            <tr>
                                <th>App Name</th>
                                <td><?php echo $registration['app_name'] ? $registration['app_name'] : '<em class="text-muted">N/A</em>'; ?></td>
                            </tr>
                            <tr>
                                <th>Submission Date</th>
                                <td><?php echo date('F d, Y h:i A', strtotime($registration['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td><?php echo date('F d, Y h:i A', strtotime($registration['updated_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($registration['status']) {
                                        case 'pending':
                                            $statusClass = 'bg-warning';
                                            break;
                                        case 'approved':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'rejected':
                                            $statusClass = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?> p-2"><?php echo ucfirst($registration['status']); ?></span>
                                </td>
                            </tr>
                            <?php if (!empty($registration['notes'])): ?>
                            <tr>
                                <th>Admin Notes</th>
                                <td><?php echo nl2br($registration['notes']); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Update Status Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" <?php echo $registration['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $registration['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $registration['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="5" class="form-control"><?php echo $registration['notes']; ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_status" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:<?php echo $registration['contact_number']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i> Send Email
                        </a>
                        <a href="tel:<?php echo $registration['contact_number']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i> Call Applicant
                        </a>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $registration['contact_number']); ?>" target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i> WhatsApp Message
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the registration for <strong><?php echo $registration['full_name']; ?></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" action="">
                    <button type="submit" name="delete_registration" class="btn btn-danger">Delete Registration</button>
                </form>
            </div>
        </div>
    </div>
</div>