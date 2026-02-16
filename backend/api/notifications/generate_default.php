<?php
require_once __DIR__ . '/../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Only POST method is allowed", null, 405);
}

$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    sendResponse("error", "User ID is required", null, 400);
}

try {
    // Check if user already has welcome notifications
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND title LIKE 'Welcome%'");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() == 0) {
        // 1. Welcome Notification
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, notification_type, priority) VALUES (?, 'Welcome to Campus Connect! 🎓', 'We are thrilled to have you here. Start exploring workshops and hackathons now!', 'system', 'high')");
        $stmt->execute([$user_id]);

        // 2. Profile Reminder
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, notification_type, priority) VALUES (?, 'Complete Your Profile', 'Add your skills and education to get better event recommendations.', 'system', 'medium')");
        $stmt->execute([$user_id]);

        // 3. Upcoming Event Suggestion
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, notification_type, priority) VALUES (?, 'Nearby Tech Workshop', 'An AI & ML Workshop is happening soon. Dont miss out!', 'event', 'medium')");
        $stmt->execute([$user_id]);

        sendResponse("success", "Default notifications generated for user");
    } else {
        sendResponse("success", "User already has default notifications");
    }
} catch (PDOException $e) {
    sendResponse("error", "Notification generation failed: " . $e->getMessage(), null, 500);
}
?>
