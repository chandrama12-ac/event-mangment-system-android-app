<?php
require_once 'db_config.php';

try {
    $tableName = 'registrations';
    echo "Schema for $tableName:\n";
    $stmt = $conn->query("DESCRIBE $tableName");
    $fields = $stmt->fetchAll();
    echo json_encode($fields, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
