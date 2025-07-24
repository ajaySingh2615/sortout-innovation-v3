<?php
require '../includes/db_connect.php';
require '../includes/config.php'; // Cloudinary config
require '../vendor/autoload.php';

use Cloudinary\Api\Upload\UploadApi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $device_id = $_POST['id'];
    $device_name = $_POST['device_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $contact_number = $_POST['contact_number'];

    // ✅ Check if new image is uploaded
    if (!empty($_FILES['image']['tmp_name'])) {
        $uploadApi = new UploadApi();
        $upload = $uploadApi->upload($_FILES['image']['tmp_name']);
        $image_url = $upload['secure_url'];

        // ✅ Update with new image
        $query = "UPDATE devices SET device_name = ?, category = ?, price = ?, description = ?, contact_number = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdsssi", $device_name, $category, $price, $description, $contact_number, $image_url, $device_id);
    } else {
        // ✅ Update without changing image
        $query = "UPDATE devices SET device_name = ?, category = ?, price = ?, description = ?, contact_number = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdssi", $device_name, $category, $price, $description, $contact_number, $device_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Failed to update device."]);
    }
    $stmt->close();
}
?>
