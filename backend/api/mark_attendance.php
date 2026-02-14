<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Method Not Allowed", null, 405);
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) $data = $_POST;

$ticketId = $data['ticket_id'] ?? null;
$action = $data['action'] ?? 'attend'; // 'attend' or 'block'

if (empty($ticketId)) {
    sendResponse("error", "Ticket ID is required", null, 400);
}

try {
    if ($action === 'attend') {
        $query = "UPDATE registrations SET attendance_status = 'present', status = 'checked_in' WHERE ticket_id = ?";
        $message = "User marked as attended";
    } else {
        $query = "UPDATE registrations SET status = 'cancelled' WHERE ticket_id = ?";
        $message = "Entry blocked (registration cancelled)";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute([$ticketId]);

    if ($stmt->rowCount() > 0) {
        sendResponse("success", $message);
    } else {
        sendResponse("error", "Ticket not found or already in that status", null, 404);
    }

} catch (PDOException $e) {
    logError("Database Error during attendance update: " . $e->getMessage());
    sendResponse("error", "Database failure", null, 500);
}

/**
 * Custom error logger
 */
function logError($message) {
    $logFile = "../../logs/api_errors.log";
    $logDir = dirname($logFile);
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}
?>
