-- ============================================
-- Candidate Registration System Database Schema
-- Created: 2024-12-19
-- Purpose: Store candidate registration data from QR code form
-- ============================================

-- Create candidates table
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `city` varchar(100) NOT NULL,
  `job_category` varchar(255) NOT NULL,
  `job_role` varchar(255) NOT NULL,
  `years_experience` decimal(3,1) NOT NULL,
  `current_salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','archived','contacted') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `idx_job_category` (`job_category`),
  KEY `idx_city` (`city`),
  KEY `idx_experience` (`years_experience`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status` (`status`),
  KEY `idx_phone` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some constraints for data integrity
ALTER TABLE `candidates` 
  ADD CONSTRAINT `chk_age` CHECK (`age` >= 16 AND `age` <= 80),
  ADD CONSTRAINT `chk_experience` CHECK (`years_experience` >= 0 AND `years_experience` <= 50),
  ADD CONSTRAINT `chk_salary` CHECK (`current_salary` >= 0);

-- Create a view for quick statistics
CREATE OR REPLACE VIEW `candidate_stats` AS
SELECT 
    COUNT(*) as total_candidates,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_registrations,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_candidates,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_candidates,
    COUNT(CASE WHEN status = 'archived' THEN 1 END) as archived_candidates,
    ROUND(AVG(years_experience), 1) as avg_experience,
    ROUND(AVG(current_salary), 2) as avg_salary
FROM candidates; 