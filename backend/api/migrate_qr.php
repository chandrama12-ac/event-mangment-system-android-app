<?php
require_once 'db_config.php';

try {
    // Check and add ticket_id
    $stmt = $conn->query("SHOW COLUMNS FROM registrations LIKE 'ticket_id'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE registrations ADD COLUMN ticket_id VARCHAR(50) UNIQUE AFTER event_id");
        echo "Added column: ticket_id\n";
    }

    // Check and add qr_code_path
    $stmt = $conn->query("SHOW COLUMNS FROM registrations LIKE 'qr_code_path'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE registrations ADD COLUMN qr_code_path VARCHAR(255) AFTER ticket_id");
        echo "Added column: qr_code_path\n";
    }

    // Check and add registered_at
    $stmt = $conn->query("SHOW COLUMNS FROM registrations LIKE 'registered_at'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE registrations ADD COLUMN registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER qr_code_path");
        echo "Added column: registered_at\n";
    }

    // Check and add attendance_status
    $stmt = $conn->query("SHOW COLUMNS FROM registrations LIKE 'attendance_status'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE registrations ADD COLUMN attendance_status ENUM('pending', 'present', 'absent') DEFAULT 'pending'");
        echo "Added column: attendance_status\n";
    }

    echo "Database migration for QR system completed.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
?>
