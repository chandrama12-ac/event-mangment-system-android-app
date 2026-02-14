<?php
// backend/api/analytics.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

require_once 'db_config.php';

try {
    // 1. Total Events
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM events");
    $total_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Total Users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Total Registrations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM registrations");
    $total_registrations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 4. Registrations by Category
    $stmt = $pdo->query("SELECT category, COUNT(*) as count 
                         FROM events e 
                         JOIN registrations r ON e.id = r.event_id 
                         GROUP BY category");
    $by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Recent Registrations
    $stmt = $pdo->query("SELECT u.name, e.title, r.created_at 
                         FROM registrations r 
                         JOIN users u ON r.user_id = u.id 
                         JOIN events e ON r.event_id = e.id 
                         ORDER BY r.created_at DESC LIMIT 5");
    $recent_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Attendance Summary
    $stmt = $pdo->query("SELECT attendance_status, COUNT(*) as count 
                         FROM registrations 
                         GROUP BY attendance_status");
    $attendance_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => [
            "total_events" => (int)$total_events,
            "total_users" => (int)$total_users,
            "total_registrations" => (int)$total_registrations,
            "by_category" => $by_category,
            "recent_registrations" => $recent_registrations,
            "attendance_summary" => $attendance_summary
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
