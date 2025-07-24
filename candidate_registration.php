<?php
// Include database connection
require_once 'includes/db_connect.php';

// Initialize variables
$success_message = '';
$error_message = '';
$job_roles = [];

// Load job roles from JSON
$json_content = file_get_contents('job_roles.json');
$job_data = json_decode($json_content, true);

if ($job_data && isset($job_data['job_categories'])) {
    $job_roles = $job_data['job_categories'];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $full_name = trim($_POST['full_name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $phone_number = trim($_POST['phone_number'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $job_category = trim($_POST['job_category'] ?? '');
    $job_role = trim($_POST['job_role'] ?? '');
    $years_experience = floatval($_POST['years_experience'] ?? 0);
    $current_salary = !empty($_POST['current_salary']) ? floatval($_POST['current_salary']) : null;
    
    // Validation
    $errors = [];
    
    if (empty($full_name) || strlen($full_name) < 2) {
        $errors[] = "Full name is required (minimum 2 characters)";
    }
    
    if ($age < 16 || $age > 80) {
        $errors[] = "Age must be between 16 and 80";
    }
    
    if (empty($phone_number) || !preg_match('/^[6-9][0-9]{9}$/', $phone_number)) {
        $errors[] = "Please enter a valid 10-digit phone number";
    }
    
    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Please select a valid gender";
    }
    
    if (empty($city) || strlen($city) < 2) {
        $errors[] = "City is required";
    }
    
    if (empty($job_category)) {
        $errors[] = "Please select a job category";
    }
    
    if (empty($job_role)) {
        $errors[] = "Please select a job role";
    }
    
    if ($years_experience < 0 || $years_experience > 50) {
        $errors[] = "Years of experience must be between 0 and 50";
    }
    
    if ($current_salary !== null && $current_salary < 0) {
        $errors[] = "Current salary cannot be negative";
    }
    
    // Check if phone number already exists
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM candidates WHERE phone_number = ?");
        $check_stmt->bind_param("s", $phone_number);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "This phone number is already registered";
        }
    }
    
    // Insert data if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO candidates (full_name, age, phone_number, gender, city, job_category, job_role, years_experience, current_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssssdd", $full_name, $age, $phone_number, $gender, $city, $job_category, $job_role, $years_experience, $current_salary);
        
        if ($stmt->execute()) {
            $success_message = "Registration successful! Thank you for joining our talent network.";
            // Clear form data
            $full_name = $age = $phone_number = $gender = $city = $job_category = $job_role = $years_experience = $current_salary = '';
        } else {
            $error_message = "Registration failed. Please try again.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Registration - SortOut Innovation</title>
    <link rel="icon" type="image/png" href="images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #d90429;
            --secondary-red: #ef233c;
            --accent-red: #e63946;
            --light-gray: #f8f9fa;
            --dark-gray: #6c757d;
        }

        body {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .main-container {
            padding: 2rem 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            font-size: 1.1rem;
            margin-bottom: 0;
            opacity: 0.9;
        }

        .form-body {
            padding: 2rem;
        }

        .section-title {
            color: var(--primary-red);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-gray);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(217, 4, 41, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .required {
            color: var(--primary-red);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(217, 4, 41, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(217, 4, 41, 0.4);
            color: white;
        }

        .success-alert {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .error-alert {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .form-header {
                padding: 1.5rem;
            }
            
            .form-header h1 {
                font-size: 1.75rem;
            }
            
            .form-body {
                padding: 1.5rem;
            }
            
            .btn-submit {
                width: 100%;
                padding: 15px;
            }
        }

        /* Loading state */
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Form validation styles */
        .is-invalid {
            border-color: #dc3545;
        }

        .is-valid {
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container">
            <div class="form-card">
                <!-- Header -->
                <div class="form-header">
                    <div class="logo-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1>Join Our Talent Network</h1>
                    <p>Register now to explore exciting career opportunities</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <!-- Success/Error Messages -->
                    <?php if (!empty($success_message)): ?>
                        <div class="alert success-alert" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert error-alert" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="candidateForm" novalidate>
                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>
                                Personal Information
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">
                                        Full Name <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="full_name" 
                                           name="full_name" 
                                           required 
                                           value="<?= htmlspecialchars($full_name ?? '') ?>"
                                           placeholder="Enter your full name">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="age" class="form-label">
                                        Age <span class="required">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="age" 
                                           name="age" 
                                           required 
                                           min="16" 
                                           max="80"
                                           value="<?= htmlspecialchars($age ?? '') ?>"
                                           placeholder="Enter your age">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">
                                        Phone Number <span class="required">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           required 
                                           pattern="[6-9][0-9]{9}"
                                           value="<?= htmlspecialchars($phone_number ?? '') ?>"
                                           placeholder="10-digit mobile number">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">
                                        Gender <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= (isset($gender) && $gender === 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= (isset($gender) && $gender === 'Female') ? 'selected' : '' ?>>Female</option>
                                        <option value="Other" <?= (isset($gender) && $gender === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                
                                <div class="col-12">
                                    <label for="city" class="form-label">
                                        City <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="city" 
                                           name="city" 
                                           required 
                                           value="<?= htmlspecialchars($city ?? '') ?>"
                                           placeholder="Enter your current city">
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="mb-4">
                            <h3 class="section-title">
                                <i class="fas fa-briefcase"></i>
                                Professional Information
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="job_category" class="form-label">
                                        Job Category <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="job_category" name="job_category" required>
                                        <option value="">Select Job Category</option>
                                        <?php foreach ($job_roles as $category): ?>
                                            <option value="<?= htmlspecialchars($category['category']) ?>" 
                                                    <?= (isset($job_category) && $job_category === $category['category']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['category']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="job_role" class="form-label">
                                        Job Role <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="job_role" name="job_role" required>
                                        <option value="">First select a category</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="years_experience" class="form-label">
                                        Years of Experience <span class="required">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="years_experience" 
                                           name="years_experience" 
                                           required 
                                           min="0" 
                                           max="50" 
                                           step="0.5"
                                           value="<?= htmlspecialchars($years_experience ?? '') ?>"
                                           placeholder="e.g., 2.5">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="current_salary" class="form-label">
                                        Current Salary (per month)
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="current_salary" 
                                           name="current_salary" 
                                           min="0" 
                                           step="1000"
                                           value="<?= htmlspecialchars($current_salary ?? '') ?>"
                                           placeholder="Optional - in INR">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Register Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Job roles data
        const jobRoles = <?= json_encode($job_roles) ?>;
        
        // Dynamic job role loading
        document.getElementById('job_category').addEventListener('change', function() {
            const selectedCategory = this.value;
            const jobRoleSelect = document.getElementById('job_role');
            
            // Clear existing options
            jobRoleSelect.innerHTML = '<option value="">Select Job Role</option>';
            
            if (selectedCategory) {
                const categoryData = jobRoles.find(cat => cat.category === selectedCategory);
                if (categoryData && categoryData.roles) {
                    categoryData.roles.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role;
                        option.textContent = role;
                        jobRoleSelect.appendChild(option);
                    });
                }
            } else {
                jobRoleSelect.innerHTML = '<option value="">First select a category</option>';
            }
        });
        
        // Form validation and submission
        document.getElementById('candidateForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Add loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
            
            // Re-enable after a delay (in case of server error)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Register Now';
            }, 10000);
        });
        
        // Phone number validation
        document.getElementById('phone_number').addEventListener('input', function() {
            const phone = this.value;
            const phoneRegex = /^[6-9][0-9]{9}$/;
            
            if (phone && !phoneRegex.test(phone)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (phone) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        // Real-time validation for other fields
        const requiredFields = ['full_name', 'age', 'gender', 'city', 'job_category', 'job_role', 'years_experience'];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            }
        });
    </script>
</body>
</html> 