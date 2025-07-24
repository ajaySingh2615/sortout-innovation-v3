<?php
// Test script to check visibility functionality
require_once __DIR__ . '/../includes/db_connect.php';

// Check if is_visible column exists
$query = "DESCRIBE clients";
$result = mysqli_query($conn, $query);

echo "<h3>Database Structure Check:</h3>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

$has_is_visible = false;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
    
    if ($row['Field'] === 'is_visible') {
        $has_is_visible = true;
    }
}
echo "</table>";

if (!$has_is_visible) {
    echo "<p style='color: red;'><strong>ERROR: is_visible column does not exist!</strong></p>";
    echo "<p>Run this SQL to add it:</p>";
    echo "<code>ALTER TABLE clients ADD COLUMN is_visible TINYINT(1) DEFAULT 1;</code>";
} else {
    echo "<p style='color: green;'><strong>SUCCESS: is_visible column exists!</strong></p>";
}

// Check some sample data
echo "<h3>Sample Data Check:</h3>";
$sample_query = "SELECT id, name, approval_status, is_visible FROM clients LIMIT 5";
$sample_result = mysqli_query($conn, $sample_query);

if ($sample_result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Approval Status</th><th>Is Visible</th></tr>";
    
    while ($row = mysqli_fetch_assoc($sample_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['approval_status'] . "</td>";
        echo "<td>" . ($row['is_visible'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Error fetching sample data: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?> 