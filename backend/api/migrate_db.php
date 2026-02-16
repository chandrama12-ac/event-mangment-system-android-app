<?php
require_once __DIR__ . '/db_config.php';

$sql = "ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email,
        ADD COLUMN IF NOT EXISTS dob DATE AFTER phone,
        ADD COLUMN IF NOT EXISTS education VARCHAR(255) AFTER dob,
        ADD COLUMN IF NOT EXISTS skills TEXT AFTER education,
        ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) AFTER skills";

try {
    $conn->exec($sql);
    sendResponse("success", "Database schema updated successfully");
} catch (PDOException $e) {
    sendResponse("error", "Failed to update database schema: " . $e->getMessage(), null, 500);
}
?>
