<?php
include_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->name) && isset($data->email) && isset($data->password) && isset($data->phone)) {
    $name = $data->name;
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    $phone = $data->phone;

    // Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email = :email OR phone = :phone";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(":email", $email);
    $checkStmt->bindParam(":phone", $phone);
    $checkStmt->execute();

    if($checkStmt->rowCount() > 0) {
        sendResponse("error", "User with this email or phone already exists.");
    }

    $query = "INSERT INTO users (name, email, phone, password_hash) VALUES (:name, :email, :phone, :password)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":password", $password);

    if($stmt->execute()) {
        sendResponse("success", "User registered successfully.", ["id" => $conn->lastInsertId()]);
    } else {
        sendResponse("error", "Unable to register user.");
    }
} else {
    sendResponse("error", "Incomplete data.");
}
?>
