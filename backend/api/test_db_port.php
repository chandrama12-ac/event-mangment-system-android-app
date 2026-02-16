<?php
$ports = [3306, 3307];
foreach ($ports as $port) {
    try {
        $dsn = "mysql:host=localhost;port=$port;dbname=event_management";
        $conn = new PDO($dsn, "root", "");
        echo "Connected successfully to port $port\n";
    } catch (PDOException $e) {
        echo "Failed to connect to port $port: " . $e->getMessage() . "\n";
    }
}
?>
