<?php
require_once 'db_config.php';

try {
    $tablesStmt = $conn->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "Table: $table\n";
        $columnsStmt = $conn->query("DESCRIBE $table");
        while ($column = $columnsStmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
