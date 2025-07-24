<?php
// Process form submissions
$message = '';
$error = '';

// Check if the init database button was clicked
if (isset($_POST['init_database'])) {
    global $conn;
    
    // Get the SQL file content
    $sqlFile = file_get_contents(__DIR__ . '/../includes/artist_v2_tables.sql');
    
    // Split SQL file into statements
    $statements = explode(';', $sqlFile);
    
    // Track execution
    $executed = 0;
    $failed = 0;
    
    // Execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if ($conn->query($statement)) {
                $executed++;
            } else {
                $failed++;
                $error .= "Error executing SQL: " . $conn->error . "<br>";
            }
        }
    }
    
    if ($failed === 0) {
        $message = "Database initialized successfully! Executed $executed statements.";
    } else {
        $error = "Database initialization completed with errors. Executed: $executed, Failed: $failed<br>" . $error;
    }
}

// Check if the create default images button was clicked
if (isset($_POST['create_images'])) {
    // Define paths for default images
    $defaultPaths = [
        'admin' => [
            'app' => __DIR__ . '/../../assets/img/default-app.jpg',
            'category' => __DIR__ . '/../../assets/img/default-category.jpg',
            'apps_dir' => __DIR__ . '/../../assets/img/apps/',
            'categories_dir' => __DIR__ . '/../../assets/img/categories/'
        ],
        'root' => [
            'app' => __DIR__ . '/../../../assets/img/default-app.jpg',
            'category' => __DIR__ . '/../../../assets/img/default-category.jpg',
            'apps_dir' => __DIR__ . '/../../../assets/img/apps/',
            'categories_dir' => __DIR__ . '/../../../assets/img/categories/'
        ]
    ];
    
    // Create directories
    $dirSuccess = true;
    foreach ($defaultPaths as $location => $paths) {
        foreach ($paths as $key => $path) {
            if (strpos($key, 'dir') !== false) {
                if (!file_exists($path)) {
                    if (!mkdir($path, 0777, true)) {
                        $dirSuccess = false;
                        $error .= "Failed to create directory: $path<br>";
                    }
                }
            }
        }
    }
    
    // Create default images
    $imgSuccess = true;
    foreach ($defaultPaths as $location => $paths) {
        if (!createDefaultImage($paths['app'], 'Default App')) {
            $imgSuccess = false;
            $error .= "Failed to create default app image in $location<br>";
        }
        
        if (!createDefaultImage($paths['category'], 'Default Category')) {
            $imgSuccess = false;
            $error .= "Failed to create default category image in $location<br>";
        }
    }
    
    if ($dirSuccess && $imgSuccess) {
        $message = "Default image directories and images created successfully!";
    } else {
        $error = "There were issues creating directories or images: <br>" . $error;
    }
}

// Check if the sample images button was clicked
if (isset($_POST['sample_images'])) {
    // Sample images
    $sampleImages = [
        'categories' => [
            'social-media.jpg' => 'Social Media',
            'entertainment.jpg' => 'Entertainment',
            'productivity.jpg' => 'Productivity',
            'education.jpg' => 'Education',
            'lifestyle.jpg' => 'Lifestyle'
        ],
        'apps' => [
            'instagram.jpg' => 'Instagram',
            'tiktok.jpg' => 'TikTok',
            'youtube.jpg' => 'YouTube',
            'netflix.jpg' => 'Netflix',
            'spotify.jpg' => 'Spotify',
            'trello.jpg' => 'Trello',
            'duolingo.jpg' => 'Duolingo',
            'myfitnesspal.jpg' => 'MyFitnessPal'
        ]
    ];
    
    // Create sample images
    $imgSuccess = true;
    
    // Create category sample images
    foreach ($sampleImages['categories'] as $filename => $label) {
        $adminPath = __DIR__ . '/../../assets/img/categories/' . $filename;
        $rootPath = __DIR__ . '/../../../assets/img/categories/' . $filename;
        
        if (!createDefaultImage($adminPath, $label)) {
            $imgSuccess = false;
            $error .= "Failed to create sample category image: $filename (admin)<br>";
        }
        
        if (!createDefaultImage($rootPath, $label)) {
            $imgSuccess = false;
            $error .= "Failed to create sample category image: $filename (root)<br>";
        }
    }
    
    // Create app sample images
    foreach ($sampleImages['apps'] as $filename => $label) {
        $adminPath = __DIR__ . '/../../assets/img/apps/' . $filename;
        $rootPath = __DIR__ . '/../../../assets/img/apps/' . $filename;
        
        if (!createDefaultImage($adminPath, $label)) {
            $imgSuccess = false;
            $error .= "Failed to create sample app image: $filename (admin)<br>";
        }
        
        if (!createDefaultImage($rootPath, $label)) {
            $imgSuccess = false;
            $error .= "Failed to create sample app image: $filename (root)<br>";
        }
    }
    
    if ($imgSuccess) {
        $message = "Sample images created successfully!";
    } else {
        $error = "There were issues creating sample images: <br>" . $error;
    }
}
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Setup</h1>
            <p class="text-muted">Initialize the database and create necessary assets</p>
        </div>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Initialize Database</h6>
                </div>
                <div class="card-body">
                    <p>Create database tables and sample data for the Artist V2 system.</p>
                    <form method="post">
                        <button type="submit" name="init_database" class="btn btn-primary">
                            <i class="fas fa-database me-2"></i> Initialize Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create Default Images</h6>
                </div>
                <div class="card-body">
                    <p>Create directories and default images for apps and categories.</p>
                    <form method="post">
                        <button type="submit" name="create_images" class="btn btn-success">
                            <i class="fas fa-images me-2"></i> Create Default Images
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create Sample Images</h6>
                </div>
                <div class="card-body">
                    <p>Create sample images for the default categories and apps.</p>
                    <form method="post">
                        <button type="submit" name="sample_images" class="btn btn-info">
                            <i class="fas fa-photo-video me-2"></i> Create Sample Images
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Directory Structure</h6>
                </div>
                <div class="card-body">
                    <p>The following directory structure is used for the Artist V2 system:</p>
                    <pre class="bg-light p-3">
/admin/artist-v2/          - Admin dashboard files
  /assets/                 - Admin assets
    /img/                  - Admin images
      /apps/               - App images (admin copy)
      /categories/         - Category images (admin copy)
  /includes/               - Admin includes
  /pages/                  - Admin pages

/artist-v2/               - Frontend files
  /assets/                - Frontend assets
    /css/                 - Frontend CSS
    /js/                  - Frontend JavaScript
    /img/                 - Frontend images
  /includes/              - Frontend includes
  /components/            - Frontend components
  
/assets/                  - Root assets
  /img/                   - Root images
    /apps/                - App images (main copy)
    /categories/          - Category images (main copy)
                    </pre>
                    
                    <p>Important: Images are stored in both the admin and root directories to ensure proper display.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Create Image Directories and Default Images</h5>
        </div>
        <div class="card-body">
            <p>This tool will create the necessary directories and default images for both admin and frontend.</p>
            <form method="post" action="../create_default_images.php">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-images me-2"></i> Create Image Directories & Default Images
                </button>
            </form>
            <div class="mt-2">
                <p>If the above button doesn't work, try this direct link:</p>
                <a href="create_images.php" class="btn btn-outline-primary">
                    <i class="fas fa-images me-2"></i> Alternative Image Creation Tool
                </a>
            </div>
        </div>
    </div>
</div> 