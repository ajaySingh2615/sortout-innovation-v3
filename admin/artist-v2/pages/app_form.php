<?php
// Initialize variables
$app = [
    'id' => '',
    'name' => '',
    'description' => '',
    'image_url' => '',
    'form_url' => '',
    'app_link' => '',
    'tutorial_video' => '',
    'app_policy' => '',
    'status' => 1
];
$isEdit = false;
$selectedCategories = [];

// Get all categories for the form
$allCategories = getCategories();

// Check if we're editing an existing app
if (isset($_GET['id'])) {
    $appId = (int)$_GET['id'];
    $existingApp = getAppById($appId);
    
    if ($existingApp) {
        $app = $existingApp;
        $isEdit = true;
        $selectedCategories = getCategoriesForApp($appId);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $formUrl = sanitize($_POST['form_url']);
    $appLink = sanitize($_POST['app_link']);
    $tutorialVideo = sanitize($_POST['tutorial_video']);
    $status = isset($_POST['status']) ? 1 : 0;
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    
    // Keep existing policy by default
    $policyPath = $app['app_policy'];
    
    // Handle policy file upload
    if (!empty($_FILES['app_policy']['name'])) {
        // Define upload directory
        $policyUploadDir = __DIR__ . '/../../assets/policies/';
        $rootPolicyDir = __DIR__ . '/../../../assets/policies/';
        
        // Create directories if they don't exist
        if (!file_exists($policyUploadDir)) {
            if (!mkdir($policyUploadDir, 0777, true)) {
                error_log('Failed to create policy upload directory: ' . $policyUploadDir);
            }
        }
        
        if (!file_exists($rootPolicyDir)) {
            if (!mkdir($rootPolicyDir, 0777, true)) {
                error_log('Failed to create root policy directory: ' . $rootPolicyDir);
            }
        }
        
        // Generate a unique filename
        $filename = uniqid() . '_' . basename($_FILES['app_policy']['name']);
        $policyTargetPath = $policyUploadDir . $filename;
        $rootPolicyPath = $rootPolicyDir . $filename;
        
        // Check file type
        $fileType = strtolower(pathinfo($_FILES['app_policy']['name'], PATHINFO_EXTENSION));
        if ($fileType != "pdf") {
            showError("Only PDF files are allowed for app policies.");
        } else {
            // Upload the file
            $uploadSuccess = move_uploaded_file($_FILES['app_policy']['tmp_name'], $policyTargetPath);
            
            if ($uploadSuccess) {
                // Copy to root directory
                if (copy($policyTargetPath, $rootPolicyPath)) {
                    // Set permissions
                    chmod($policyTargetPath, 0644);
                    chmod($rootPolicyPath, 0644);
                    
                    // Update the policy path
                    $policyPath = 'assets/policies/' . $filename;
                } else {
                    error_log('Failed to copy policy to root directory: ' . $rootPolicyPath);
                }
            } else {
                showError("Failed to upload policy file. Please try again.");
            }
        }
    }
    
    // Handle image upload
    $imageURL = $app['image_url']; // Keep existing image by default
    
    if (!empty($_FILES['image']['name'])) {
        // Upload new image
        $uploadedImage = uploadImage($_FILES['image'], 'apps');
        
        if ($uploadedImage) {
            $imageURL = $uploadedImage;
        } else {
            showError("Failed to upload image. Please try again.");
        }
    } else if (isset($_POST['use_default']) && $_POST['use_default'] == '1') {
        // Use default image
        $imageURL = 'assets/img/default-app.jpg';
    }
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "App name is required.";
    }
    
    if (empty($description)) {
        $errors[] = "App description is required.";
    }
    
    if (empty($formUrl)) {
        $errors[] = "Form URL is required.";
    }
    
    if (empty($categories)) {
        $errors[] = "At least one category is required.";
    }
    
    // If there are no errors, proceed with database operation
    if (empty($errors)) {
        if ($isEdit) {
            // Update existing app
            $sql = "UPDATE artist_v2_apps SET 
                    name = ?,
                    description = ?,
                    image_url = ?,
                    form_url = ?,
                    app_link = ?,
                    tutorial_video = ?,
                    app_policy = ?,
                    status = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssi", $name, $description, $imageURL, $formUrl, $appLink, $tutorialVideo, $policyPath, $status, $app['id']);
            
            if ($stmt->execute()) {
                // Update app categories
                
                // First delete all existing associations
                $sql = "DELETE FROM artist_v2_app_categories WHERE app_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $app['id']);
                $stmt->execute();
                
                // Then insert new associations
                foreach ($categories as $categoryId) {
                    $sql = "INSERT INTO artist_v2_app_categories (app_id, category_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $app['id'], $categoryId);
                    $stmt->execute();
                }
                
                showSuccess("App updated successfully!");
                // Update local variable to display updated values in the form
                $app['name'] = $name;
                $app['description'] = $description;
                $app['image_url'] = $imageURL;
                $app['form_url'] = $formUrl;
                $app['app_link'] = $appLink;
                $app['tutorial_video'] = $tutorialVideo;
                $app['app_policy'] = $policyPath;
                $app['status'] = $status;
                $selectedCategories = $categories;
            } else {
                showError("Error updating app: " . $conn->error);
            }
        } else {
            // Insert new app
            $sql = "INSERT INTO artist_v2_apps (name, description, image_url, form_url, app_link, tutorial_video, app_policy, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $name, $description, $imageURL, $formUrl, $appLink, $tutorialVideo, $policyPath, $status);
            
            if ($stmt->execute()) {
                $newAppId = $conn->insert_id;
                
                // Insert app categories
                foreach ($categories as $categoryId) {
                    $sql = "INSERT INTO artist_v2_app_categories (app_id, category_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $newAppId, $categoryId);
                    $stmt->execute();
                }
                
                showSuccess("App added successfully!");
                // Redirect to apps page after adding
                echo "<script>window.location='artist_dashboard.php?page=apps';</script>";
                exit;
            } else {
                showError("Error adding app: " . $conn->error);
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
        <h1 class="h3"><?php echo $isEdit ? 'Edit App' : 'Add New App'; ?></h1>
        <a href="artist_dashboard.php?page=apps" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Apps
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">App Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $app['name']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $app['description']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="form_url" class="form-label">Form URL</label>
                            <input type="url" class="form-control" id="form_url" name="form_url" value="<?php echo $app['form_url']; ?>" required>
                            <div class="form-text">The URL where users will be redirected when selecting this app.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="app_link" class="form-label">App Link</label>
                            <input type="url" class="form-control" id="app_link" name="app_link" value="<?php echo $app['app_link']; ?>">
                            <div class="form-text">The direct download or access URL for the app. Leave empty if not applicable.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tutorial_video" class="form-label">Tutorial Video</label>
                            <input type="url" class="form-control" id="tutorial_video" name="tutorial_video" value="<?php echo $app['tutorial_video']; ?>">
                            <div class="form-text">Embed URL for a tutorial video (e.g., YouTube embed link). Leave empty if not applicable.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="app_policy" class="form-label">App Policy</label>
                            <input type="file" class="form-control" id="app_policy" name="app_policy" accept=".pdf">
                            <div class="form-text">Upload a PDF file containing the app's policy/terms document (max 5MB).</div>
                            
                            <?php if (!empty($app['app_policy'])): ?>
                                <div class="mt-2">
                                    <p class="mb-1"><strong>Current Policy:</strong></p>
                                    <a href="/<?php echo $app['app_policy']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i> View Current Policy
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Categories</label>
                            <div class="row">
                                <?php foreach ($allCategories as $category): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" 
                                                   value="<?php echo $category['id']; ?>" id="category<?php echo $category['id']; ?>"
                                                   <?php echo in_array($category['id'], $selectedCategories) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="category<?php echo $category['id']; ?>">
                                                <?php echo $category['name']; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($allCategories) === 0): ?>
                                <div class="alert alert-warning">
                                    No categories found. <a href="artist_dashboard.php?page=category-form">Add a category</a> first.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo $app['status'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">App Image</label>
                            
                            <?php if (!empty($app['image_url'])): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $app['image_url']; ?>" alt="<?php echo $app['name']; ?>" class="img-thumbnail mb-2" style="max-width: 100%; max-height: 200px;" onerror="this.src='../assets/img/default-app.jpg';">
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
                        <i class="fas fa-save me-2"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> App
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