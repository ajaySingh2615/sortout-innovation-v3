<?php
require '../auth/auth.php'; // Ensure only admin access
require '../includes/db_connect.php';
require '../includes/config.php'; // Cloudinary config
require '../vendor/autoload.php';

use Cloudinary\Api\Upload\UploadApi;

// Define categories array
$categories = [
    'Digital Marketing',
    'Live Streaming Service',
    'Find Talent',
    'Information Technology',
    'Chartered Accountant',
    'Human Resources',
    'Courier',
    'Shipping and Fulfillment',
    'Stationery',
    'Real Estate and Property',
    'Event Management',
    'Design and Creative',
    'Corporate Insurance',
    'Business Strategy',
    'Innovation',
    'Industry News',
    'Marketing and Sales',
    'Finance and Investment',
    'Legal Services',
    'Healthcare Services'
];
sort($categories);

$updateSuccess = false;
$blog = null;

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

    if ($blog) {
        $blog['categories_array'] = json_decode($blog['categories'] ?? '[]', true);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $seo_title = trim($_POST['seo_title']) ?: $title;
    $content = trim($_POST['content']);
    $meta_description = trim($_POST['meta_description']);
    $focus_keyword = trim($_POST['focus_keyword']);
    $slug = trim($_POST['slug']) ?: create_slug($title);
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    $categories_json = json_encode($categories);
    $image_alt = trim($_POST['image_alt']);

    // Handle image upload if new image is selected
    $image_url = $_POST['current_image'];
    if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        try {
            $uploadApi = new UploadApi();
            $upload = $uploadApi->upload($_FILES['image']['tmp_name'], [
                'folder' => 'blog_images',
                'alt' => $image_alt
            ]);
            $image_url = $upload['secure_url'];
        } catch (Exception $e) {
            die("❌ Cloudinary Upload Error: " . $e->getMessage());
        }
    }

    // Update blog post with all SEO fields
    $stmt = $conn->prepare("UPDATE blogs SET 
        title = ?, 
        seo_title = ?, 
        content = ?, 
        meta_description = ?, 
        focus_keyword = ?, 
        slug = ?, 
        categories = ?, 
        image_url = ?, 
        image_alt = ? 
        WHERE id = ?");
    $stmt->bind_param("sssssssssi", 
        $title, 
        $seo_title, 
        $content, 
        $meta_description, 
        $focus_keyword, 
        $slug, 
        $categories_json, 
        $image_url, 
        $image_alt, 
        $id
    );

    if ($stmt->execute()) {
        $updateSuccess = true;
    } else {
        die("❌ Database Update Error: " . $stmt->error);
    }
}

// Function to create SEO-friendly slug
function create_slug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}

if (!$blog) {
    header("Location: main_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    
    <!-- Add TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/ckhdla67dgiuczihylz9vgm24qocra38y6d17t4zfaad8v8b/tinymce/5/tinymce.min.js"></script>

    <style>
        body {
            background: radial-gradient(circle, rgba(235,235,235,1) 0%, rgba(220,220,220,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            cursor: pointer;
            transition: color 0.2s;
        }

        .form-check-input:checked + .form-check-label {
            color: #0d6efd;
            font-weight: 500;
        }

        .card {
            border-radius: 15px;
        }

        .card-body {
            max-height: none;
            overflow-y: visible;
        }

        /* SEO Score Indicator */
        .seo-score {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .seo-score-good {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .seo-score-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }
        .seo-score-poor {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        /* SEO Tips */
        .seo-tip {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* Character Counter */
        .char-counter {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }
        .char-counter.warning {
            color: #856404;
        }
        .char-counter.danger {
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow">
    <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../public/logo.png" alt="Logo" height="40">
        </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/blog_dashboard.php">Blog_dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../blog/index.php">View Blog</a>
                    </li>
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        👤 <?= $_SESSION['username']; ?> (<?= ucfirst($_SESSION['role']); ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Spacing for Fixed Navbar -->
<div style="height: 80px;"></div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="bg-white shadow-lg rounded-4 p-5">
                    <h2 class="text-center mb-4">Edit Blog Post</h2>

                    <?php if ($updateSuccess): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ✅ Blog post updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="id" value="<?= $blog['id']; ?>">
                        <input type="hidden" name="current_image" value="<?= $blog['image_url']; ?>">

                        <!-- Display Title -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Display Title:</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($blog['title']); ?>" required>
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> This is how your title will appear on your website.
                            </div>
                        </div>

                        <!-- SEO Title -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">SEO Title:</label>
                            <input type="text" name="seo_title" id="seoTitle" class="form-control" value="<?= htmlspecialchars($blog['seo_title']); ?>">
                            <div class="char-counter" id="seoTitleCounter">0/60 characters</div>
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> Optimal length: 50-60 characters. Include your focus keyword near the beginning.
                            </div>
                        </div>

                        <!-- URL Slug -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">URL Slug:</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="<?= htmlspecialchars($blog['slug']); ?>">
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> Short, keyword-rich URL for better SEO.
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Meta Description:</label>
                            <textarea name="meta_description" id="metaDescription" class="form-control" rows="3"><?= htmlspecialchars($blog['meta_description']); ?></textarea>
                            <div class="char-counter" id="metaDescCounter">0/160 characters</div>
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> Optimal length: 150-160 characters. Include your focus keyword and a clear call-to-action.
                            </div>
                        </div>

                        <!-- Focus Keyword -->
            <div class="col-12">
                            <label class="form-label fw-semibold">Focus Keyword:</label>
                            <input type="text" name="focus_keyword" id="focusKeyword" class="form-control" value="<?= htmlspecialchars($blog['focus_keyword']); ?>">
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> The main keyword you want this post to rank for.
                            </div>
            </div>

                        <!-- Content -->
            <div class="col-12">
                <label class="form-label fw-semibold">Content:</label>
                            <textarea name="content" id="content" class="form-control" rows="10" required><?= htmlspecialchars($blog['content']); ?></textarea>
                            <div id="contentAnalysis" class="mt-3"></div>
            </div>

                        <!-- Categories -->
            <div class="col-12">
                            <label class="form-label fw-semibold">Categories:</label>
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <?php foreach ($categories as $index => $category): ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="categories[]" 
                                                           value="<?= htmlspecialchars($category) ?>" 
                                                           id="cat<?= $index ?>"
                                                           <?= in_array($category, $blog['categories_array']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="cat<?= $index ?>">
                                                        <?= htmlspecialchars($category) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
            </div>

                        <!-- Featured Image -->
            <div class="col-12">
                            <label class="form-label fw-semibold">Current Image:</label>
                            <?php if ($blog['image_url']): ?>
                                <img src="<?= $blog['image_url']; ?>" alt="Current Blog Image" class="img-fluid rounded mb-3" style="max-height: 200px;">
                            <?php endif; ?>
                            
                <label class="form-label fw-semibold">Upload New Image (optional):</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <input type="text" name="image_alt" id="imageAlt" class="form-control mt-2" placeholder="Image Alt Text" value="<?= htmlspecialchars($blog['image_alt']); ?>">
                            <div class="seo-tip">
                                <i class="fas fa-info-circle"></i> Add descriptive alt text for better SEO and accessibility.
                            </div>
            </div>

                        <div class="col-12 d-flex gap-2 mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-primary flex-grow-1 py-2">
                                <span class="spinner-border spinner-border-sm me-2" id="submitSpinner" role="status" aria-hidden="true" style="display: none;"></span>
                                💾 Save Changes
                            </button>
                            <a href="blog_dashboard.php" class="btn btn-secondary py-2">
                                ← Back to Blog_dashboard
                            </a>
            </div>
        </form>
    </div>
</div>

            <!-- SEO Sidebar -->
            <!-- <div class="col-lg-4">
                <div class="bg-white shadow-lg rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">SEO Analysis</h4>
                    <div id="seoScore" class="seo-score"></div>
                    <div id="seoChecklist"></div>
                </div>
            </div> -->
        <!-- </div>
    </div> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            plugins: 'link image lists table code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
            height: 400,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // This syncs TinyMCE content to the textarea
                    analyzeSEO();
                });
            }
        });
        
        // Form submission handler
        document.querySelector('form').addEventListener('submit', function(e) {
            // Show spinner when form is submitted
            document.getElementById('submitSpinner').style.display = 'inline-block';
            document.getElementById('submitBtn').disabled = true;
            
            // Make sure TinyMCE content is saved to the textarea
            if(tinymce.get('content')) {
                tinymce.get('content').save();
            }
        });

        // Auto-generate slug from title
        $('#title').on('keyup', function() {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9-]+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            if ($('#slug').val() === '') {
                $('#slug').val(slug);
            }
            
            // If SEO title is empty, copy from main title
            if ($('#seoTitle').val() === '') {
                $('#seoTitle').val($(this).val());
                updateCharCounter('#seoTitle', '#seoTitleCounter', 60);
            }
            
            analyzeSEO();
        });

        // Character counters
        function updateCharCounter(inputId, counterId, optimal) {
            let length = $(inputId).val().length;
            $(counterId).text(length + '/' + optimal + ' characters');
            
            if (length > optimal) {
                $(counterId).addClass('danger').removeClass('warning');
            } else if (length > optimal * 0.8) {
                $(counterId).addClass('warning').removeClass('danger');
            } else {
                $(counterId).removeClass('warning danger');
            }
        }

        // Monitor input fields
        $('#seoTitle').on('keyup', function() {
            updateCharCounter('#seoTitle', '#seoTitleCounter', 60);
            analyzeSEO();
        });

        $('#metaDescription').on('keyup', function() {
            updateCharCounter('#metaDescription', '#metaDescCounter', 160);
            analyzeSEO();
        });

        // SEO Analysis
        function analyzeSEO() {
            let score = 100;
            let checks = [];
            
            const title = $('#title').val();
            const seoTitle = $('#seoTitle').val();
            const metaDesc = $('#metaDescription').val();
            const content = tinymce.get('content').getContent();
            const focusKeyword = $('#focusKeyword').val();
            
            // Title checks
            if (title.length < 20 || title.length > 70) {
                score -= 10;
                checks.push({
                    status: 'warning',
                    message: 'Title length should be between 20-70 characters'
                });
            }
            
            // Focus keyword checks
            if (focusKeyword) {
                if (!title.toLowerCase().includes(focusKeyword.toLowerCase())) {
                    score -= 10;
                    checks.push({
                        status: 'warning',
                        message: 'Focus keyword missing from title'
                    });
                }
                
                if (!metaDesc.toLowerCase().includes(focusKeyword.toLowerCase())) {
                    score -= 10;
                    checks.push({
                        status: 'warning',
                        message: 'Focus keyword missing from meta description'
                    });
                }
                
                const keywordDensity = (content.toLowerCase().match(new RegExp(focusKeyword.toLowerCase(), 'g')) || []).length;
                if (keywordDensity < 2) {
                    score -= 10;
                    checks.push({
                        status: 'warning',
                        message: 'Focus keyword density is too low'
                    });
                }
            } else {
                score -= 20;
                checks.push({
                    status: 'error',
                    message: 'No focus keyword set'
                });
            }
            
            // Content length check
            const wordCount = content.split(/\s+/).length;
            if (wordCount < 300) {
                score -= 20;
                checks.push({
                    status: 'error',
                    message: 'Content is too short (minimum 300 words recommended)'
                });
            }
            
            // Meta description check
            if (!metaDesc) {
                score -= 15;
                checks.push({
                    status: 'error',
                    message: 'Meta description is missing'
                });
            } else if (metaDesc.length < 120 || metaDesc.length > 160) {
                score -= 10;
                checks.push({
                    status: 'warning',
                    message: 'Meta description length should be between 120-160 characters'
                });
            }
            
            // Update SEO score display
            let scoreClass = score >= 80 ? 'good' : (score >= 50 ? 'warning' : 'poor');
            $('#seoScore').attr('class', 'seo-score seo-score-' + scoreClass)
                .html(`<h5 class="mb-0">SEO Score: ${score}%</h5>`);
            
            // Update checklist
            let checklistHtml = '<ul class="list-unstyled mt-3">';
            checks.forEach(check => {
                let icon = check.status === 'error' ? '❌' : '⚠️';
                checklistHtml += `<li class="mb-2">${icon} ${check.message}</li>`;
            });
            checklistHtml += '</ul>';
            $('#seoChecklist').html(checklistHtml);
        }

        // Initialize character counters and SEO analysis
        $(document).ready(function() {
            updateCharCounter('#seoTitle', '#seoTitleCounter', 60);
            updateCharCounter('#metaDescription', '#metaDescCounter', 160);
            analyzeSEO();
        });
</script>
</body>
</html>
