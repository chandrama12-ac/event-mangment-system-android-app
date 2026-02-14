<?php
// Secure this script or delete it after use
require_once __DIR__ . '/../backend/api/db_config.php';

try {
    $sql = file_get_contents(__DIR__ . '/create_event_registrations.sql');
    $conn->exec($sql);
    echo "<h1>Database Success</h1>";
    echo "<p>Table 'event_registrations' created or already exists.</p>";
    
    $stmt = $conn->query("SHOW TABLES LIKE 'event_registrations'");
    if ($stmt->fetch()) {
        echo "<p>Verification: Table EXISTS.</p>";
    } else {
        echo "<p style='color:red;'>Verification: Table DOES NOT EXIST.</p>";
    }
} catch (PDOException $e) {
    echo "<h1>Database Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
