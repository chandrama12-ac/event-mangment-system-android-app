<?php
require_once 'db_config.php';

try {
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("DROP TABLE IF EXISTS notifications");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    $query = "CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'reminder', 'announcement', 'success') DEFAULT 'info',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($query);
    echo "Notifications table recreated.\n";

    $userStmt = $conn->query("SELECT id FROM users LIMIT 10");
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
        foreach ($users as $u) {
            $stmt->execute([$u['id'], 'Welcome!', 'System is now fully functional!', 'success']);
        }
        echo "Seed notifications successful.";
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>
