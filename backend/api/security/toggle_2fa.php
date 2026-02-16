<?php
require_once __DIR__ . '/../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Only POST method is allowed", null, 405);
}

$user_id = $_POST['user_id'] ?? null;
$enabled = isset($_POST['enabled']) ? ($_POST['enabled'] == 'true' || $_POST['enabled'] == '1' ? 1 : 0) : null;

if (!$user_id || $enabled === null) {
    sendResponse("error", "Missing parameters", null, 400);
}

try {
    $stmt = $conn->prepare("UPDATE users SET two_factor_enabled = ? WHERE id = ?");
    $stmt->execute([$enabled, $user_id]);

    sendResponse("success", "2FA " . ($enabled ? "enabled" : "disabled") . " successfully", ["enabled" => (bool)$enabled]);
} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
