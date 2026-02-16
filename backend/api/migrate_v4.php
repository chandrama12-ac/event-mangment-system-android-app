<?php
require_once __DIR__ . '/db_config.php';

try {
    // 1. Add columns to registrations table
    $alterRegistrationsTable = "ALTER TABLE registrations 
        ADD COLUMN IF NOT EXISTS college VARCHAR(150),
        ADD COLUMN IF NOT EXISTS course VARCHAR(100),
        ADD COLUMN IF NOT EXISTS year_of_study VARCHAR(20),
        ADD COLUMN IF NOT EXISTS gender VARCHAR(20),
        ADD COLUMN IF NOT EXISTS address TEXT,
        ADD COLUMN IF NOT EXISTS id_card_url VARCHAR(255)";
    $conn->exec($alterRegistrationsTable);

    sendResponse("success", "Database schema v4 (registration fields) updated successfully");
} catch (PDOException $e) {
    sendResponse("error", "Failed to update database schema v4: " . $e->getMessage(), null, 500);
}
?>
