<?php
// Get all categories
$categories = getCategories();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $categoryId = (int)$_GET['id'];
    
    // Check if the category has any apps associated with it
    $appsInCategory = getAppsForCategory($categoryId);
    
    if (count($appsInCategory) > 0) {
        showError("Cannot delete category because it has " . count($appsInCategory) . " apps associated with it. Remove the apps from this category first.");
    } else {
        // Delete the category
        $sql = "DELETE FROM artist_v2_categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        
        if ($stmt->execute()) {
            // Also try to delete the image file
            $category = getCategoryById($categoryId);
            if ($category && !empty($category['image_url'])) {
                $imagePath = parse_url($category['image_url'], PHP_URL_PATH);
                $localPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                if (file_exists($localPath)) {
                    unlink($localPath);
                }
            }
            
            showSuccess("Category deleted successfully!");
            // Refresh the categories list
            $categories = getCategories();
        } else {
            showError("Error deleting category: " . $conn->error);
        }
    }
}

// Handle status toggle action
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $categoryId = (int)$_GET['id'];
    $category = getCategoryById($categoryId);
    
    if ($category) {
        $newStatus = $category['status'] == 1 ? 0 : 1;
        
        $sql = "UPDATE artist_v2_categories SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newStatus, $categoryId);
        
        if ($stmt->execute()) {
            showSuccess("Category status updated successfully!");
            // Refresh the categories list
            $categories = getCategories();
        } else {
            showError("Error updating category status: " . $conn->error);
        }
    } else {
        showError("Category not found!");
    }
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Categories</h1>
        <a href="artist_dashboard.php?page=category-form" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Category
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Categories</h6>
        </div>
        <div class="card-body">
            <?php if (count($categories) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="categoriesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td>
                                        <img src="<?php echo !empty($category['image_url']) ? $category['image_url'] : '../assets/img/default-category.jpg'; ?>" 
                                             alt="<?php echo $category['name']; ?>" 
                                             class="img-thumbnail" 
                                             width="50"
                                             onerror="this.onerror=null; this.src='../assets/img/default-category.jpg';">
                                    </td>
                                    <td><?php echo $category['name']; ?></td>
                                    <td>
                                        <?php
                                        // Show a trimmed description
                                        if (strlen($category['description']) > 100) {
                                            echo substr($category['description'], 0, 100) . '...';
                                        } else {
                                            echo $category['description'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($category['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="artist_dashboard.php?page=category-form&id=<?php echo $category['id']; ?>" 
                                           class="btn btn-sm btn-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="artist_dashboard.php?page=categories&action=toggle&id=<?php echo $category['id']; ?>" 
                                           class="btn btn-sm <?php echo $category['status'] == 1 ? 'btn-warning' : 'btn-success'; ?>" 
                                           data-bs-toggle="tooltip" 
                                           title="<?php echo $category['status'] == 1 ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas <?php echo $category['status'] == 1 ? 'fa-times' : 'fa-check'; ?>"></i>
                                        </a>
                                        
                                        <a href="javascript:void(0);" 
                                           onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo $category['name']; ?>')" 
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
                    No categories found. <a href="artist_dashboard.php?page=category-form">Add a category</a> to get started.
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
                Are you sure you want to delete the category <span id="categoryName" class="fw-bold"></span>?
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
        document.getElementById('categoryName').textContent = name;
        document.getElementById('confirmDeleteBtn').href = `artist_dashboard.php?page=categories&action=delete&id=${id}`;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Initialize DataTable
    $(document).ready(function() {
        $('#categoriesTable').DataTable();
    });
</script> 