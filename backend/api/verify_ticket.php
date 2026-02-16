<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Method Not Allowed", null, 405);
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) $data = $_POST;

$ticketId = $data['ticket_id'] ?? null;

if (empty($ticketId)) {
    sendResponse("error", "Ticket ID is required", null, 400);
}

try {
    // Receive ticket_id and check DB (PART 5 - Steps 1 & 2)
    $query = "SELECT r.*, e.title as event_name, e.event_date 
              FROM registrations r 
              JOIN events e ON r.event_id = e.id 
              WHERE r.ticket_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$ticketId]);
    $registration = $stmt->fetch();

    if (!$registration) {
        logError("Verification failed: Invalid Ticket ID $ticketId");
        sendResponse("error", "Invalid Ticket", null, 404);
    }

    // Validate: Ticket exists, Event valid, Not already used (PART 5 - Step 3)
    if ($registration['status'] === 'cancelled') {
        sendResponse("error", "This registration has been cancelled", null, 400);
    }

    if ($registration['attendance_status'] === 'present') {
        sendResponse("error", "Ticket already used", [
            "ticket_id" => $registration['ticket_id'],
            "user_name" => $registration['full_name'] ?? $registration['name'],
            "event_name" => $registration['event_name'],
            "entry" => "Already Used"
        ], 400);
    }

    // Return success (PART 4 - Valid)
    sendResponse("success", "Ticket is valid", [
        "user_name" => $registration['full_name'] ?? $registration['name'],
        "event_name" => $registration['event_name'],
        "entry" => "Allowed"
    ]);

} catch (PDOException $e) {
    logError("Database Error during verification: " . $e->getMessage());
    sendResponse("error", "Database failure", null, 500);
}

?>
