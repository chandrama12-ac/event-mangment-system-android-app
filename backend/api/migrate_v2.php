<?php
require_once __DIR__ . '/db_config.php';

try {
    // 1. Create support_tickets table
    $createTicketsTable = "CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        subject VARCHAR(100),
        message TEXT,
        rating INT DEFAULT 0,
        email VARCHAR(100),
        status ENUM('open','closed') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($createTicketsTable);

    // 2. Add security columns to users table
    $alterUsersTable = "ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS two_factor_enabled TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS profile_visible TINYINT(1) DEFAULT 1,
        ADD COLUMN IF NOT EXISTS email_visible TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS phone_visible TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS last_login_at DATETIME,
        ADD COLUMN IF NOT EXISTS last_login_ip VARCHAR(45)";
    $conn->exec($alterUsersTable);

    sendResponse("success", "Database schema v2 updated successfully");
} catch (PDOException $e) {
    sendResponse("error", "Failed to update database schema v2: " . $e->getMessage(), null, 500);
}
?>
