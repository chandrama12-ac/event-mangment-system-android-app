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
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    sendResponse("success", "Account deleted successfully");
} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
