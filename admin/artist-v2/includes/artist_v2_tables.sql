-- Artist V2 Database Tables

-- Create categories table
CREATE TABLE IF NOT EXISTS `artist_v2_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create apps table
CREATE TABLE IF NOT EXISTS `artist_v2_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `form_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create app_categories junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS `artist_v2_app_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `artist_v2_app_categories_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `artist_v2_apps` (`id`) ON DELETE CASCADE,
  CONSTRAINT `artist_v2_app_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `artist_v2_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO `artist_v2_categories` (`name`, `description`, `image_url`, `status`) VALUES
('Social Media', 'Applications for social networking and content sharing', 'assets/img/categories/social-media.jpg', 1),
('Entertainment', 'Applications for entertainment and media consumption', 'assets/img/categories/entertainment.jpg', 1),
('Productivity', 'Applications for work and productivity enhancement', 'assets/img/categories/productivity.jpg', 1),
('Education', 'Applications for learning and educational purposes', 'assets/img/categories/education.jpg', 1),
('Lifestyle', 'Applications for lifestyle, health, and personal management', 'assets/img/categories/lifestyle.jpg', 1);

-- Insert sample apps
INSERT INTO `artist_v2_apps` (`name`, `description`, `image_url`, `form_url`, `status`) VALUES
('Instagram', 'A photo and video sharing social networking service', 'assets/img/apps/instagram.jpg', 'https://forms.gle/instagramForm123', 1),
('TikTok', 'A short-form video hosting service', 'assets/img/apps/tiktok.jpg', 'https://forms.gle/tiktokForm123', 1),
('YouTube', 'An online video sharing and social media platform', 'assets/img/apps/youtube.jpg', 'https://forms.gle/youtubeForm123', 1),
('Netflix', 'A subscription-based streaming service', 'assets/img/apps/netflix.jpg', 'https://forms.gle/netflixForm123', 1),
('Spotify', 'A digital music, podcast, and video service', 'assets/img/apps/spotify.jpg', 'https://forms.gle/spotifyForm123', 1),
('Trello', 'A web-based list-making application', 'assets/img/apps/trello.jpg', 'https://forms.gle/trelloForm123', 1),
('Duolingo', 'A language learning platform', 'assets/img/apps/duolingo.jpg', 'https://forms.gle/duolingoForm123', 1),
('MyFitnessPal', 'A smartphone app and website for tracking diet and exercise', 'assets/img/apps/myfitnesspal.jpg', 'https://forms.gle/fitnessForm123', 1);

-- Link apps to categories
INSERT INTO `artist_v2_app_categories` (`app_id`, `category_id`) VALUES
(1, 1), -- Instagram -> Social Media
(2, 1), -- TikTok -> Social Media
(3, 1), -- YouTube -> Social Media
(3, 2), -- YouTube -> Entertainment
(4, 2), -- Netflix -> Entertainment
(5, 2), -- Spotify -> Entertainment
(6, 3), -- Trello -> Productivity
(7, 4), -- Duolingo -> Education
(8, 5); -- MyFitnessPal -> Lifestyle 