<?php
require_once 'db_config.php';

try {
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("TRUNCATE TABLE events"); 
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    $adminStmt = $conn->query("SELECT id FROM admins LIMIT 1");
    $adminId = ($adminStmt->fetch(PDO::FETCH_ASSOC))['id'] ?? 1;

    $categories = ['Technology', 'Education', 'Cultural', 'Sports', 'Workshop', 'Business', 'Arts', 'Health'];
    $educational_topics = [
        'Advanced Mathematics', 'Physics of Tomorrow', 'Chemical Engineering Basics', 'World History Deep Dive',
        'Economic Theories', 'Literature Analysis', 'Programming in Python', 'Data Science Fundamentals',
        'Cybersecurity Essentials', 'Blockchain Technology', 'Renewable Energy Systems', 'Environmental Science',
        'Psychology of Success', 'Sociology Trends', 'Philosophy and Logic', 'Archaeology Discoveries',
        'Astronomy Workshop', 'Robotics for Beginners', 'Genetics Research', 'Neuroscience Updates'
    ];

    echo "Inserting 20 Demo Events...\n";
    for ($i = 1; $i <= 20; $i++) {
        $title = "Demo Event #$i: " . $categories[$i % count($categories)];
        $slug = "demo-evt-" . bin2hex(random_bytes(4)) . "-" . $i;
        $stmt = $conn->prepare("INSERT INTO events (title, slug, description, event_date, location, image_url, event_type, category, created_by, status, is_trending, is_active) 
                                VALUES (:title, :slug, :description, :date, :loc, :img, :type, :cat, :by, 'published', :trending, 1)");
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':description' => "This is a comprehensive demo event for " . $categories[$i % count($categories)] . ".",
            ':date' => date('Y-m-d H:i:s', strtotime("+" . ($i * 2) . " days")),
            ':loc' => "Venue Location #$i",
            ':img' => 'https://images.unsplash.com/photo-1540575861501-7ad0582371f1?w=800',
            ':type' => $categories[$i % count($categories)],
            ':cat' => $categories[$i % count($categories)],
            ':by' => $adminId,
            ':trending' => ($i % 5 == 0) ? 1 : 0
        ]);
    }

    echo "Inserting 100 Educational Events...\n";
    for ($i = 1; $i <= 100; $i++) {
        $topic = $educational_topics[$i % count($educational_topics)];
        $title = "Educational Seminar: $topic ($i)";
        $slug = "edu-evt-" . bin2hex(random_bytes(4)) . "-" . $i;
        $stmt = $conn->prepare("INSERT INTO events (title, slug, description, event_date, location, image_url, event_type, category, created_by, status, is_active) 
                                VALUES (:title, :slug, :description, :date, :loc, :img, :type, 'Education', :by, 'published', 1)");
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':description' => "Deep dive into $topic.",
            ':date' => date('Y-m-d H:i:s', strtotime("+" . (10 + $i) . " days")),
            ':loc' => "University Hall #" . ($i % 10 + 1),
            ':img' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800',
            ':type' => 'Workshop',
            ':by' => $adminId
        ]);
    }

    echo "Seeding completed successfully!";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage();
}
?>
