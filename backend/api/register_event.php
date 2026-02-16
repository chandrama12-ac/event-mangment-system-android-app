<?php
require_once 'db_config.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Method Not Allowed", null, 405);
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// If JSON decoding fails, check $_POST (for multipart compatibility)
if (!$data) {
    $data = $_POST;
}

// Extract parameters
$userId = $data['user_id'] ?? null;
$eventId = $data['event_id'] ?? null;
$fullName = $data['full_name'] ?? null;
$email = $data['email'] ?? null;
$phone = $data['phone'] ?? null;
$college = $data['college'] ?? null;
$department = $data['department'] ?? null;
$year = $data['year'] ?? null;
$gender = $data['gender'] ?? null;
$address = $data['address'] ?? null;

// Validation
$requiredFields = ['event_id', 'full_name', 'email', 'phone', 'college', 'department', 'year', 'gender', 'address'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        sendResponse("error", "Field '$field' is required", null, 400);
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse("error", "Invalid email format", null, 400);
}

try {
    // If userId is missing, try to find it via email
    if (!$userId) {
        $userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch();
        if ($user) {
            $userId = $user['id'];
        } else {
            sendResponse("error", "User not found. Please register an account first.", null, 404);
        }
    }

    // UPDATED: Changed 'event_registrations' to 'registrations' to match SQL schema
    // Check if user is already registered for this event
    $checkQuery = "SELECT id FROM registrations WHERE user_id = ? AND event_id = ? AND status != 'cancelled'";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([$userId, $eventId]);
    if ($checkStmt->rowCount() > 0) {
        sendResponse("error", "You are already registered for this event", null, 400);
    }

    // Generate unique ticket_id
    $ticketId = "EVT" . time() . $userId;

    // Insert into registrations table (matching the SQL schema columns)
    $query = "INSERT INTO registrations (user_id, event_id, status, registration_data)
              VALUES (?, ?, 'approved', ?)";
    $stmt = $conn->prepare($query);
    
    // Package extra data into the registration_data JSON column
    $registrationData = json_encode([
        "full_name" => $fullName,
        "email" => $email,
        "phone" => $phone,
        "college" => $college,
        "department" => $department,
        "year" => $year,
        "gender" => $gender,
        "address" => $address,
        "ticket_id" => $ticketId
    ]);

    $stmt->execute([$userId, $eventId, $registrationData]);
    
    $registrationId = $conn->lastInsertId();

    // Success Response
    sendResponse("success", "Registered Successfully", [
        "registration_id" => $registrationId,
        "ticket_id" => $ticketId
    ]);

} catch (PDOException $e) {
    sendResponse("error", "Database failure: " . $e->getMessage(), null, 500);
} catch (Exception $e) {
    sendResponse("error", $e->getMessage(), null, 400);
}

?>
