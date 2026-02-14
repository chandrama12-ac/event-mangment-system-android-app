<?php
require_once 'db_config.php';

try {
    $email = 'admin@event.com';
    $pass = 'admin123';
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $name = 'System Admin';
    $username = 'admin';

    $stmt = $conn->prepare("SELECT id FROM admins WHERE admin_email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO admins (full_name, admin_username, admin_email, password_hash, is_active, admin_level) VALUES (?, ?, ?, ?, 1, 'super_admin')");
        $stmt->execute([$name, $username, $email, $hash]);
        echo "Admin created: $email / $pass\n";
    } else {
        echo "Admin $email already exists.\n";
    }

    echo "\nVerifying login logic in login.php...\n";
    $loginContent = file_get_contents('login.php');
    if (strpos($loginContent, 'password_verify') !== false) {
        echo "Login logic uses password_verify (Bcrypt) -> OK\n";
    } else {
        echo "WARNING: Login logic might NOT be using Bcrypt correctly!\n";
    }

} catch (Exception $e) {
    echo "Check failed: " . $e->getMessage();
}
?>
