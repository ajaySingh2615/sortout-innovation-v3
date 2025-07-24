<?php
require '../includes/db_connect.php';
require '../includes/config.php'; // Cloudinary config
require '../vendor/autoload.php';

use Cloudinary\Api\Upload\UploadApi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $device_name = $_POST['device_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $contact_number = $_POST['contact_number'];

    $uploadApi = new UploadApi();
    $upload = $uploadApi->upload($_FILES['image']['tmp_name']);
    $image_url = $upload['secure_url'];

    $query = "INSERT INTO devices (device_name, category, price, description, contact_number, image_url) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdsss", $device_name, $category, $price, $description, $contact_number, $image_url);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();
}
?>
