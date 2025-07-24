<?php
// Get all apps
$apps = getApps();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $appId = (int)$_GET['id'];
    
    // Delete the app
    $sql = "DELETE FROM artist_v2_apps WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appId);
    
    if ($stmt->execute()) {
        // The app-category relations will be deleted automatically due to foreign key constraints
        
        // Also try to delete the image file
        $app = getAppById($appId);
        if ($app && !empty($app['image_url'])) {
            $imagePath = parse_url($app['image_url'], PHP_URL_PATH);
            $localPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }
        
        showSuccess("App deleted successfully!");
        // Refresh the apps list
        $apps = getApps();
    } else {
        showError("Error deleting app: " . $conn->error);
    }
}

// Handle status toggle action
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $appId = (int)$_GET['id'];
    $app = getAppById($appId);
    
    if ($app) {
        $newStatus = $app['status'] == 1 ? 0 : 1;
        
        $sql = "UPDATE artist_v2_apps SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newStatus, $appId);
        
        if ($stmt->execute()) {
            showSuccess("App status updated successfully!");
            // Refresh the apps list
            $apps = getApps();
        } else {
            showError("Error updating app status: " . $conn->error);
        }
    } else {
        showError("App not found!");
    }
}

// Get all categories for filtering
$categories = getCategories();
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Apps</h1>
        <a href="artist_dashboard.php?page=app-form" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New App
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Apps</h6>
        </div>
        <div class="card-body">
            <?php if (count($apps) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="appsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Form URL</th>
                                <th>Status</th>
                                <th>Categories</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($apps as $app): ?>
                                <tr>
                                    <td><?php echo $app['id']; ?></td>
                                    <td>
                                        <img src="<?php echo !empty($app['image_url']) ? $app['image_url'] : '../assets/img/default-app.jpg'; ?>" 
                                             alt="<?php echo $app['name']; ?>" 
                                             class="img-thumbnail" 
                                             width="50"
                                             onerror="this.onerror=null; this.src='../assets/img/default-app.jpg';">
                                    </td>
                                    <td><?php echo $app['name']; ?></td>
                                    <td>
                                        <?php
                                        // Show a trimmed description
                                        if (strlen($app['description']) > 100) {
                                            echo substr($app['description'], 0, 100) . '...';
                                        } else {
                                            echo $app['description'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $app['form_url']; ?>" target="_blank" class="text-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> Form
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($app['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Get categories for this app
                                        $appCategories = getCategoriesForApp($app['id']);
                                        
                                        // Display category badges
                                        if (!empty($appCategories)) {
                                            foreach ($appCategories as $catId) {
                                                foreach ($categories as $category) {
                                                    if ($category['id'] == $catId) {
                                                        echo '<span class="badge bg-info me-1">' . $category['name'] . '</span>';
                                                    }
                                                }
                                            }
                                        } else {
                                            echo '<span class="text-muted">No categories</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="artist_dashboard.php?page=app-form&id=<?php echo $app['id']; ?>" 
                                           class="btn btn-sm btn-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="artist_dashboard.php?page=apps&action=toggle&id=<?php echo $app['id']; ?>" 
                                           class="btn btn-sm <?php echo $app['status'] == 1 ? 'btn-warning' : 'btn-success'; ?>" 
                                           data-bs-toggle="tooltip" 
                                           title="<?php echo $app['status'] == 1 ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas <?php echo $app['status'] == 1 ? 'fa-times' : 'fa-check'; ?>"></i>
                                        </a>
                                        
                                        <a href="javascript:void(0);" 
                                           onclick="confirmDelete(<?php echo $app['id']; ?>, '<?php echo $app['name']; ?>')" 
                                           class="btn btn-sm btn-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No apps found. <a href="artist_dashboard.php?page=app-form">Add an app</a> to get started.
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
                Are you sure you want to delete the app <span id="appName" class="fw-bold"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to show delete confirmation modal
    function confirmDelete(id, name) {
        document.getElementById('appName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = `artist_dashboard.php?page=apps&action=delete&id=${id}`;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Initialize DataTable
    $(document).ready(function() {
        $('#appsTable').DataTable();
    });
</script> 