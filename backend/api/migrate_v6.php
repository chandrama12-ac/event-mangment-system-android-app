<?php
require_once 'db_config.php';

try {
    // Add map-related columns to events table
    $sql = "ALTER TABLE events 
            ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER location,
            ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude,
            ADD COLUMN venue_address TEXT NULL AFTER longitude";

    $conn->exec($sql);
    echo "Migration successful: Added map fields to events table.\n";

    // Update existing events with some dummy coordinates
    // Using a central location for testing
    $updateSql = "UPDATE events SET latitude = 12.9716, longitude = 77.5946, venue_address = 'MG Road, Bangalore, Karnataka' WHERE latitude IS NULL";
    $conn->exec($updateSql);
    echo "Default coordinates updated for existing events.\n";

} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Columns already exist. Skipping migration.\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
?>
