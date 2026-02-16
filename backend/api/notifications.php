<?php
require_once 'db_config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

if ($method === 'GET') {
    if ($action === 'list') {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            sendResponse("error", "User ID required");
        }
        
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        sendResponse("success", "Notifications retrieved", $stmt->fetchAll());
    }

    if ($action === 'unread_count') {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            sendResponse("error", "User ID required");
        }
        
        $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        sendResponse("success", "Unread count retrieved", $stmt->fetch());
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if ($action === 'mark_read') {
        if (!empty($data->id)) {
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            if ($stmt->execute([$data->id])) {
                sendResponse("success", "Notification marked as read");
            }
        }
        sendResponse("error", "Notification ID required");
    }

    if ($action === 'create_broadcast') {
        // Admin only (simplified for demo)
        if (!empty($data->title) && !empty($data->message)) {
            $stmt = $conn->query("SELECT id FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $insert = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
            foreach ($users as $u) {
                $insert->execute([$u['id'], $data->title, $data->message, $data->type ?? 'announcement']);
            }
            sendResponse("success", "Broadcast sent to " . count($users) . " users");
        }
        sendResponse("error", "Title and message required");
    }
}
?>
