<?php
require '../auth/auth.php';
require '../includes/db_connect.php';
require '../includes/config.php'; // Cloudinary config
require '../vendor/autoload.php';

use Cloudinary\Api\Upload\UploadApi;

$submissionSuccess = false;

// Add this PHP array at the top of the file after requires
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
sort($categories); // Sort categories alphabetically

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $seo_title = trim($_POST['seo_title']) ?: $title;
    $content = trim($_POST['content']);
    $meta_description = trim($_POST['meta_description']);
    $focus_keyword = trim($_POST['focus_keyword']);
    $slug = trim($_POST['slug']) ?: create_slug($title);
    
    // Handle categories - changed variable name to avoid conflict
    $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    $categories_json = json_encode($selected_categories);

    if (!isset($_SESSION['user_id'])) {
        die("❌ Session Error: User ID is missing.");
    }

    // Handle image upload
    $image_url = '';
    $image_alt = trim($_POST['image_alt']);
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

    // Insert Data with SEO fields
    $stmt = $conn->prepare("INSERT INTO blogs (title, seo_title, content, meta_description, focus_keyword, slug, categories, image_url, image_alt, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $title, $seo_title, $content, $meta_description, $focus_keyword, $slug, $categories_json, $image_url, $image_alt, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $submissionSuccess = true;
        
        // Clear form data to prevent resubmission
        $_POST = array();
    } else {
        die("❌ Database Insert Error: " . $stmt->error);
    }
}

// Function to create SEO-friendly slug
function create_slug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}

// Function to count words in content
function count_words($string) {
    return str_word_count(strip_tags($string));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Blog Post | Admin Panel</title>
    <meta name="robots" content="noindex,nofollow">

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />

    <!-- Add TinyMCE for better content editing -->
    <script src="https://cdn.tiny.cloud/1/ckhdla67dgiuczihylz9vgm24qocra38y6d17t4zfaad8v8b/tinymce/5/tinymce.min.js"></script>

    <style>
        body {
    background: radial-gradient(circle, rgba(235, 235, 235, 1) 0%, rgba(220, 220, 220, 1) 100%);
    background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
    background-repeat: repeat;
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.05); /* Light shadow effect */
    z-index: -1;
}


        /* ✅ Success Modal Styling */
        .modal-content {
            border-radius: 15px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            text-align: center;
            padding: 20px;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }

        .modal-body p {
            font-size: 1.1rem;
            color: #555;
        }

        .modal-footer {
            border-top: none;
        }

        .modal-footer .btn {
            width: 48%;
            font-size: 1.1rem;
            padding: 10px;
            border-radius: 8px;
        }

        /* ✅ Submit Button Spinner */
        .spinner-border {
            display: none;
        }

        /* ✅ Image Preview */
        .image-preview {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #ddd;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        /* ✅ Cancel Button */
        .cancel-btn {
            display: none;
            margin-top: 10px;
            font-size: 1rem;
            padding: 8px 14px;
            border-radius: 8px;
            background: #dc3545;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            border: none;
            width: 100%;
        }
        .cancel-btn:hover {
            background: #b02a37;
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

        /* Categories card - removed height limit and changed overflow */
        .card-body {
            max-height: none; 
            overflow-y: visible;
        }
        
        /* Additional categories styling */
        .categories-container {
            padding: 1.5rem;
            background-color: #f9f9f9;
        }
        
        .form-check {
            margin-bottom: 8px;
        }
        
        .form-check-input:checked + .form-check-label {
            color: #0d6efd;
            font-weight: 600;
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

<!-- ✅ Modern Red & White Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" style="background: linear-gradient(135deg, #fff, #fff); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);">
    <div class="container">
        <!-- ✅ Logo -->
        <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="#">
            <img src="../public/logo.png" alt="Logo" height="40" class="me-2"> 
        </a>

        <!-- ✅ Mobile Menu Button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ✅ Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <!-- <li class="nav-item"><a class="nav-link text-black fw-semibold px-3" href="#">Home</a></li> -->
                <li class="nav-item"><a class="nav-link text-black fw-semibold px-3" href="../admin/blog_dashboard.php">Blog_dashboard</a></li>
                <!-- <li class="nav-item"><a class="nav-link text-black fw-semibold px-3" href="../blog/index.php">Blogs</a></li> -->
                <!-- <li class="nav-item">
                    <a class="btn btn-light text-danger fw-bold px-4 rounded-pill shadow-sm" href="logout.php">Logout</a>
                </li> -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-black fw-semibold px-3" href="#" role="button" data-bs-toggle="dropdown">
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



<!-- ✅ Spacing for Fixed Navbar -->
<div style="height: 80px;"></div>

<!-- ✅ Blog Form -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white shadow-lg rounded-4 p-5">
                <h2 class="text-center fw-bold mb-3">Add a New Blog Post</h2>
                <p class="text-center text-muted mb-4">Create SEO-optimized content to reach your target audience.</p>

            <form method="POST" enctype="multipart/form-data" class="row g-3" id="blogForm">
                    <!-- Display Title -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Display Title:</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> This is how your title will appear on your website.
                        </div>
                    </div>

                    <!-- SEO Title -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">SEO Title:</label>
                        <input type="text" name="seo_title" id="seoTitle" class="form-control">
                        <div class="char-counter" id="seoTitleCounter">0/60 characters</div>
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> Optimal length: 50-60 characters. Include your focus keyword near the beginning.
                        </div>
                    </div>

                    <!-- URL Slug -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">URL Slug:</label>
                        <input type="text" name="slug" id="slug" class="form-control">
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> Short, keyword-rich URL for better SEO. Will be auto-generated if left empty.
                        </div>
                    </div>

                    <!-- Meta Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Meta Description:</label>
                        <textarea name="meta_description" id="metaDescription" class="form-control" rows="3"></textarea>
                        <div class="char-counter" id="metaDescCounter">0/160 characters</div>
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> Optimal length: 150-160 characters. Include your focus keyword and a clear call-to-action.
                        </div>
                    </div>

                    <!-- Focus Keyword -->
                <div class="col-12">
                        <label class="form-label fw-semibold">Focus Keyword:</label>
                        <input type="text" name="focus_keyword" id="focusKeyword" class="form-control">
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> The main keyword you want this post to rank for.
                        </div>
                </div>

                    <!-- Content -->
                <div class="col-12">
    <label class="form-label fw-semibold">Content:</label>
                        <textarea name="content" id="content" class="form-control" rows="10" required></textarea>
                        <div id="contentAnalysis" class="mt-3"></div>
                    </div>

                    <!-- Categories -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Categories:</label>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body categories-container">
                                <div class="row g-3">
                                    <?php foreach ($categories as $index => $category): ?>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="categories[]" 
                                                       value="<?= htmlspecialchars($category) ?>" 
                                                       id="cat<?= $index ?>">
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
                        <label class="form-label fw-semibold">Featured Image:</label>
                        <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                        <input type="text" name="image_alt" id="imageAlt" class="form-control mt-2" placeholder="Image Alt Text">
                        <div class="seo-tip">
                            <i class="fas fa-info-circle"></i> Add descriptive alt text for better SEO and accessibility.
                        </div>
                        <img id="imagePreview" class="img-fluid mt-3" style="display:none;">
                        <button type="button" class="btn btn-danger mt-2" id="removeImage" style="display:none;">Remove Image</button>
                </div>

                <div class="col-12">
                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-3">
                            <span class="spinner-border spinner-border-sm me-2" id="submitSpinner" role="status" aria-hidden="true"></span>
                            🚀 Publish Blog Post
                    </button>
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
        </div>
    </div>

    <!-- ✅ Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">✅ Blog Published Successfully!</h5>
                </div>
                <div class="modal-body">
                    <p>Your blog has been successfully added.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="submitAnother">➕ Submit Another Blog</button>
                    <a href="main_dashboard.php" class="btn btn-success">🏠 Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modern Black Footer -->
<footer class="text-white text-center py-5" style="background: #111; box-shadow: 0px -4px 10px rgba(0, 0, 0, 0.2);">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-md-4">
                <h5 class="fw-bold text-uppercase">About Us</h5>
                <p class="small">We share the latest insights in technology, marketing, and innovation.</p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4">
                <h5 class="fw-bold text-uppercase">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">🏠 Home</a></li>
                    <li><a href="blogs.php" class="text-white text-decoration-none">📝 Blogs</a></li>
                    <li><a href="contact.php" class="text-white text-decoration-none">📞 Contact</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-4">
                <h5 class="fw-bold text-uppercase">Follow Us</h5>
                <div class="d-flex justify-content-center">
                    <a href="#" class="me-3 text-white fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-3 text-white fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3 text-white fs-4"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-4 small">
            © 2025 YourBrand. All Rights Reserved.
        </div>
    </div>
</footer>




    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ JavaScript for Image Preview, Spinner & Modal -->
    <script>
        // Hide spinner by default
        document.getElementById('submitSpinner').style.display = 'none';
        
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
        document.getElementById('blogForm').addEventListener('submit', function(e) {
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
            $('#slug').val(slug);
            
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

        // Image handling
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                    $('#removeImage').show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imageUpload").change(function() {
            readURL(this);
        });

        $("#removeImage").click(function() {
            $('#imageUpload').val('');
            $('#imagePreview').hide();
            $(this).hide();
            $('#imageAlt').val('');
        });

        // Initial SEO analysis
        analyzeSEO();

        // For debugging - show submission status in console
        console.log("Submission success status:", <?= $submissionSuccess ? 'true' : 'false' ?>);
        
        // Show success modal if submission was successful
            <?php if ($submissionSuccess): ?>
        // Use plain JavaScript to show the modal when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
        <?php endif; ?>

        document.getElementById('submitAnother').addEventListener('click', function() {
            document.getElementById('blogForm').reset();
            
            // Reset TinyMCE
            if(tinymce.get('content')) {
                tinymce.get('content').setContent('');
            }
            
            // Reset image
            $('#imagePreview').hide();
            $('#removeImage').hide();
            
            // Reset SEO analysis
            analyzeSEO();
            
            // Hide the modal
            var successModalInstance = bootstrap.Modal.getInstance(document.getElementById('successModal'));
            if (successModalInstance) {
                successModalInstance.hide();
            }
            
            // Redirect to same page to clear POST data
            window.location.href = window.location.pathname;
        });
    </script>

</body>
</html>
