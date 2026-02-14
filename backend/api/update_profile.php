<?php
require_once __DIR__ . '/db_config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse("error", "Only POST method is allowed", null, 405);
}

// Get user ID from POST data (In a real app, this would come from a JWT/Session)
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

if (!$user_id) {
    sendResponse("error", "User ID is required", null, 400);
}

// Prepare data from POST
$name = isset($_POST['name']) ? $_POST['name'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$dob = isset($_POST['dob']) ? $_POST['dob'] : null;
$education = isset($_POST['education']) ? $_POST['education'] : null;
$skills = isset($_POST['skills']) ? $_POST['skills'] : null;
$gender = isset($_POST['gender']) ? $_POST['gender'] : null;
$bio = isset($_POST['bio']) ? $_POST['bio'] : null;

// Handle Image Upload
$profile_pic = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileSize = $_FILES['profile_pic']['size'];
    $fileType = $_FILES['profile_pic']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Sanitized file name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // Allowed extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Directory where uploaded images will be saved
        $uploadFileDir = __DIR__ . '/../../uploads/profile_pics/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $profile_pic = 'uploads/profile_pics/' . $newFileName;
        } else {
            sendResponse("error", "There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.", null, 500);
        }
    } else {
        sendResponse("error", "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions), null, 400);
    }
}

try {
    // Start building the query
    $query = "UPDATE users SET ";
    $params = [];
    $updates = [];

    if ($name) {
        $updates[] = "name = ?";
        $params[] = $name;
    }
    if ($phone) {
        $updates[] = "phone = ?";
        $params[] = $phone;
    }
    if ($dob) {
        $updates[] = "dob = ?";
        $params[] = $dob;
    }
    if ($education) {
        $updates[] = "education = ?";
        $params[] = $education;
    }
    if ($skills) {
        $updates[] = "skills = ?";
        $params[] = $skills;
    }
    if ($gender) {
        $updates[] = "gender = ?";
        $params[] = $gender;
    }
    if ($bio) {
        $updates[] = "bio = ?";
        $params[] = $bio;
    }
    if ($profile_pic) {
        $updates[] = "profile_pic = ?";
        $params[] = $profile_pic;
    }

    // New Privacy Settings
    if (isset($_POST['profile_visible'])) {
        $updates[] = "profile_visible = ?";
        $params[] = $_POST['profile_visible'] == 'true' || $_POST['profile_visible'] == '1' ? 1 : 0;
    }
    if (isset($_POST['email_visible'])) {
        $updates[] = "email_visible = ?";
        $params[] = $_POST['email_visible'] == 'true' || $_POST['email_visible'] == '1' ? 1 : 0;
    }
    if (isset($_POST['phone_visible'])) {
        $updates[] = "phone_visible = ?";
        $params[] = $_POST['phone_visible'] == 'true' || $_POST['phone_visible'] == '1' ? 1 : 0;
    }

    if (empty($updates)) {
        sendResponse("error", "No fields to update", null, 400);
    }

    $query .= implode(", ", $updates);
    $query .= " WHERE id = ?";
    $params[] = $user_id;

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    // Fetch updated user data to return
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $updatedUser = $stmt->fetch();

    sendResponse("success", "Profile updated successfully", $updatedUser);

} catch (PDOException $e) {
    sendResponse("error", "Database error: " . $e->getMessage(), null, 500);
}
?>
