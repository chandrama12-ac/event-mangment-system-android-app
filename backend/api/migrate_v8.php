<?php
require_once 'db_config.php';

try {
    echo "Starting migration...\n";

    // 1. Create support_tickets table
    $query1 = "CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        issue_type VARCHAR(100),
        message TEXT,
        status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->exec($query1);
    echo "Table 'support_tickets' created or already exists.\n";

    // 2. Update registrations table with new required fields
    $columnsToAdd = [
        'full_name' => "VARCHAR(255) AFTER user_id",
        'college' => "VARCHAR(255) AFTER phone",
        'department' => "VARCHAR(255) AFTER college",
        'year' => "VARCHAR(50) AFTER department",
        'gender' => "VARCHAR(20) AFTER year",
        'address' => "TEXT AFTER gender"
    ];

    foreach ($columnsToAdd as $column => $definition) {
        $check = $conn->query("SHOW COLUMNS FROM registrations LIKE '$column'");
        if ($check->rowCount() == 0) {
            $conn->exec("ALTER TABLE registrations ADD $column $definition");
            echo "Column '$column' added to 'registrations'.\n";
        } else {
            echo "Column '$column' already exists in 'registrations'.\n";
        }
    }

    // 3. Optional: Copy 'name' to 'full_name' if 'full_name' is empty
    $conn->exec("UPDATE registrations SET full_name = name WHERE (full_name IS NULL OR full_name = '') AND name IS NOT NULL");
    echo "Data synced from 'name' to 'full_name'.\n";

    sendResponse("success", "Migration V8 completed successfully");

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    sendResponse("error", "Migration V8 failed: " . $e->getMessage(), null, 500);
}
?>
