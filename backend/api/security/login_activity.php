<?php
require_once __DIR__ . '/../db_config.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    sendResponse("error", "User ID is required", null, 400);
}

try {
    $stmt = $conn->prepare("SELECT last_login_at, last_login_ip FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $activity = $stmt->fetch();

    if (!$activity) {
        sendResponse("error", "User not found", null, 404);
    }

    sendResponse("success", "Activity retrieved", $activity);
} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
