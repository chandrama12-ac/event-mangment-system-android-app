<?php
require_once __DIR__ . '/../config/db.php';

echo "Attempting schema update...\n";

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Check admins table
    $stmt = $conn->query("DESCRIBE admins");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('password_hash', $columns)) {
        echo "Adding 'password_hash' to admins...\n";
        $conn->exec("ALTER TABLE admins ADD COLUMN password_hash VARCHAR(255) AFTER admin_email");
    }

    // 2. Check users table
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('password_hash', $columns)) {
        echo "Adding 'password_hash' to users...\n";
        $conn->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) AFTER email");
    }

    // 3. Update test user (if exists) to use hashed password
    // Password: chan@123
    $hash = password_hash("chan@123", PASSWORD_DEFAULT);
    $conn->prepare("UPDATE admins SET password_hash = ? WHERE admin_email = ?")->execute([$hash, "chandramakumar2004@gmail.com"]);
    $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?")->execute([$hash, "chandramakumar2004@gmail.com"]);

    echo "Update completed successfully.";

} catch (PDOException $e) {
    echo "Update failed: " . $e->getMessage();
}
?>
