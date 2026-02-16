<?php
require_once 'db_config.php';

try {
    // 1. Add qr_code_data column
    $sql1 = "ALTER TABLE registrations 
             ADD COLUMN IF NOT EXISTS name VARCHAR(255) AFTER user_id,
             ADD COLUMN IF NOT EXISTS email VARCHAR(255) AFTER name,
             ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email,
             ADD COLUMN IF NOT EXISTS qr_code_data TEXT AFTER id_card_url,
             ADD COLUMN IF NOT EXISTS department VARCHAR(255) AFTER course";
    $conn->exec($sql1);

    echo json_encode(["status" => "success", "message" => "Migration v5 successful (QR data and user contact columns added)"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Migration failed: " . $e->getMessage()]);
}
?>
