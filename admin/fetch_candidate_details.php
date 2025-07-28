<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// ✅ Ensure Only Admins & Super Admins Can Access
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    exit('Access Denied');
}

$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($candidate_id <= 0) {
    echo '<div class="text-center text-danger">Invalid candidate ID</div>';
    exit();
}

// Fetch candidate details
$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="text-center text-danger">Candidate not found</div>';
    exit();
}

$candidate = $result->fetch_assoc();
?>

<div class="candidate-details-container">
    <!-- Personal Information Section -->
    <div class="candidate-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-user"></i>
            </div>
            <h3 class="section-title">Personal Info</h3>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Name</div>
                <div class="info-value">
                    <i class="fas fa-user-circle"></i>
                    <?= htmlspecialchars($candidate['full_name']) ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value">
                    <i class="fas fa-phone"></i>
                    <a href="tel:<?= $candidate['phone_number'] ?>">
                        <?= htmlspecialchars($candidate['phone_number']) ?>
                    </a>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Gender</div>
                <div class="info-value">
                    <i class="fas fa-venus-mars"></i>
                    <?= ucfirst($candidate['gender']) ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge-modern status-<?= $candidate['status'] ?>-modern">
                        <?= ucfirst($candidate['status']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Professional Information Section -->
    <div class="candidate-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h3 class="section-title">Professional Info</h3>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Category</div>
                <div class="info-value">
                    <span class="job-category-badge">
                        <?= htmlspecialchars($candidate['job_category']) ?>
                    </span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Role</div>
                <div class="info-value">
                    <i class="fas fa-tag"></i>
                    <?= htmlspecialchars($candidate['job_role']) ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Experience</div>
                <div class="info-value">
                    <span class="experience-badge">
                        <?= htmlspecialchars($candidate['experience_range']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registration Information Section -->
    <div class="candidate-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="section-title">Registration Info</h3>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Registered</div>
                <div class="info-value">
                    <div class="date-info">
                        <div class="date-main">
                            <i class="fas fa-calendar-check"></i>
                            <?= date('F d, Y', strtotime($candidate['created_at'])) ?>
                        </div>
                        <div class="date-secondary">
                            <?= date('h:i A', strtotime($candidate['created_at'])) ?> 
                            (<?= date('D', strtotime($candidate['created_at'])) ?>)
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($candidate['updated_at'] !== $candidate['created_at']): ?>
            <div class="info-item">
                <div class="info-label">Updated</div>
                <div class="info-value">
                    <div class="date-info">
                        <div class="date-main">
                            <i class="fas fa-edit"></i>
                            <?= date('F d, Y h:i A', strtotime($candidate['updated_at'])) ?>
                        </div>
                        <div class="date-secondary">
                            Profile was modified
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="quick-actions-section">
    <div class="quick-actions-header">
        <h4 class="quick-actions-title">Quick Actions</h4>
        <p class="quick-actions-subtitle">Update candidate status</p>
    </div>
    
    <div class="quick-actions-grid">
        <button class="action-btn action-btn-contact" onclick="updateStatus(<?= $candidate['id'] ?>, 'contacted')">
            <i class="fas fa-phone"></i>
            Contacted
        </button>
        
        <button class="action-btn action-btn-active" onclick="updateStatus(<?= $candidate['id'] ?>, 'active')">
            <i class="fas fa-user-check"></i>
            Active
        </button>
        
        <button class="action-btn action-btn-archive" onclick="updateStatus(<?= $candidate['id'] ?>, 'archived')">
            <i class="fas fa-archive"></i>
            Archive
        </button>
    </div>
</div> 