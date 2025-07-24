-- Talent Registration Database Tables

-- Create talent registrations table
CREATE TABLE IF NOT EXISTS `artist_v2_talent_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Prefer not to say','Other') NOT NULL,
  `talent_category` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status_index` (`status`),
  KEY `talent_category_index` (`talent_category`),
  KEY `created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create talent categories reference table
CREATE TABLE IF NOT EXISTS `artist_v2_talent_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert talent categories from the form
INSERT INTO `artist_v2_talent_categories` (`name`, `status`) VALUES
('Dating App Host', 1),
('Video Live Streamers', 1),
('Voice Live Streamers', 1),
('Singer', 1),
('Dancer', 1),
('Actor / Actress', 1),
('Model', 1),
('Artist / Painter', 1),
('Social Media Influencer', 1),
('Content Creator', 1),
('Vlogger', 1),
('Gamer / Streamer', 1),
('YouTuber', 1),
('Anchor / Emcee / Host', 1),
('DJ / Music Producer', 1),
('Photographer / Videographer', 1),
('Makeup Artist / Hair Stylist', 1),
('Fashion Designer / Stylist', 1),
('Fitness Trainer / Yoga Instructor', 1),
('Motivational Speaker / Life Coach', 1),
('Chef / Culinary Artist', 1),
('Child Artist', 1),
('Pet Performer / Pet Model', 1),
('Instrumental Musician', 1),
('Director / Scriptwriter / Editor', 1),
('Voice Over Artist', 1),
('Magician / Illusionist', 1),
('Stand-up Comedian', 1),
('Mimicry Artist', 1),
('Poet / Storyteller', 1),
('Language Trainer / Public Speaking Coach', 1),
('Craft Expert / DIY Creator', 1),
('Travel Blogger / Explorer', 1),
('Astrologer / Tarot Reader', 1),
('Educator / Subject Matter Expert', 1),
('Tech Reviewer / Gadget Expert', 1),
('Unboxing / Product Reviewer', 1),
('Business Coach / Startup Mentor', 1),
('Health & Wellness Coach', 1),
('Event Anchor / Wedding Host', 1); 