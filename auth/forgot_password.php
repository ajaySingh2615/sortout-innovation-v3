<?php
require '../includes/db_connect.php';
require '../vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$successMsg = $errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Store token in DB with expiry time (15 mins)
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // ✅ Setup Email with PHPMailer
        $resetLink = "http://localhost/Frontent/auth/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'ajaysingh261526@gmail.com'; // 🔹 Your Gmail
            $mail->Password = 'leor xcca jxir kmzw'; // 🔹 Your App Password (not Gmail password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('your_email@gmail.com', 'Your Website Name');
            $mail->addAddress($email);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "Click the following link to reset your password: \n\n" . $resetLink;

            $mail->send();
            $successMsg = "✅ Reset link sent! Check your email.";
        } catch (Exception $e) {
            $errorMsg = "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $errorMsg = "❌ No account found with this email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="form-container">
        <h2 class="text-center fw-bold mb-3">Forgot Password?</h2>
        <p class="text-center text-muted mb-4">Enter your email, and we'll send you a reset link.</p>

        <?php if ($successMsg): ?>
            <div class="alert alert-success text-center"><?= $successMsg; ?></div>
        <?php elseif ($errorMsg): ?>
            <div class="alert alert-danger text-center"><?= $errorMsg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email:</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">📩 Send Reset Link</button>
        </form>
    </div>
</div>

</body>
</html>
