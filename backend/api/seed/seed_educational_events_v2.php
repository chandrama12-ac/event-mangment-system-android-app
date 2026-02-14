<?php
require_once __DIR__ . '/../db_config.php';

try {
    // 1. Delete existing seed events (optional, but good for clean seed)
    // $conn->exec("DELETE FROM events WHERE event_type = 'education'");

    $educationalEvents = [
        ['AI & Machine Learning Workshop', 'Deep dive into neural networks and predictive modeling.', '2026-03-10 10:00:00', 'Tech Lab 101', 'https://images.unsplash.com/photo-1591453089816-0fbb971fb915?w=800'],
        ['Modern Web Dev Seminar', 'Exploring React, Flutter, and the future of web.', '2026-03-15 14:00:00', 'Main Auditorium', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800'],
        ['National Coding Contest', 'The biggest competitive programming event on campus.', '2026-03-20 09:00:00', 'Computer Center', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800'],
        ['24-Hour Campus Hackathon', 'Build innovative solutions to real-world problems.', '2026-04-05 18:00:00', 'Innovation Hall', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800'],
        ['Placement Prep: Top Tech', 'Hiring event and prep for top tier tech roles.', '2026-04-12 09:00:00', 'Block A', 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=800'],
        ['Cyber Security Guidance', 'Learn about PenTesting and security research.', '2026-04-18 11:00:00', 'Seminar Hall 2', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=800'],
        ['AWS Cloud Training', 'Hands-on training for AWS certification.', '2026-05-02 10:00:00', 'IT Lab 4', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800'],
        ['Dr. Satya Nadella Lecture', 'Remote session on AI and society.', '2026-05-10 16:00:00', 'Virtual Theater', 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800'],
        ['Ethical Hacking Webinar', 'Intro to network security and vulnerabilities.', '2026-05-15 19:00:00', 'Zoom', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800'],
        ['Java Certification Prep', '5-day bootcamp for Java SE certification.', '2026-06-01 09:00:00', 'Training Room', 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=800'],
        ['Digital Marketing 101', 'SEO, SEM, and social media branding.', '2026-06-10 14:00:00', 'Business Auditorium', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800'],
        ['Data Science Expo 2026', 'Showcase of final year projects and papers.', '2026-06-15 10:00:00', 'Campus Gardens', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800'],
        ['Blockchain & Web3 Summit', 'Understanding DeFi and smart contracts.', '2026-06-25 11:00:00', 'Finance Center', 'https://images.unsplash.com/photo-1639762681485-074b7f938ba0?w=800'],
        ['Soft Skills Workshop', 'Communication for future engineers.', '2026-07-02 10:00:00', 'Creative Block', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800'],
        ['Python for Finance', 'Automating financial predictive analysis.', '2026-07-10 15:00:00', 'Finance Lab', 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800'],
        ['Mobile App Design UI/UX', 'Designing intuitive apps with Figma.', '2026-07-15 13:00:00', 'Design Studio', 'https://images.unsplash.com/photo-1586717791821-3f44a563eb4c?w=800'],
        ['Public Speaking Masterclass', 'Master the art of presenting to large crowds.', '2026-07-20 11:00:00', 'Little Theater', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=800'],
        ['IoT Infrastructure Seminar', 'Smart cities and connected devices.', '2026-08-05 10:00:00', 'Robotics Lab', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800'],
        ['Entrepreneurship Bootcamp', 'Launching your own tech startup.', '2026-08-12 09:00:00', 'Innovation Hub', 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800'],
        ['Graphic Design Fundamentals', 'Learn Adobe Suite and core principles.', '2026-08-20 14:00:00', 'Multi-media Suite', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800'],
    ];

    $stmt = $conn->prepare("INSERT INTO events (title, slug, description, event_date, location, image_url, event_type, status, created_by) VALUES (?, ?, ?, ?, ?, ?, 'education', 'published', 1)");

    foreach ($educationalEvents as $event) {
        $slug = strtolower(str_replace(' ', '-', $event[0])) . '-' . time();
        $stmt->execute([$event[0], $slug, $event[1], $event[2], $event[3], $event[4]]);
    }

    sendResponse("success", "20 educational events seeded successfully");
} catch (PDOException $e) {
    sendResponse("error", "Seeding failed: " . $e->getMessage(), null, 500);
}
?>
