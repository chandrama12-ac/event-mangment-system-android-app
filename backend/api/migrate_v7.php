<?php
require_once 'db_config.php';

try {
    // Check if columns exist before adding them
    $columns = [
        'category' => "VARCHAR(100) DEFAULT 'General'",
        'speaker_name' => "VARCHAR(255) DEFAULT NULL",
        'is_trending' => "TINYINT(1) DEFAULT 0",
        'college_name' => "VARCHAR(255) DEFAULT NULL",
        'rating' => "DECIMAL(3,1) DEFAULT 4.5"
    ];

    foreach ($columns as $column => $definition) {
        $stmt = $conn->query("SHOW COLUMNS FROM events LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $conn->exec("ALTER TABLE events ADD COLUMN $column $definition");
            echo "Added column: $column\n";
        } else {
            echo "Column $column already exists\n";
        }
    }

    // Update some events to be trending for demo purposes
    $conn->exec("UPDATE events SET is_trending = 1 LIMIT 5");
    
    // Set some random categories if they are empty
    $categories = ['Tech', 'Cultural', 'Sports', 'Workshop', 'Music'];
    foreach ($categories as $index => $cat) {
        $stmt = $conn->prepare("UPDATE events SET category = ? WHERE id % 5 = ?");
        $stmt->execute([$cat, $index]);
    }

    echo "Migration v7 (Search & Trending) completed successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
