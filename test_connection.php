<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$configs = [
    ['host' => 'localhost', 'port' => '3306'],
    ['host' => '127.0.0.1', 'port' => '3306'],
    ['host' => 'localhost', 'port' => '3307'],
    ['host' => '127.0.0.1', 'port' => '3307'],
];

$db_name = 'event_management';
$user = 'root';
$pass = '';

foreach ($configs as $config) {
    echo "Testing Host: {$config['host']}, Port: {$config['port']}... ";
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname=$db_name";
        $conn = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "SUCCESS!\n";
        exit(0);
    } catch (PDOException $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "\nAll connection attempts failed.\n";
exit(1);
?>
