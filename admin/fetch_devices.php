<?php
require '../includes/db_connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$start = ($page - 1) * $limit;

// ✅ Fetch Devices with Search & Category Filters
$query = "SELECT * FROM devices WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND device_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= "ii";

// ✅ Execute Query
$stmt = $conn->prepare($query);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// ✅ Generate Device List HTML
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td><img src="' . htmlspecialchars($row['image_url']) . '" width="80" height="80" class="rounded"></td>';
        echo '<td>' . htmlspecialchars($row['device_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['category']) . '</td>';
        echo '<td>₹' . number_format($row['price'], 2) . '</td>';
        echo '<td><a href="https://wa.me/' . htmlspecialchars($row['contact_number']) . '" target="_blank" class="btn btn-success btn-sm">📞 Contact</a></td>';
        echo '<td>';
        echo '<button class="btn btn-warning btn-sm edit-device me-2 mb-2" data-id="' . $row['id'] . '">✏️ Edit</button>';
        echo '<button class="btn btn-danger btn-sm delete-device me-2" data-id="' . $row['id'] . '">🗑️ Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="6" class="text-center">No devices found</td></tr>';
}

$stmt->close();
$conn->close();
?>
