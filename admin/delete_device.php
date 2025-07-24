<?php
require '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $device_id = $_POST['id'];

    // ✅ Delete from Database
    $query = "DELETE FROM devices WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $device_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Failed to delete device."]);
    }
    $stmt->close();
}
?>
