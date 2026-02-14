<?php
require_once __DIR__ . '/../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Only POST method is allowed", null, 405);
}

$user_id = $_POST['user_id'] ?? null;
$old_password = $_POST['old_password'] ?? null;
$new_password = $_POST['new_password'] ?? null;

if (!$user_id || !$old_password || !$new_password) {
    sendResponse("error", "All fields are required", null, 400);
}

try {
    // 1. Verify old password
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($old_password, $user['password_hash'])) {
        sendResponse("error", "Incorrect old password", null, 401);
    }

    // 2. Update to new password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$new_hash, $user_id]);

    sendResponse("success", "Password updated successfully");
} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
