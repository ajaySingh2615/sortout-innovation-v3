<?php
require 'includes/db_connect.php';
header('Content-Type: application/json');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($searchTerm)) {
    $stmt = $conn->prepare("SELECT id, device_name FROM devices WHERE device_name LIKE ? LIMIT 5");
    $searchTerm = "%{$searchTerm}%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $devices = [];
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }

    echo json_encode($devices);
} else {
    echo json_encode([]);
}
?>
