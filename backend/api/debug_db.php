<?php
include_once 'db_config.php';

echo "--- DATABASE DIAGNOSTIC ---\n";
echo "Database Name: " . $db_name . "\n";
echo "Host: " . $host . "\n";
echo "Port: " . $port . "\n";

try {
    // 1. Check Admins Table
    echo "\n--- ADMINS TABLE ---\n";
    $stmt = $conn->query("SELECT admin_email, full_name FROM admins");
    $admins = $stmt->fetchAll();
    if (empty($admins)) {
        echo "No admins found in database.\n";
    } else {
        foreach ($admins as $admin) {
            echo "Email: " . $admin['admin_email'] . " | Name: " . $admin['full_name'] . "\n";
        }
    }

    // 2. Check Users Table
    echo "\n--- USERS TABLE ---\n";
    $stmt = $conn->query("SELECT email, name FROM users");
    $users = $stmt->fetchAll();
    if (empty($users)) {
        echo "No users found in database.\n";
    } else {
        foreach ($users as $user) {
            echo "Email: " . $user['email'] . " | Name: " . $user['name'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
