<?php
include_once 'db_config.php';

echo "<h2>Admin Setup Tool</h2>";

try {
    $email = "chandramakumar2004@gmail.com";
    $password_hash = '$2y$12$F4YGR9eY2EnUDPZ.uKPs/ez2DmluRKQFjbywtTuTmTO3ulmi83Tpi'; // Password: chan@123
    
    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE admin_email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: orange;'>Admin account already exists.</p>";
    } else {
        // Insert
        $query = "INSERT INTO admins (admin_username, admin_email, password_hash, full_name, secret_key, admin_level)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'admin_chandra',
            $email,
            $password_hash,
            'Chandrama Kumar',
            'SECURE_KEY_2024_CHANDRA',
            'super_admin'
        ]);
        echo "<p style='color: green;'>Admin account created successfully!</p>";
        echo "<ul><li>Email: $email</li><li>Password: chan@123</li></ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
