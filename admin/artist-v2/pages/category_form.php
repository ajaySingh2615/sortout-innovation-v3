<?php
// Initialize variables
$category = [
    'id' => '',
    'name' => '',
    'description' => '',
    'image_url' => '',
    'status' => 1
];
$isEdit = false;

// Check if we're editing an existing category
if (isset($_GET['id'])) {
    $categoryId = (int)$_GET['id'];
    $existingCategory = getCategoryById($categoryId);
    
    if ($existingCategory) {
        $category = $existingCategory;
        $isEdit = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;
    
    // Handle image upload
    $imageURL = $category['image_url']; // Keep existing image by default
    
    if (!empty($_FILES['image']['name'])) {
        // Upload new image
        $uploadedImage = uploadImage($_FILES['image'], 'categories');
        
        if ($uploadedImage) {
            $imageURL = $uploadedImage;
        } else {
            showError("Failed to upload image. Please try again.");
        }
    } else if (isset($_POST['use_default']) && $_POST['use_default'] == '1') {
        // Use default image
        $imageURL = 'assets/img/default-category.jpg';
    }
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Category name is required.";
    }
    
    if (empty($description)) {
        $errors[] = "Category description is required.";
    }
    
    // If there are no errors, proceed with database operation
    if (empty($errors)) {
        if ($isEdit) {
            // Update existing category
            $sql = "UPDATE artist_v2_categories SET 
                    name = ?,
                    description = ?,
                    image_url = ?,
                    status = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $name, $description, $imageURL, $status, $category['id']);
            
            if ($stmt->execute()) {
                showSuccess("Category updated successfully!");
                // Update local variable to display updated values in the form
                $category['name'] = $name;
                $category['description'] = $description;
                $category['image_url'] = $imageURL;
                $category['status'] = $status;
            } else {
                showError("Error updating category: " . $conn->error);
            }
        } else {
            // Insert new category
            $sql = "INSERT INTO artist_v2_categories (name, description, image_url, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $description, $imageURL, $status);
            
            if ($stmt->execute()) {
                showSuccess("Category added successfully!");
                // Redirect to categories page after adding
                echo "<script>window.location='artist_dashboard.php?page=categories';</script>";
                exit;
            } else {
                showError("Error adding category: " . $conn->error);
            }
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            showError($error);
        }
    }
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $isEdit ? 'Edit Category' : 'Add New Category'; ?></h1>
        <a href="artist_dashboard.php?page=categories" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Categories
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $category['name']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $category['description']; ?></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo $category['status'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Category Image</label>
                            
                            <?php if (!empty($category['image_url'])): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $category['image_url']; ?>" alt="<?php echo $category['name']; ?>" class="img-thumbnail mb-2" style="max-width: 100%; max-height: 200px;" onerror="this.src='../assets/img/default-category.jpg';">
                                    <div class="text-muted small">Current Image</div>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload a new image (optional). Recommended size: 512x512px.</div>
                            
                            <div class="mt-2 form-check">
                                <input type="checkbox" class="form-check-input" id="use_default" name="use_default" value="1">
                                <label class="form-check-label" for="use_default">Use default image</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Disable image upload when default image is selected
    document.getElementById('use_default').addEventListener('change', function() {
        document.getElementById('image').disabled = this.checked;
    });
</script> 