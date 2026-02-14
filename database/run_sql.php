<?php
require_once 'C:/xampp/htdocs/event_management/backend/api/db_config.php';

try {
    $sql = file_get_contents(__DIR__ . '/create_event_registrations.sql');
    $conn->exec($sql);
    echo "Table 'event_registrations' created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
