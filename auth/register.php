<?php
require '../includes/db_connect.php';

$registrationSuccess = false;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // ✅ Strong Password Validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errorMsg = "❌ Password must have at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        if (!in_array($role, ['admin', 'user'])) {
            die("❌ Invalid role selected.");
        }

        // ✅ New Admins Need Approval, Users Are Auto-Approved
        $status = ($role === 'admin') ? 'pending' : 'approved';

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $role, $status);

        if ($stmt->execute()) {
            $registrationSuccess = true;
        } else {
            $errorMsg = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
         /* ✅ Background Design */
         body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%);
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            background-repeat: repeat;
        }

        /* ✅ Centering the Form (NOW RESPONSIVE) */
        .form-container {
            max-width: 600px; /* Increased width */
            width: 90%; /* Ensures good scaling on small screens */
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
            color: black;
            animation: fadeIn 0.8s ease-in-out;
        }


        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ✅ Form Styling */
        .form-control {
            border-radius: 8px;
            padding: 10px;
        }

        /* ✅ Custom Button */
        .btn-primary {
            background: #d90429;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #ef233c;
        }

        /* ✅ Password Strength Indicator */
        .password-strength {
            font-size: 0.9rem;
            margin-top: 5px;
            font-weight: bold;
        }

        /* ✅ Navbar */
        .navbar {
            background: linear-gradient(135deg, #fff, #fff);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            font-weight: bold;
        }

        /* ✅ Footer */
        footer {
            background: black;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }

        /* ✅ Eye Icon */
        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: gray;
        }

         /* ✅ Responsive Design */
         @media (max-width: 768px) {
            .form-container {
                padding: 30px;
                width: 95%;
            }
        }
    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="#">
            <img src="../public/logo.png" alt="Logo" height="40" class="me-2">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-black fw-semibold px-3" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-black fw-semibold px-3" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ✅ Spacing for Navbar -->
<div style="height: 80px;"></div>

<!-- ✅ Signup Form -->
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="form-container">
        <h2 class="text-center fw-bold mb-3">Create an Account</h2>
        <p class="text-center text-muted mb-4">Register with your details below.</p>

        <!-- ✅ Success Alert -->
        <?php if ($registrationSuccess): ?>
            <div class="alert alert-success text-center">
                🎉 Registration Successful! 
                <?php if ($role === 'admin'): ?>
                    Your account is pending approval by Super Admin.
                <?php else: ?>
                    You can now <a href="login.php" class="fw-bold">Login</a>.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Error Alert -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger text-center"><?= $errorMsg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label fw-semibold">Username:</label>
            <input type="text" name="username" class="form-control" required>

            <label class="form-label fw-semibold">Email:</label>
            <input type="email" name="email" class="form-control" required>

            <label class="form-label fw-semibold">Password:</label>
            <div class="position-relative">
                <input type="password" name="password" id="password" class="form-control" required>
                <span class="eye-icon" onclick="togglePassword('password')">👁️</span>
            </div>
            <small id="password-strength" class="password-strength text-muted"></small>

            <label class="form-label fw-semibold">Confirm Password:</label>
            <div class="position-relative">
                <input type="password" id="confirm_password" class="form-control" required>
                <span class="eye-icon" onclick="togglePassword('confirm_password')">👁️</span>
            </div>
            <small id="password-match" class="password-strength text-muted"></small>

            <label class="form-label fw-semibold mt-3">Select Role:</label>
            <select name="role" class="form-select">
                <option value="user" selected>👤 User</option>
                <option value="admin">🔑 Admin</option>
            </select>

            <button type="submit" class="btn btn-primary w-100 mt-3">🚀 Register</button>
        </form>
    </div>
</div>
<!-- ✅ Footer -->
<footer>© 2025 Your Website | All Rights Reserved.</footer>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ JavaScript for Password Validation -->
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm_password");
    const passwordStrength = document.getElementById("password-strength");
    const passwordMatch = document.getElementById("password-match");
    const showPasswordCheckbox = document.getElementById("show-password");

    passwordInput.addEventListener("input", function() {
        const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        passwordStrength.textContent = strongPasswordRegex.test(passwordInput.value) ? "✅ Strong Password" : "❌ Weak Password";
        passwordStrength.style.color = strongPasswordRegex.test(passwordInput.value) ? "green" : "red";
    });

    confirmPasswordInput.addEventListener("input", function() {
        passwordMatch.textContent = passwordInput.value === confirmPasswordInput.value ? "✅ Passwords match" : "❌ Passwords do not match";
        passwordMatch.style.color = passwordInput.value === confirmPasswordInput.value ? "green" : "red";
    });

    showPasswordCheckbox.addEventListener("change", function() {
        passwordInput.type = confirmPasswordInput.type = this.checked ? "text" : "password";
    });
});
</script>

</body>
</html>
