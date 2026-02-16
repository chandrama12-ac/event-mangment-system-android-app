<?php
require_once __DIR__ . '/../config/db.php';

echo "Inserting demo events...\n";

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get an admin ID for created_by
    $adminStmt = $conn->query("SELECT id FROM admins LIMIT 1");
    $adminRow = $adminStmt->fetch(PDO::FETCH_ASSOC);
    $adminId = $adminRow ? $adminRow['id'] : 1;

    $events = [
        [
            'title' => 'Tech Innovation Summit 2026',
            'description' => 'A gathering of tech enthusiasts to discuss future trends in AI and Robotics.',
            'event_date' => '2026-05-15 09:00:00',
            'location' => 'Silicon Valley Convention Center',
            'image_url' => 'https://images.unsplash.com/photo-1540575861501-7ad0582371f1?auto=format&fit=crop&w=800&q=80',
            'category' => 'Technology'
        ],
        [
            'title' => 'Global Music Festival',
            'description' => 'Experience a diverse range of musical performances from around the world.',
            'event_date' => '2026-06-20 18:00:00',
            'location' => 'Central Park, New York',
            'image_url' => 'https://images.unsplash.com/photo-1459749411177-042180ce673b?auto=format&fit=crop&w=800&q=80',
            'category' => 'Entertainment'
        ],
        [
            'title' => 'Fitness & Wellness Expo',
            'description' => 'Discover the latest in health, nutrition, and fitness technology.',
            'event_date' => '2026-07-10 10:00:00',
            'location' => 'Exhibition Hall, London',
            'image_url' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=800&q=80',
            'category' => 'Health'
        ],
        [
            'title' => 'Startup Pitch Night',
            'description' => 'Watch aspiring entrepreneurs pitch their ideas to a panel of investors.',
            'event_date' => '2026-08-05 19:00:00',
            'location' => 'Innovation Hub, Bangalore',
            'image_url' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=800&q=80',
            'category' => 'Business'
        ],
        [
            'title' => 'Art & Culture Exhibition',
            'description' => 'A showcase of contemporary art and cultural heritage from emerging artists.',
            'event_date' => '2026-09-12 11:00:00',
            'location' => 'National Art Gallery, Paris',
            'image_url' => 'https://images.unsplash.com/photo-1460661419201-fd4cecea8f82?auto=format&fit=crop&w=800&q=80',
            'category' => 'Arts'
        ]
    ];

    $insertQuery = "INSERT INTO events (title, slug, description, event_date, location, image_url, event_type, created_by, status, is_active) 
                    VALUES (:title, :slug, :description, :event_date, :location, :image_url, :event_type, :created_by, 'published', 1)";
    $stmt = $conn->prepare($insertQuery);

    foreach ($events as $event) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $event['title']))) . '-' . time();
        $stmt->execute([
            ':title' => $event['title'],
            ':slug' => $slug,
            ':description' => $event['description'],
            ':event_date' => $event['event_date'],
            ':location' => $event['location'],
            ':image_url' => $event['image_url'],
            ':event_type' => $event['category'],
            ':created_by' => $adminId
        ]);
        echo "Inserted: " . $event['title'] . "\n";
    }

    echo "Demo events insertion completed successfully.";

} catch (PDOException $e) {
    echo "Insertion failed: " . $e->getMessage();
}
?>
