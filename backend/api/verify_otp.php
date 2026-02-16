<?php
include_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->phone) && isset($data->otp)) {
    $phone = $data->phone;
    $otp = $data->otp;

    $query = "SELECT * FROM otp_codes WHERE phone = :phone AND otp = :otp";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":otp", $otp);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        // OTP verified
        // Check if user exists, if not, create a temp user or handle in complete profile
        $userQuery = "SELECT * FROM users WHERE phone = :phone";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bindParam(":phone", $phone);
        $userStmt->execute();

        if($userStmt->rowCount() > 0) {
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            sendResponse("success", "Login successful.", ["id" => $user['id'], "name" => $user['name']]);
        } else {
            // Register new user with phone only for now
            $regQuery = "INSERT INTO users SET phone=:phone, created_at=NOW()";
            $regStmt = $conn->prepare($regQuery);
            $regStmt->bindParam(":phone", $phone);
            if($regStmt->execute()) {
                 sendResponse("success", "User registered and logged in.", ["id" => $conn->lastInsertId()]);
            } else {
                 sendResponse("error", "OTP verified but failed to create user.");
            }
        }
        
        // Delete used OTP
        $delQuery = "DELETE FROM otp_codes WHERE phone = :phone";
        $delStmt = $conn->prepare($delQuery);
        $delStmt->bindParam(":phone", $phone);
        $delStmt->execute();

    } else {
        sendResponse("error", "Invalid OTP.");
    }
} else {
    sendResponse("error", "Incomplete data.");
}
?>
