<?php
require_once 'db_config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if ($action === 'register_event') {
            // Using $_POST to handle multipart data as well
            $userId = $_POST['user_id'] ?? null;
            $eventId = $_POST['event_id'] ?? null;
            $name = $_POST['name'] ?? null;
            $email = $_POST['email'] ?? null;
            $phone = $_POST['phone'] ?? null;
            $college = $_POST['college'] ?? null;
            $course = $_POST['course'] ?? null;
            $department = $_POST['department'] ?? null;
            $year = $_POST['year_of_study'] ?? null;
            $gender = $_POST['gender'] ?? null;
            $address = $_POST['address'] ?? null;

            if ($userId && $eventId) {
                try {
                    $conn->beginTransaction();

                    // 1. Check if already registered
                    $checkQuery = "SELECT id FROM registrations WHERE user_id = ? AND event_id = ? AND status != 'cancelled'";
                    $checkStmt = $conn->prepare($checkQuery);
                    $checkStmt->execute([$userId, $eventId]);
                    if ($checkStmt->rowCount() > 0) {
                        throw new Exception("You are already registered for this event.");
                    }

                    // 2. Check capacity
                    $capQuery = "SELECT capacity, (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND status != 'cancelled') as current_count 
                                FROM events WHERE id = ?";
                    $capStmt = $conn->prepare($capQuery);
                    $capStmt->execute([$eventId]);
                    $capData = $capStmt->fetch();

                    if ($capData['capacity'] !== null && $capData['current_count'] >= $capData['capacity']) {
                        throw new Exception("Event is at full capacity.");
                    }

                    // 3. Handle ID Card Upload
                    $idCardUrl = null;
                    if (isset($_FILES['id_card'])) {
                        $targetDir = "../../uploads/id_cards/";
                        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
                        
                        $fileName = time() . '_' . basename($_FILES["id_card"]["name"]);
                        $targetFilePath = $targetDir . $fileName;
                        
                        if (move_uploaded_file($_FILES["id_card"]["tmp_name"], $targetFilePath)) {
                            $idCardUrl = 'uploads/id_cards/' . $fileName;
                        }
                    }

                    // 4. Register
                    $query = "INSERT INTO registrations (user_id, event_id, name, email, phone, college, course, department, year_of_study, gender, address, id_card_url, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$userId, $eventId, $name, $email, $phone, $college, $course, $department, $year, $gender, $address, $idCardUrl]);
                    $regId = $conn->lastInsertId();

                    // 5. Generate QR Data
                    $qrData = json_encode([
                        "registration_id" => (int)$regId,
                        "event_id" => (int)$eventId,
                        "name" => $name,
                        "email" => $email,
                        "phone" => $phone,
                        "timestamp" => time()
                    ]);
                    $updateQrQuery = "UPDATE registrations SET qr_code_data = ? WHERE id = ?";
                    $conn->prepare($updateQrQuery)->execute([$qrData, $regId]);

                    // 5. Create notification
                    $eventQuery = "SELECT title FROM events WHERE id = ?";
                    $eventStmt = $conn->prepare($eventQuery);
                    $eventStmt->execute([$eventId]);
                    $event = $eventStmt->fetch();
                    
                    $notifQuery = "INSERT INTO notifications (user_id, title, message, notification_type) VALUES (?, ?, ?, 'event')";
                    $notifStmt = $conn->prepare($notifQuery);
                    $notifStmt->execute([$userId, "Registration Successful", "You have registered for " . $event['title'] . ". We look forward to seeing you!"]);

                    $conn->commit();
                    sendResponse("success", "Registered for event successfully", [
                        "registration_id" => $regId,
                        "qr_code_data" => $qrData
                    ]);
                } catch (Exception $e) {
                    $conn->rollBack();
                    sendResponse("error", $e->getMessage(), null, 400);
                }
            } else {
                sendResponse("error", "User ID and Event ID are required", null, 400);
            }
        }

        if ($action === 'verify_ticket') {
            $qrCodeData = $data->qr_code_data ?? null;

            if ($qrCodeData) {
                try {
                    // Find registration
                    $query = "SELECT r.*, e.title as event_name 
                              FROM registrations r 
                              JOIN events e ON r.event_id = e.id 
                              WHERE r.qr_code_data = ? AND r.status != 'cancelled'";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$qrCodeData]);
                    $registration = $stmt->fetch();

                    if (!$registration) {
                        sendResponse("error", "Invalid Ticket", null, 404);
                    }

                    if ($registration['status'] === 'checked_in') {
                        sendResponse("warning", "Already Used", [
                            "name" => $registration['name'],
                            "event_name" => $registration['event_name'],
                            "registration_id" => $registration['id'],
                            "status" => "Already Used"
                        ], 200);
                    }

                    // Mark as Checked-In
                    $updateQuery = "UPDATE registrations SET status = 'checked_in', attendance_status = 'present', check_in_time = CURRENT_TIMESTAMP WHERE id = ?";
                    $conn->prepare($updateQuery)->execute([$registration['id']]);

                    sendResponse("success", "Check-In Successful", [
                        "name" => $registration['name'],
                        "event_name" => $registration['event_name'],
                        "registration_id" => $registration['id'],
                        "status" => "Checked-In"
                    ]);

                } catch (Exception $e) {
                    sendResponse("error", $e->getMessage(), null, 500);
                }
            } else {
                sendResponse("error", "QR Code data is required", null, 400);
            }
        }
        break;

    case 'GET':
        if ($action === 'user_registrations' && isset($_GET['user_id'])) {
            $query = "SELECT r.*, e.title, e.event_date, e.location, e.image_url 
                      FROM registrations r 
                      JOIN events e ON r.event_id = e.id 
                      WHERE r.user_id = ? AND r.status != 'cancelled'
                      ORDER BY e.event_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_GET['user_id']]);
            sendResponse("success", "User registrations retrieved", $stmt->fetchAll());
        }
        
        if ($action === 'event_attendees' && isset($_GET['event_id'])) {
            $query = "SELECT r.*, u.name, u.email, u.phone 
                      FROM registrations r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE r.event_id = ? AND r.status != 'cancelled'
                      ORDER BY u.name ASC";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_GET['event_id']]);
            sendResponse("success", "Event attendees retrieved", $stmt->fetchAll());
        }
        break;
}

sendResponse("error", "Invalid request or method", null, 404);
?>
