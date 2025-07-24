<?php
require '../includes/db_connect.php';

$errorMsg = $successMsg = "";
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['password'];
    $token = $_POST['token'];

    // ✅ Strong Password Validation (Backend)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        $errorMsg = "❌ Password must have at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // ✅ Verify token and expiration
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // ✅ Token is valid, update password
            $stmt->bind_result($userId);
            $stmt->fetch();
            
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();

            $successMsg = "✅ Password successfully updated! <a href='login.php'>Login</a>";
        } else {
            $errorMsg = "❌ Invalid or expired reset link.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ✅ Styling */
        body { background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(240,240,240,1) 100%); }
        .container { max-width: 500px; margin: auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .password-strength { font-size: 0.9rem; margin-top: 5px; }
        footer { background: black; color: white; padding: 30px 0; text-align: center; }
    </style>
</head>
<body>

<!-- ✅ Reset Password Form -->
<div class="container mt-5">
    <h2 class="text-center fw-bold">Reset Password</h2>
    <p class="text-center text-muted">Enter a new strong password for your account.</p>

    <!-- ✅ Success Message -->
    <?php if (!empty($successMsg)) : ?>
        <div class="alert alert-success text-center"><?= $successMsg; ?></div>
    <?php endif; ?>

    <!-- ✅ Error Message -->
    <?php if (!empty($errorMsg)) : ?>
        <div class="alert alert-danger text-center"><?= $errorMsg; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold">New Password:</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter a strong password" required>
            <small id="password-strength" class="password-strength text-muted"></small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Confirm Password:</label>
            <input type="password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
            <small id="password-match" class="password-strength text-muted"></small>
        </div>

        <div class="mb-3">
            <input type="checkbox" id="show-password" class="form-check-input">
            <label for="show-password" class="form-check-label">Show Password</label>
        </div>

        <button type="submit" class="btn btn-danger w-100">🔒 Reset Password</button>
    </form>
</div>

<!-- ✅ Footer -->
<footer>© 2025 Your Website | All Rights Reserved.</footer>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ JavaScript for Password Validation -->
<script>
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
