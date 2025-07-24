<?php
require 'includes/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Device not found.</p>";
    exit;
}

$device_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM devices WHERE id = ?");
$stmt->bind_param("i", $device_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Device not found.</p>";
    exit;
}

$device = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $device['device_name']; ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo $device['device_name']; ?></h1>
        <img src="<?php echo $device['image_url']; ?>" class="img-fluid" alt="<?php echo $device['device_name']; ?>">
        
        <p class="mt-3"><strong>Price:</strong> ₹<?php echo $device['price']; ?></p>
        <p><strong>Description:</strong> <?php echo $device['description']; ?></p>
        <p><strong>Stock:</strong> <?php echo ($device['in_stock'] ? "<span class='text-success'>In Stock</span>" : "<span class='text-danger'>Out of Stock</span>"); ?></p>
        
        <a href="index.php" class="btn btn-primary mt-3">Go Back</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
