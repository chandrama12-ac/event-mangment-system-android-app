-- Default Notifications for Campus Connect
INSERT INTO notifications (user_id, title, message, notification_type, priority, is_read, created_at)
SELECT * FROM (
    SELECT NULL, 'Welcome to Event Management App 🎉', 'Start exploring the upcoming educational events on your campus.', 'system', 'high', 0, CURRENT_TIMESTAMP UNION ALL
    SELECT NULL, 'New Workshop Available', 'Register for the AI & Machine Learning Workshop happening next month.', 'event', 'medium', 0, CURRENT_TIMESTAMP UNION ALL
    SELECT NULL, 'Placement Training Open', 'Career guidance for technical roles is now accepting registrations.', 'system', 'high', 0, CURRENT_TIMESTAMP UNION ALL
    SELECT NULL, 'Coding Contest Registration Started', 'Show off your skills in the National Coding Contest!', 'event', 'medium', 0, CURRENT_TIMESTAMP UNION ALL
    SELECT NULL, 'Profile Completion Reminder', 'Complete your profile to get personalized event recommendations.', 'system', 'medium', 0, CURRENT_TIMESTAMP UNION ALL
    SELECT NULL, 'New Certificate Program', 'Enroll in the Oracle Java Certification bootcamp today.', 'system', 'medium', 0, CURRENT_TIMESTAMP
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM notifications LIMIT 1
);
