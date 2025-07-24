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

<div class="row">
    <!-- Left Column - Personal Info -->
    <div class="col-md-6">
        <h6 class="text-danger fw-bold mb-3">
            <i class="fas fa-user me-2"></i>Personal Information
        </h6>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Full Name:</label>
            <div class="fs-5"><?= htmlspecialchars($candidate['full_name']) ?></div>
        </div>
        
        <div class="row mb-3">
            <div class="col-6">
                <label class="fw-semibold text-muted">Age:</label>
                <div><?= $candidate['age'] ?> years</div>
            </div>
            <div class="col-6">
                <label class="fw-semibold text-muted">Gender:</label>
                <div><?= ucfirst($candidate['gender']) ?></div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Phone Number:</label>
            <div>
                <i class="fas fa-phone text-success me-2"></i>
                <a href="tel:<?= $candidate['phone_number'] ?>" class="text-decoration-none">
                    <?= htmlspecialchars($candidate['phone_number']) ?>
                </a>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">City:</label>
            <div>
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                <?= htmlspecialchars($candidate['city']) ?>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Status:</label>
            <div>
                <span class="status-badge status-<?= $candidate['status'] ?>">
                    <?= ucfirst($candidate['status']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Professional Info -->
    <div class="col-md-6">
        <h6 class="text-danger fw-bold mb-3">
            <i class="fas fa-briefcase me-2"></i>Professional Information
        </h6>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Job Category:</label>
            <div class="badge bg-primary fs-6"><?= htmlspecialchars($candidate['job_category']) ?></div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Job Role:</label>
            <div class="fs-6 fw-semibold"><?= htmlspecialchars($candidate['job_role']) ?></div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Years of Experience:</label>
            <div>
                <i class="fas fa-clock text-warning me-2"></i>
                <?= $candidate['years_experience'] ?> years
                <?php if ($candidate['years_experience'] >= 5): ?>
                    <span class="badge bg-success ms-2">Experienced</span>
                <?php elseif ($candidate['years_experience'] >= 2): ?>
                    <span class="badge bg-info ms-2">Mid-level</span>
                <?php elseif ($candidate['years_experience'] > 0): ?>
                    <span class="badge bg-warning ms-2">Junior</span>
                <?php else: ?>
                    <span class="badge bg-secondary ms-2">Fresher</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Current Salary:</label>
            <div>
                <?php if ($candidate['current_salary']): ?>
                    <i class="fas fa-rupee-sign text-success me-2"></i>
                    <span class="fw-bold fs-6">₹<?= number_format($candidate['current_salary']) ?></span>
                    <small class="text-muted">per month</small>
                <?php else: ?>
                    <span class="text-muted">Not specified</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="fw-semibold text-muted">Registration Date:</label>
            <div>
                <i class="fas fa-calendar text-info me-2"></i>
                <?= date('F d, Y', strtotime($candidate['created_at'])) ?>
                <br>
                <small class="text-muted">
                    <?= date('h:i A', strtotime($candidate['created_at'])) ?>
                    (<?= date('D', strtotime($candidate['created_at'])) ?>)
                </small>
            </div>
        </div>
        
        <?php if ($candidate['updated_at'] !== $candidate['created_at']): ?>
            <div class="mb-3">
                <label class="fw-semibold text-muted">Last Updated:</label>
                <div>
                    <i class="fas fa-edit text-secondary me-2"></i>
                    <?= date('F d, Y h:i A', strtotime($candidate['updated_at'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-success btn-sm" onclick="updateStatus(<?= $candidate['id'] ?>, 'contacted')">
                <i class="fas fa-phone me-1"></i> Mark as Contacted
            </button>
            <button class="btn btn-warning btn-sm" onclick="updateStatus(<?= $candidate['id'] ?>, 'active')">
                <i class="fas fa-user-check me-1"></i> Mark as Active
            </button>
            <button class="btn btn-secondary btn-sm" onclick="updateStatus(<?= $candidate['id'] ?>, 'archived')">
                <i class="fas fa-archive me-1"></i> Archive
            </button>
        </div>
    </div>
</div>

<!-- Additional Styling -->
<style>
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background-color: #d1f2eb;
        color: #148a5c;
    }

    .status-contacted {
        background-color: #d4f1fc;
        color: #0c7a96;
    }

    .status-archived {
        background-color: #f8d7da;
        color: #721c24;
    }
</style> 