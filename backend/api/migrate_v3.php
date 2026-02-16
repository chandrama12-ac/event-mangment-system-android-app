<?php
require_once __DIR__ . '/db_config.php';

try {
    // 1. Add gender and bio columns to users table
    $alterUsersTable = "ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS gender VARCHAR(20),
        ADD COLUMN IF NOT EXISTS bio TEXT";
    $conn->exec($alterUsersTable);

    sendResponse("success", "Database schema v3 (gender, bio) updated successfully");
} catch (PDOException $e) {
    sendResponse("error", "Failed to update database schema v3: " . $e->getMessage(), null, 500);
}
?>
