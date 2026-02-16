<?php
include_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->google_id)) {
    $email = $data->email;
    $google_id = $data->google_id;
    $name = isset($data->name) ? $data->name : "";
    
    // Check if user exists by google_id or email
    $query = "SELECT * FROM users WHERE google_id = :google_id OR email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":google_id", $google_id);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Update google_id if missing
        if(empty($user['google_id'])) {
            $updateQuery = "UPDATE users SET google_id = :google_id WHERE id = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(":google_id", $google_id);
            $updateStmt->bindParam(":id", $user['id']);
            $updateStmt->execute();
        }
        sendResponse("success", "Login successful.", ["id" => $user['id'], "name" => $user['name']]);
    } else {
        // Register new user
        $regQuery = "INSERT INTO users SET name=:name, email=:email, google_id=:google_id, created_at=NOW()";
        $regStmt = $conn->prepare($regQuery);
        $regStmt->bindParam(":name", $name);
        $regStmt->bindParam(":email", $email);
        $regStmt->bindParam(":google_id", $google_id);
        
        if($regStmt->execute()) {
             sendResponse("success", "User registered and logged in.", ["id" => $conn->lastInsertId()]);
        } else {
             sendResponse("error", "Failed to register user.");
        }
    }
} else {
    sendResponse("error", "Incomplete data.");
}
?>
