<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // Ensure JSON response

// ✅ Fix file paths
require '../includes/db_connect.php';
require '../includes/config.php'; // Cloudinary config
require '../vendor/autoload.php';

use Cloudinary\Api\Upload\UploadApi;

$response = ["status" => "error", "message" => "Unknown error"];

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method");
    }

    // ✅ Get form data safely
    $name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $age = isset($_POST['age']) ? intval($_POST['age']) : 0;
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : "";
    $followers = isset($_POST['followers']) ? intval($_POST['followers']) : 0;
    $category = isset($_POST['category']) ? trim($_POST['category']) : "";
    $languages = isset($_POST['language']) ? implode(", ", $_POST['language']) : "";
    $professional = isset($_POST['professional']) ? trim($_POST['professional']) : "";

    if (empty($name) || empty($category) || empty($professional)) {
        throw new Exception("Missing required fields.");
    }

    // ✅ Handle Image Upload to Cloudinary
    $image_url = "";
    if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        try {
            $uploadApi = new UploadApi();
            $upload = $uploadApi->upload($_FILES['image']['tmp_name'], ['folder' => 'client_images']);
            $image_url = $upload['secure_url'];
        } catch (Exception $e) {
            throw new Exception("Cloudinary Upload Error: " . $e->getMessage());
        }
    }

    // ✅ Insert Data into Database
    $stmt = $conn->prepare("INSERT INTO clients (name, age, gender, followers, category, language, professional, image_url) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssss", $name, $age, $gender, $followers, $category, $languages, $professional, $image_url);

    if (!$stmt->execute()) {
        throw new Exception("Database Insert Error: " . $stmt->error);
    }

    $response = ["status" => "success", "message" => "Client added successfully"];
} catch (Exception $e) {
    $response = ["status" => "error", "message" => $e->getMessage()];
}

// ✅ Always return JSON response
echo json_encode($response);
exit;
?>
