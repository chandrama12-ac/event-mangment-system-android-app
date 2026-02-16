<?php
include_once 'db_config.php';

echo "Testing Login API...\n";

function testLogin($email, $password) {
    global $host, $port, $db_name;
    $url = "http://localhost/event_mangment/backend/api/login.php"; // This might not work via CLI if server not running
    
    // Testing via direct inclusion and simulation
    $_POST['email'] = $email;
    $_POST['password'] = $password;
    
    // Capture output
    ob_start();
    include 'login.php';
    $output = ob_get_clean();
    
    echo "Login result for $email: $output\n\n";
}

// Test Admin
testLogin("chandramakumar2004@gmail.com", "chan@123");

// Test Non-existent User
testLogin("test@example.com", "pass123");
?>
