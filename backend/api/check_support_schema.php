<?php
require_once 'db_config.php';

try {
    $stmt = $conn->query("DESCRIBE support_tickets");
    $schema = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "schema" => $schema]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
