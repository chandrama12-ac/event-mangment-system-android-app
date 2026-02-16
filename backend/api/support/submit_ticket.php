<?php
require_once __DIR__ . '/../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Only POST method is allowed", null, 405);
}

$user_id = $_POST['user_id'] ?? null;
$issue_type = $_POST['issue_type'] ?? null;
$message = $_POST['message'] ?? null;

if (!$issue_type || !$message) {
    sendResponse("error", "Issue type and message are required", null, 400);
}

try {
    $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, issue_type, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $issue_type, $message]);

    sendResponse("success", "Support ticket submitted successfully");
} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
