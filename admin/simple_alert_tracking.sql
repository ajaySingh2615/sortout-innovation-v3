-- Simple Admin Alert Tracking (Currently Working Approach)
-- This links clients and admin tracking through timestamp comparison

CREATE TABLE `admin_alert_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` varchar(100) NOT NULL,
  `last_seen_timestamp` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin` (`admin_id`),
  KEY `idx_admin_timestamp` (`admin_id`,`last_seen_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert initial tracking records for existing admins
INSERT IGNORE INTO `admin_alert_tracking` (`admin_id`, `last_seen_timestamp`)
SELECT DISTINCT `username`, NOW()
FROM `users` 
WHERE `role` IN ('admin', 'super_admin');

-- How the linking works (this is the query logic, not a table):
-- 
-- To find NEW registrations for a specific admin:
-- SELECT c.* FROM clients c, admin_alert_tracking a 
-- WHERE a.admin_id = 'current_admin_username' 
-- AND c.created_at > a.last_seen_timestamp
-- ORDER BY c.created_at DESC;
--
-- To find ALL registrations since admin's last visit:
-- SELECT c.* FROM clients c
-- LEFT JOIN admin_alert_tracking a ON a.admin_id = 'current_admin_username'
-- WHERE c.created_at > COALESCE(a.last_seen_timestamp, '1970-01-01')
-- ORDER BY c.created_at DESC; 