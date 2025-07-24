-- ============================================
-- Candidate Registration System Database Schema
-- Updated: 2024-12-19
-- Purpose: Store candidate registration data from QR code form
-- ============================================

-- Drop existing table if needed (for fresh install)
-- DROP TABLE IF EXISTS `candidates`;

-- Create candidates table with simplified structure
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `job_category` varchar(255) NOT NULL,
  `job_role` varchar(255) NOT NULL,
  `experience_range` enum('Fresher','1-2 years','2-3 years','3-4 years','4-5 years','5-7 years','7-10 years','10+ years') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','archived','contacted') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `idx_job_category` (`job_category`),
  KEY `idx_experience` (`experience_range`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status` (`status`),
  KEY `idx_phone` (`phone_number`),
  UNIQUE KEY `unique_phone` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create a view for quick statistics
CREATE OR REPLACE VIEW `candidate_stats` AS
SELECT 
    COUNT(*) as total_candidates,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_registrations,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_candidates,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_candidates,
    COUNT(CASE WHEN status = 'archived' THEN 1 END) as archived_candidates,
    (SELECT experience_range FROM candidates GROUP BY experience_range ORDER BY COUNT(*) DESC LIMIT 1) as most_common_experience
FROM candidates;

-- Migration script for existing installations
-- ALTER TABLE `candidates` 
--   DROP COLUMN `age`,
--   DROP COLUMN `city`, 
--   DROP COLUMN `years_experience`,
--   DROP COLUMN `current_salary`,
--   ADD COLUMN `experience_range` enum('Fresher','1-2 years','2-3 years','3-4 years','4-5 years','5-7 years','7-10 years','10+ years') NOT NULL DEFAULT 'Fresher',
--   ADD UNIQUE KEY `unique_phone` (`phone_number`),
--   DROP INDEX `idx_city`,
--   ADD KEY `idx_experience` (`experience_range`); 