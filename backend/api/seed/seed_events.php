<?php
require_once __DIR__ . '/../db_config.php';

try {
    // Check if events table is empty
    $checkQuery = "SELECT COUNT(*) FROM events";
    $count = $conn->query($checkQuery)->fetchColumn();

    if ($count == 0) {
        $sql = file_get_contents(__DIR__ . '/../../../database/seed_events.sql');
        if ($sql) {
            $conn->exec($sql);
            sendResponse("success", "Demo events seeded successfully");
        } else {
            sendResponse("error", "Seed SQL file not found", null, 404);
        }
    } else {
        sendResponse("success", "Events table already has data. Skipping seed.");
    }
} catch (PDOException $e) {
    sendResponse("error", "Seeding failed: " . $e->getMessage(), null, 500);
}
?>
