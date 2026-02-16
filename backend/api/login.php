<?php
include_once 'db_config.php';

// 1. Get Input Data (Handle JSON, POST, or empty)
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? $_POST['email'] ?? null;
$password = $data['password'] ?? $_POST['password'] ?? null;

// TEST FALLBACK: If no data is provided, use requested test credentials
if (empty($email) && empty($password)) {
    $email = "chandramakumar2004@gmail.com";
    $password = "chan@123";
    // Optional: Log that a fallback was used (helpful for debugging)
    // $is_fallback = true;
}

if(!empty($email) && !empty($password)) {
    // 1. Try Admin Login first
    $query = "SELECT * FROM admins WHERE admin_email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $authenticated = false;
        if (isset($row['password_hash'])) {
            $authenticated = password_verify($password, $row['password_hash']);
        } elseif (isset($row['password'])) {
            // WARNING: Fallback for plaintext password (NOT SECURE - for testing/migration only)
            $authenticated = ($password === $row['password']);
        }

        if($authenticated) {
            sendResponse("success", "Admin login successful.", [
                "id" => $row['id'],
                "name" => $row['full_name'] ?? $row['name'] ?? 'Admin',
                "email" => $row['admin_email'] ?? $row['email'],
                "role" => "admin"
            ]);
        } else {
            sendResponse("error", "Invalid admin password.");
        }
    }

    // 2. Try User Login if Admin not found
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $authenticated = false;
        if (isset($row['password_hash'])) {
            $authenticated = password_verify($password, $row['password_hash']);
        } elseif (isset($row['password'])) {
            // WARNING: Fallback for plaintext password (NOT SECURE - for testing/migration only)
            $authenticated = ($password === $row['password']);
        }

        if($authenticated) {
            sendResponse("success", "User login successful.", [
                "id" => $row['id'],
                "name" => $row['name'],
                "email" => $row['email'],
                "role" => "user"
            ]);
        } else {
            sendResponse("error", "Invalid user password.");
        }
    }

    sendResponse("error", "Account not found.");
} else {
    sendResponse("error", "Incomplete data.");
}
?>
