<?php
// Manual connection to avoid db_config.php's sendResponse/exit
require_once __DIR__ . '/../config/db.php';

echo "Testing connection to host: " . DB_HOST . " port: " . DB_PORT . " db: " . DB_NAME . "\n";

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!\n\n";

    function checkTable($conn, $tableName) {
        echo "Schema for $tableName:\n";
        try {
            $stmt = $conn->query("DESCRIBE $tableName");
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo " - " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
        } catch (Exception $e) {
            echo " Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    checkTable($conn, 'admins');
    checkTable($conn, 'users');

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    echo "Check if MySQL is running on port " . DB_PORT . " and credentials are correct.\n";
}
?>
