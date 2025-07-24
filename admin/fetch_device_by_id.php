<?php
require '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $device_id = $_GET['id'];

    // ✅ Fetch Device Details
    $query = "SELECT * FROM devices WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $device = $result->fetch_assoc();
    $stmt->close();

    if ($device) {
        echo json_encode(["status" => "success", "data" => $device]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Device not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ No Device ID provided."]);
}
?>
