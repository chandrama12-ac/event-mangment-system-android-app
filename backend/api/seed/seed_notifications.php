<?php
require_once __DIR__ . '/../db_config.php';

try {
    // Check if notifications table is empty
    $checkQuery = "SELECT COUNT(*) FROM notifications";
    $count = $conn->query($checkQuery)->fetchColumn();

    if ($count == 0) {
        $sql = file_get_contents(__DIR__ . '/../../../database/seed_notifications.sql');
        if ($sql) {
            $conn->exec($sql);
            sendResponse("success", "Global notifications seeded successfully");
        } else {
            sendResponse("error", "Seed SQL file not found", null, 404);
        }
    } else {
        sendResponse("success", "Notifications table already has data. Skipping seed.");
    }
} catch (PDOException $e) {
    sendResponse("error", "Seeding failed: " . $e->getMessage(), null, 500);
}
?>
