<?php
// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $registrationId = (int)$_GET['id'];
    
    if (deleteTalentRegistration($registrationId)) {
        showSuccess("Registration deleted successfully!");
    } else {
        showError("Error deleting registration: " . $conn->error);
    }
}

// Handle status update action
if (isset($_GET['action']) && $_GET['action'] === 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $registrationId = (int)$_GET['id'];
    $newStatus = $_GET['status'];
    
    if (in_array($newStatus, ['pending', 'approved', 'rejected'])) {
        if (updateTalentRegistrationStatus($registrationId, $newStatus)) {
            showSuccess("Registration status updated successfully!");
        } else {
            showError("Error updating registration status: " . $conn->error);
        }
    } else {
        showError("Invalid status value!");
    }
}

// Get all talent categories for filtering
$categories = getTalentCategories(true);

// Process filter parameters
$filters = [];

// Status filter
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

// Category filter
if (isset($_GET['talent_category']) && !empty($_GET['talent_category'])) {
    $filters['talent_category'] = $_GET['talent_category'];
}

// Date range filter
if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
    $dateRange = generateDateRange($_GET['date_range']);
    if (!empty($dateRange['from'])) {
        $filters['date_from'] = $dateRange['from'];
        $filters['date_to'] = $dateRange['to'];
    }
} else {
    // Custom date range
    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
        $filters['date_from'] = $_GET['date_from'];
    }
    
    if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
        $filters['date_to'] = $_GET['date_to'];
    }
}

// Search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Pagination
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
$offset = ($page - 1) * $limit;

// Add pagination to filters
$filters['limit'] = $limit;
$filters['offset'] = $offset;

// Get total count for pagination
$totalRegistrations = countTalentRegistrations($filters);
$totalPages = ceil($totalRegistrations / $limit);

// Get registrations with filters and pagination
$registrations = getTalentRegistrations($filters);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Talent Registrations</h1>
        <a href="?page=export" class="btn btn-success">
            <i class="fas fa-file-export me-2"></i> Export Data
        </a>
    </div>
    
    <?php
    // Display bulk action messages
    if (isset($_GET['bulk_message'])) {
        $bulkSuccess = isset($_GET['bulk_success']) && $_GET['bulk_success'] == '1';
        $bulkMessage = urldecode($_GET['bulk_message']);
        
        if ($bulkSuccess) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>' . $bulkMessage . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>' . $bulkMessage . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
    }
    ?>
    
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Registrations</h6>
        </div>
        <div class="card-body">
            <form action="" method="get" class="row g-3">
                <input type="hidden" name="page" value="registrations">
                
                <!-- Status Filter -->
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo (isset($filters['status']) && $filters['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo (isset($filters['status']) && $filters['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-3">
                    <label for="talent_category" class="form-label">Talent Category</label>
                    <select name="talent_category" id="talent_category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['name']; ?>" <?php echo (isset($filters['talent_category']) && $filters['talent_category'] === $category['name']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Date From Filter -->
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo isset($filters['date_from']) ? $filters['date_from'] : ''; ?>">
                </div>
                
                <!-- Date To Filter -->
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo isset($filters['date_to']) ? $filters['date_to'] : ''; ?>">
                </div>
                
                <!-- Search Filter -->
                <div class="col-md-6">
                    <label for="search" class="form-label">Search (Name or Contact)</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="<?php echo isset($filters['search']) ? $filters['search'] : ''; ?>">
                </div>
                
                <!-- Quick Date Range Buttons -->
                <div class="col-md-6">
                    <label class="form-label">Quick Date Range</label>
                    <div class="d-flex gap-2">
                        <a href="?page=registrations&date_range=today<?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?>" class="btn btn-outline-primary">Today</a>
                        <a href="?page=registrations&date_range=week<?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?>" class="btn btn-outline-primary">This Week</a>
                        <a href="?page=registrations&date_range=month<?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?>" class="btn btn-outline-primary">This Month</a>
                        <a href="?page=registrations&date_range=year<?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?>" class="btn btn-outline-primary">This Year</a>
                        <a href="?page=registrations" class="btn btn-outline-danger">Clear Filters</a>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Summary -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> Showing <?php echo count($registrations); ?> of <?php echo $totalRegistrations; ?> registrations
        <?php
        $filterSummary = [];
        if (isset($filters['status'])) {
            $filterSummary[] = "Status: " . ucfirst($filters['status']);
        }
        if (isset($filters['talent_category'])) {
            $filterSummary[] = "Category: " . $filters['talent_category'];
        }
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $filterSummary[] = "Date Range: " . $filters['date_from'] . " to " . $filters['date_to'];
        }
        
        if (!empty($filterSummary)) {
            echo " (Filtered by: " . implode(", ", $filterSummary) . ")";
        }
        ?>
    </div>
    
    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Registrations</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Bulk Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                    <li><a class="dropdown-item bulk-action" data-action="approve" href="#">Approve Selected</a></li>
                    <li><a class="dropdown-item bulk-action" data-action="reject" href="#">Reject Selected</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item bulk-action" data-action="delete" href="#">Delete Selected</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <?php if (count($registrations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="registrationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Talent Category</th>
                                <th>App</th>
                                <th>App ID</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input registration-checkbox" type="checkbox" value="<?php echo $reg['id']; ?>">
                                        </div>
                                    </td>
                                    <td><?php echo $reg['id']; ?></td>
                                    <td><?php echo $reg['full_name']; ?></td>
                                    <td><?php echo $reg['contact_number']; ?></td>
                                    <td><?php echo $reg['talent_category']; ?></td>
                                    <td><?php echo $reg['app_name'] ? $reg['app_name'] : '<em class="text-muted">N/A</em>'; ?></td>
                                    <td><?php echo $reg['app_id'] ? $reg['app_id'] : '<em class="text-muted">N/A</em>'; ?></td>
                                    <td>
                                        <?php
                                        // Status badge
                                        $statusClass = '';
                                        switch ($reg['status']) {
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
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($reg['status']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($reg['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?page=registration-detail&id=<?php echo $reg['id']; ?>" class="btn btn-sm btn-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="?page=registrations&action=status&id=<?php echo $reg['id']; ?>&status=pending">Mark as Pending</a></li>
                                                <li><a class="dropdown-item" href="?page=registrations&action=status&id=<?php echo $reg['id']; ?>&status=approved">Mark as Approved</a></li>
                                                <li><a class="dropdown-item" href="?page=registrations&action=status&id=<?php echo $reg['id']; ?>&status=rejected">Mark as Rejected</a></li>
                                            </ul>
                                            
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $reg['id']; ?>, '<?php echo addslashes($reg['full_name']); ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span>Show</span>
                        <select id="paginationLimit" class="form-select form-select-sm d-inline-block mx-2" style="width: auto;">
                            <option value="10" <?php echo $limit === 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $limit === 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit === 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $limit === 100 ? 'selected' : ''; ?>>100</option>
                        </select>
                        <span>entries</span>
                    </div>
                    
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=registrations&p=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?><?php echo isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : ''; ?><?php echo isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . $filters['search'] : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Determine pagination range
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            // Always show first page
                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=registrations&p=1&limit=' . $limit . (isset($filters['status']) ? '&status=' . $filters['status'] : '') . (isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : '') . (isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : '') . (isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : '') . (isset($filters['search']) ? '&search=' . $filters['search'] : '') . '">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                            }
                            
                            // Page links
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                echo '<li class="page-item ' . ($i === $page ? 'active' : '') . '"><a class="page-link" href="?page=registrations&p=' . $i . '&limit=' . $limit . (isset($filters['status']) ? '&status=' . $filters['status'] : '') . (isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : '') . (isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : '') . (isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : '') . (isset($filters['search']) ? '&search=' . $filters['search'] : '') . '">' . $i . '</a></li>';
                            }
                            
                            // Always show last page
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=registrations&p=' . $totalPages . '&limit=' . $limit . (isset($filters['status']) ? '&status=' . $filters['status'] : '') . (isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : '') . (isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : '') . (isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : '') . (isset($filters['search']) ? '&search=' . $filters['search'] : '') . '">' . $totalPages . '</a></li>';
                            }
                            ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=registrations&p=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?><?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?><?php echo isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : ''; ?><?php echo isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : ''; ?><?php echo isset($filters['search']) ? '&search=' . $filters['search'] : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No registrations found matching the selected filters.
                </div>
            <?php endif; ?>
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
                Are you sure you want to delete the registration for <span id="registrationName" class="fw-bold"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="post" action="bulk_action.php" style="display: none;">
    <input type="hidden" name="action" id="bulkActionType">
    <input type="hidden" name="ids" id="bulkActionIds">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.registration-checkbox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
        
        // Pagination limit change
        document.getElementById('paginationLimit').addEventListener('change', function() {
            let limit = this.value;
            window.location.href = "?page=registrations&p=1&limit=" + limit + 
                "<?php echo isset($filters['status']) ? '&status=' . $filters['status'] : ''; ?>" + 
                "<?php echo isset($filters['talent_category']) ? '&talent_category=' . $filters['talent_category'] : ''; ?>" + 
                "<?php echo isset($filters['date_from']) ? '&date_from=' . $filters['date_from'] : ''; ?>" + 
                "<?php echo isset($filters['date_to']) ? '&date_to=' . $filters['date_to'] : ''; ?>" + 
                "<?php echo isset($filters['search']) ? '&search=' . $filters['search'] : ''; ?>";
        });
        
        // Bulk actions
        var bulkActions = document.querySelectorAll('.bulk-action');
        for (var i = 0; i < bulkActions.length; i++) {
            bulkActions[i].addEventListener('click', function(e) {
                e.preventDefault();
                
                var action = this.getAttribute('data-action');
                var selectedIds = [];
                
                // Collect all checked checkboxes
                var checkedBoxes = document.querySelectorAll('.registration-checkbox:checked');
                for (var j = 0; j < checkedBoxes.length; j++) {
                    selectedIds.push(checkedBoxes[j].value);
                }
                
                if (selectedIds.length === 0) {
                    alert("Please select at least one registration.");
                    return;
                }
                
                if (confirm(`Are you sure you want to ${action} the selected registrations?`)) {
                    // Set form values and submit
                    document.getElementById('bulkActionType').value = action;
                    document.getElementById('bulkActionIds').value = selectedIds.join(',');
                    document.getElementById('bulkActionForm').submit();
                }
            });
        }
    });
    
    // Function to show delete confirmation modal
    function confirmDelete(id, name) {
        document.getElementById('registrationName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = `?page=registrations&action=delete&id=${id}`;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script> 