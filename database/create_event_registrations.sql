-- Create the event_registrations table
CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `college` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `ticket_id` varchar(100) NOT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT 'approved',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_event` (`user_id`,`event_id`),
  UNIQUE KEY `ticket_id` (`ticket_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
