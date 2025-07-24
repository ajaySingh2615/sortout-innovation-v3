<?php
require_once '../../auth/auth.php';
require_once '../../includes/db_connect.php';

// Ensure only super admins can access
if ($_SESSION['role'] !== 'super_admin') {
    echo "Access Denied! Only super admins can manage jobs.";
    exit();
}

$errorMsg = "";
$successMsg = "";
$debugInfo = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save raw POST data for debugging
    $debugInfo['raw_post_data'] = $_POST;
    
    // Get the category value directly from POST
    $category = isset($_POST['category']) ? $_POST['category'] : 'NOT SET';
    $debugInfo['category_value'] = $category;
    $debugInfo['category_type'] = gettype($category);
    $debugInfo['category_empty'] = empty($category) ? 'TRUE' : 'FALSE';
    $debugInfo['category_equals_zero'] = ($category === '0') ? 'TRUE' : 'FALSE';
    
    // Try to insert directly into database with a test job
    try {
        // Create a test job entry
        $stmt = $conn->prepare("INSERT INTO jobs (title, company_name, location, min_salary, max_salary, job_type, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $title = "TEST JOB " . date('Y-m-d H:i:s');
        $companyName = "Test Company";
        $location = "Test Location";
        $minSalary = 10000;
        $maxSalary = 20000;
        $jobType = "Full Time";
        
        // Bind parameters
        $stmt->bind_param("sssiiss", 
            $title, $companyName, $location, $minSalary, $maxSalary, $jobType, $category
        );
        
        // Execute and check result
        if ($stmt->execute()) {
            $jobId = $conn->insert_id;
            $debugInfo['insert_success'] = true;
            $debugInfo['new_job_id'] = $jobId;
            
            // Verify what was actually stored
            $verifyStmt = $conn->prepare("SELECT category FROM jobs WHERE id = ?");
            $verifyStmt->bind_param("i", $jobId);
            $verifyStmt->execute();
            $result = $verifyStmt->get_result();
            $storedJob = $result->fetch_assoc();
            $debugInfo['stored_category'] = $storedJob['category'];
            $debugInfo['stored_category_type'] = gettype($storedJob['category']);
            
            $successMsg = "Test job inserted successfully!";
        } else {
            $errorMsg = "Error adding test job: " . $stmt->error;
            $debugInfo['insert_error'] = $stmt->error;
        }
    } catch (Exception $e) {
        $errorMsg = "Exception: " . $e->getMessage();
        $debugInfo['exception'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Debug Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
        .debug-data {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">Category Debug Test</h1>
        
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?= $errorMsg ?></div>
        <?php endif; ?>
        
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?= $successMsg ?></div>
        <?php endif; ?>
        
        <?php if (!empty($debugInfo)): ?>
            <div class="debug-data">
                <h3>Debug Information</h3>
                <pre><?php print_r($debugInfo); ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <h3>Test Form</h3>
                <p>Select a category and submit to see exactly what's happening</p>
                
                <form method="POST" action="" id="debugForm">
                    <div class="mb-3">
                        <label for="category" class="form-label">Job Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Select a category</option>
                            <option value="IT & Software Development">IT & Software Development</option>
                            <option value="Marketing & Advertising">Marketing & Advertising</option>
                            <option value="Sales & Business Development">Sales & Business Development</option>
                            <option value="Human Resources & Recruitment">Human Resources & Recruitment</option>
                            <option value="Finance & Accounting">Finance & Accounting</option>
                            <option value="Operations & Supply Chain Management">Operations & Supply Chain Management</option>
                            <option value="Education & Training">Education & Training</option>
                            <option value="Healthcare & Medical">Healthcare & Medical</option>
                            <option value="Banking & Insurance">Banking & Insurance</option>
                            <option value="Media, Arts & Entertainment">Media, Arts & Entertainment</option>
                            <option value="Customer Service & Support">Customer Service & Support</option>
                            <option value="Manufacturing & Engineering">Manufacturing & Engineering</option>
                            <option value="Hospitality & Travel">Hospitality & Travel</option>
                            <option value="Legal & Compliance">Legal & Compliance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Test Submission</button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="job-dashboard.php" class="btn btn-secondary">Back to Job Dashboard</a>
        </div>
    </div>
    
    <script>
    document.getElementById('debugForm').addEventListener('submit', function(e) {
        // Show the selected value before submitting
        const categoryValue = document.getElementById('category').value;
        console.log('Category value before submission:', categoryValue);
    });
    </script>
</body>
</html> 