<?php
require_once 'db_config.php';

$report = [
    "php_version" => PHP_VERSION,
    "database" => [
        "status" => "unknown",
        "tables" => []
    ],
    "folders" => [
        "uploads" => [
            "exists" => is_dir('../../uploads'),
            "writable" => is_writable('../../uploads')
        ]
    ]
];

try {
    $conn->query("SELECT 1");
    $report["database"]["status"] = "connected";
    
    $tables = ['users', 'admins', 'events', 'registrations', 'notifications'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        $report["database"]["tables"][$table] = ($stmt->rowCount() > 0) ? "exists" : "missing";
    }
} catch (Exception $e) {
    $report["database"]["status"] = "error: " . $e->getMessage();
}

sendResponse("success", "System Diagnostic Report", $report);
?>
