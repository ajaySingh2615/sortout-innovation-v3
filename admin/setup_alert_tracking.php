<?php
// Setup script for admin alert tracking
require_once __DIR__ . '/../includes/db_connect.php';

// Create the admin alert tracking table
$sql = "
CREATE TABLE IF NOT EXISTS admin_alert_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id VARCHAR(100) NOT NULL,
    last_seen_timestamp DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_admin (admin_id),
    INDEX idx_admin_timestamp (admin_id, last_seen_timestamp)
);
";

if ($conn->query($sql) === TRUE) {
    echo "Table 'admin_alert_tracking' created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// Insert default entries for existing admins (optional)
$insertSql = "
INSERT IGNORE INTO admin_alert_tracking (admin_id, last_seen_timestamp)
SELECT DISTINCT username, NOW()
FROM users 
WHERE role IN ('admin', 'super_admin');
";

if ($conn->query($insertSql) === TRUE) {
    echo "Default admin tracking entries created successfully\n";
} else {
    echo "Error creating default entries: " . $conn->error . "\n";
}

$conn->close();
echo "Setup completed!\n";
?> 