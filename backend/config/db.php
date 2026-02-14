<?php
// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307'); // Default XAMPP is 3306, user has 3307 in existing files
define('DB_NAME', 'event_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Other configuration
// Fixed the typo in the folder name: event_mangment -> event_management
define('BASE_URL', 'http://localhost/event_management/backend/api/');
define('UPLOAD_DIR', '../../uploads/');
?>
