-- =====================================================
-- SECURE Event Management System - Database Setup
-- =====================================================
-- Enterprise-Grade Security Implementation
-- Version: 3.0 (Production Ready)
-- Features: Advanced Authentication, Audit Logging, Rate Limiting
-- =====================================================

-- Create and use database
CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

-- Migration/Sync: Ensure consistent column names for existing tables
-- If users table exists but has 'password' instead of 'password_hash'
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'password';
SET @newcolumnname = 'password_hash';

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname 
     AND TABLE_NAME = @tablename 
     AND COLUMN_NAME = @columnname) > 0,
  CONCAT('ALTER TABLE ', @tablename, ' CHANGE ', @columnname, ' ', @newcolumnname, ' VARCHAR(255) NOT NULL'),
  'SELECT "Column already exists or table missing"'
));

PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- If admins table exists but has 'password' instead of 'password_hash'
SET @tablename = 'admins';
SET @columnname = 'password';
SET @newcolumnname = 'password_hash';

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname 
     AND TABLE_NAME = @tablename 
     AND COLUMN_NAME = @columnname) > 0,
  CONCAT('ALTER TABLE ', @tablename, ' CHANGE ', @columnname, ' ', @newcolumnname, ' VARCHAR(255) NOT NULL'),
  'SELECT "Column already exists or table missing"'
));

PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- CORE TABLES WITH ENHANCED SECURITY
-- =====================================================

-- Users table (for students/general users)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    
    -- Profile information
    profile_pic VARCHAR(255) DEFAULT NULL,
    dob DATE DEFAULT NULL,
    education VARCHAR(255) DEFAULT NULL,
    skills TEXT DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL UNIQUE,
    
    -- Security fields
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    failed_login_attempts INT DEFAULT 0,
    last_login TIMESTAMP NULL,
    last_login_ip VARCHAR(45) DEFAULT NULL,
    account_locked_until TIMESTAMP NULL,
    password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    must_change_password BOOLEAN DEFAULT FALSE,
    
    -- Two-Factor Authentication
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(100) DEFAULT NULL,
    backup_codes TEXT DEFAULT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_uuid (uuid),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_active_verified (is_active, is_verified),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table (separate secure admin authentication)
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    admin_username VARCHAR(50) NOT NULL UNIQUE,
    admin_email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    
    -- Advanced security
    secret_key VARCHAR(100) NOT NULL UNIQUE,
    two_factor_enabled BOOLEAN DEFAULT TRUE,
    two_factor_secret VARCHAR(100) DEFAULT NULL,
    backup_codes TEXT DEFAULT NULL,
    
    -- Status and security
    is_active BOOLEAN DEFAULT TRUE,
    failed_login_attempts INT DEFAULT 0,
    last_login TIMESTAMP NULL,
    last_login_ip VARCHAR(45) DEFAULT NULL,
    account_locked_until TIMESTAMP NULL,
    password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    must_change_password BOOLEAN DEFAULT FALSE,
    
    -- Permissions and role
    permissions JSON DEFAULT NULL,
    admin_level ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_uuid (uuid),
    INDEX idx_username (admin_username),
    INDEX idx_email (admin_email),
    INDEX idx_secret_key (secret_key),
    INDEX idx_admin_level (admin_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin sessions table (secure session management)
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    refresh_token VARCHAR(255) DEFAULT NULL UNIQUE,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    device_fingerprint VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_admin_active (admin_id, is_active),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User sessions table
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    refresh_token VARCHAR(255) DEFAULT NULL UNIQUE,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    device_fingerprint VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_user_active (user_id, is_active),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- OTP Codes table (for email verification)
CREATE TABLE IF NOT EXISTS otp_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    code VARCHAR(6) NOT NULL,
    purpose ENUM('email_verification', 'password_reset', 'login_2fa', 'phone_verification') NOT NULL,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_used BOOLEAN DEFAULT FALSE,
    attempts INT DEFAULT 0,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_otp (user_id, code, purpose),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    action_type ENUM('login', 'register', 'password_reset', 'otp_request', 'api_call') NOT NULL,
    attempts INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    blocked_until TIMESTAMP NULL,
    
    UNIQUE KEY unique_identifier_action (identifier, action_type),
    INDEX idx_blocked (blocked_until),
    INDEX idx_window (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(250) NOT NULL UNIQUE,
    description TEXT,
    event_date DATETIME NOT NULL,
    end_date DATETIME DEFAULT NULL,
    location VARCHAR(255),
    image_url VARCHAR(255),
    capacity INT DEFAULT NULL,
    event_type ENUM('exam', 'workshop', 'seminar', 'competition', 'sports', 'cultural', 'placement', 'social', 'other') DEFAULT 'other',
    status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'published',
    is_active BOOLEAN DEFAULT TRUE,
    requires_approval BOOLEAN DEFAULT FALSE,
    
    -- Metadata
    tags JSON DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    
    -- Tracking
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_uuid (uuid),
    INDEX idx_slug (slug),
    INDEX idx_event_date (event_date),
    INDEX idx_event_type (event_type),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registrations table (student event registrations)
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    attendance_status ENUM('pending', 'present', 'absent') DEFAULT 'pending',
    check_in_time TIMESTAMP NULL,
    check_out_time TIMESTAMP NULL,
    notes TEXT DEFAULT NULL,
    
    -- QR code for check-in
    qr_code VARCHAR(255) DEFAULT NULL,
    
    -- Metadata
    registration_data JSON DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (user_id, event_id),
    INDEX idx_uuid (uuid),
    INDEX idx_user_registrations (user_id),
    INDEX idx_event_registrations (event_id),
    INDEX idx_attendance_status (attendance_status),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    user_id INT,
    title VARCHAR(150),
    message TEXT,
    notification_type ENUM('event', 'registration', 'attendance', 'system', 'security', 'reminder') DEFAULT 'system',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    metadata JSON DEFAULT NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_uuid (uuid),
    INDEX idx_user_notifications (user_id, is_read),
    INDEX idx_priority (priority),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log table (comprehensive security tracking)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
    user_type ENUM('admin', 'user', 'system') NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    severity ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
    metadata JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_uuid (uuid),
    INDEX idx_user_logs (user_type, user_id),
    INDEX idx_action_logs (action),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at),
    INDEX idx_table_record (table_name, record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('admin', 'user') NOT NULL,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_used BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Security events table (track suspicious activities)
CREATE TABLE IF NOT EXISTS security_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('failed_login', 'account_locked', 'suspicious_activity', 'password_change', 'permission_change', 'session_hijack_attempt') NOT NULL,
    user_type ENUM('admin', 'user') NOT NULL,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    details JSON DEFAULT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_event_type (event_type),
    INDEX idx_user (user_type, user_id),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECURITY TRIGGERS
-- =====================================================

-- Trigger to log admin password changes
DELIMITER $$
DROP TRIGGER IF EXISTS after_admin_password_change$$
CREATE TRIGGER after_admin_password_change
AFTER UPDATE ON admins
FOR EACH ROW
BEGIN
    IF OLD.password_hash != NEW.password_hash THEN
        INSERT INTO audit_logs (user_type, user_id, action, table_name, record_id, severity)
        VALUES ('admin', NEW.id, 'password_changed', 'admins', NEW.id, 'warning');
        
        INSERT INTO security_events (event_type, user_type, user_id, severity, details)
        VALUES ('password_change', 'admin', NEW.id, 'medium', JSON_OBJECT('changed_at', NOW()));
    END IF;
END$$
DELIMITER ;

-- Trigger to log user password changes
DELIMITER $$
DROP TRIGGER IF EXISTS after_user_password_change$$
CREATE TRIGGER after_user_password_change
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.password_hash != NEW.password_hash THEN
        INSERT INTO audit_logs (user_type, user_id, action, table_name, record_id, severity)
        VALUES ('user', NEW.id, 'password_changed', 'users', NEW.id, 'warning');
        
        INSERT INTO security_events (event_type, user_type, user_id, severity, details)
        VALUES ('password_change', 'user', NEW.id, 'medium', JSON_OBJECT('changed_at', NOW()));
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- STORED PROCEDURES FOR SECURITY OPERATIONS
-- =====================================================

-- Procedure to clean expired sessions
DELIMITER $$
DROP PROCEDURE IF EXISTS cleanup_expired_sessions$$
CREATE PROCEDURE cleanup_expired_sessions()
BEGIN
    DELETE FROM admin_sessions WHERE expires_at < NOW();
    DELETE FROM user_sessions WHERE expires_at < NOW();
    DELETE FROM otp_codes WHERE expires_at < NOW() OR is_used = TRUE;
    DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR is_used = TRUE;
END$$
DELIMITER ;

-- Procedure to check and unlock accounts
DELIMITER $$
DROP PROCEDURE IF EXISTS unlock_expired_accounts$$
CREATE PROCEDURE unlock_expired_accounts()
BEGIN
    UPDATE admins 
    SET failed_login_attempts = 0, account_locked_until = NULL 
    WHERE account_locked_until < NOW();
    
    UPDATE users 
    SET failed_login_attempts = 0, account_locked_until = NULL 
    WHERE account_locked_until < NOW();
END$$
DELIMITER ;

-- Procedure to archive old audit logs (keep last 90 days)
DELIMITER $$
DROP PROCEDURE IF EXISTS archive_old_logs$$
CREATE PROCEDURE archive_old_logs()
BEGIN
    DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    DELETE FROM security_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) AND is_resolved = TRUE;
END$$
DELIMITER ;

-- =====================================================
-- SECURITY VIEWS
-- =====================================================

CREATE OR REPLACE VIEW active_events_view AS
SELECT 
    e.id,
    e.uuid,
    e.title,
    e.slug,
    e.event_type,
    e.description,
    e.event_date,
    e.end_date,
    e.location,
    e.image_url,
    e.capacity,
    e.status,
    COUNT(r.id) as registered_count,
    GREATEST(0, e.capacity - COUNT(r.id)) as available_slots,
    a.full_name as created_by_name
FROM events e
LEFT JOIN registrations r ON e.id = r.event_id AND r.status != 'cancelled'
LEFT JOIN admins a ON e.created_by = a.id
WHERE e.is_active = TRUE AND e.status = 'published' AND e.event_date > NOW()
GROUP BY e.id;

CREATE OR REPLACE VIEW student_attendance_summary AS
SELECT 
    u.id,
    u.uuid,
    u.name,
    u.email,
    COUNT(r.id) as total_registered,
    SUM(CASE WHEN r.attendance_status = 'present' THEN 1 ELSE 0 END) as attended,
    SUM(CASE WHEN r.attendance_status = 'absent' THEN 1 ELSE 0 END) as missed,
    SUM(CASE WHEN r.attendance_status = 'pending' THEN 1 ELSE 0 END) as pending,
    ROUND((SUM(CASE WHEN r.attendance_status = 'present' THEN 1 ELSE 0 END) / NULLIF(COUNT(r.id), 0)) * 100, 2) as attendance_percentage
FROM users u
LEFT JOIN registrations r ON u.id = r.user_id
WHERE u.role = 'user' AND u.is_active = TRUE AND u.deleted_at IS NULL
GROUP BY u.id;

CREATE OR REPLACE VIEW event_statistics AS
SELECT 
    event_type,
    COUNT(*) as total_events,
    SUM(capacity) as total_capacity,
    COUNT(CASE WHEN event_date > NOW() THEN 1 END) as upcoming_events,
    COUNT(CASE WHEN event_date < NOW() THEN 1 END) as past_events,
    AVG(capacity) as avg_capacity
FROM events
WHERE is_active = TRUE AND status = 'published'
GROUP BY event_type;

CREATE OR REPLACE VIEW security_dashboard AS
SELECT 
    DATE(created_at) as date,
    event_type,
    severity,
    COUNT(*) as incident_count
FROM security_events
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at), event_type, severity;

-- =====================================================
-- INDEXES FOR PERFORMANCE (Handled if not exists or via setup refinement)
-- ALTER TABLE audit_logs ADD INDEX idx_created_date (DATE(created_at));
-- ALTER TABLE security_events ADD INDEX idx_created_date (DATE(created_at));

-- =====================================================
-- INITIAL SEED DATA
-- =====================================================

-- Add Default Admin Account
-- Email: chandramakumar2004@gmail.com, Password: chan@123
INSERT IGNORE INTO admins (admin_username, admin_email, password_hash, full_name, secret_key, admin_level) 
VALUES (
    'admin_chandra', 
    'chandramakumar2004@gmail.com', 
    '$2y$12$gpYAJnkjFekrUZC6y8b.7OAolxlACvRfox8jLSprJOq5Zo3sOuE9i', 
    'Chandrama Kumar', 
    'SECURE_KEY_2024_CHANDRA', 
    'super_admin'
);

-- Add Sample Events
SET @admin_id = (SELECT id FROM admins WHERE admin_email = 'chandramakumar2004@gmail.com' LIMIT 1);

INSERT IGNORE INTO events (title, slug, description, event_date, end_date, location, image_url, capacity, event_type, status, created_by)
VALUES 
(
    'Tech Innovation Summit 2024', 
    'tech-innovation-summit-2024', 
    'A premier gathering of tech visionaries and industry leaders to discuss the latest trends in AI, Blockchain, and Sustainable Tech.', 
    DATE_ADD(NOW(), INTERVAL 15 DAY), 
    DATE_ADD(NOW(), INTERVAL 15 DAY) + INTERVAL 8 HOUR, 
    'Main Auditorium, Building A', 
    'https://images.unsplash.com/photo-1540575861501-7ad0582371f1?auto=format&fit=crop&w=800&q=80', 
    500, 
    'seminar', 
    'published', 
    @admin_id
),
(
    'Annual Cultural Fest - Rhythm 2024', 
    'rhythm-2024', 
    'Join us for three days of music, dance, and artistic expression. Featuring performances by celebrity artists and student talents.', 
    DATE_ADD(NOW(), INTERVAL 30 DAY), 
    DATE_ADD(NOW(), INTERVAL 33 DAY), 
    'University Sports Ground', 
    'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80', 
    2000, 
    'cultural', 
    'published', 
    @admin_id
),
(
    'Inter-College Coding Marathon', 
    'coding-marathon-2024', 
    'A 24-hour hackathon where students compete to build innovative solutions for real-world problems. Exciting prizes to be won!', 
    DATE_ADD(NOW(), INTERVAL 10 DAY), 
    DATE_ADD(NOW(), INTERVAL 11 DAY), 
    'Computer Science Lab, IV Floor', 
    'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80', 
    100, 
    'competition', 
    'published', 
    @admin_id
),
(
    'Corporate Placement Workshop', 
    'placement-workshop-2024', 
    'Expert guidance on resume building, interview techniques, and soft skills training to prepare you for the upcoming placement season.', 
    DATE_ADD(NOW(), INTERVAL 5 DAY), 
    DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 4 HOUR, 
    'Conference Hall, Placement Cell', 
    'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=800&q=80', 
    200, 
    'placement', 
    'published', 
    @admin_id
),
(
    'Campus Cleanliness Drive', 
    'clean-campus-2024', 
    'An initiative to make our campus greener and cleaner. Volunteers will be provided with certificates and refreshments.', 
    DATE_ADD(NOW(), INTERVAL 2 DAY), 
    DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 3 HOUR, 
    'Starting Point: University Main Gate', 
    'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=800&q=80', 
    NULL, 
    'social', 
    'published', 
    @admin_id
);

-- =====================================================
-- DATABASE CONFIGURATION
-- =====================================================

-- Set timezone
SET time_zone = '+00:00';

-- Set character set
SET NAMES utf8mb4;

-- =====================================================
-- VERIFICATION QUERY
-- =====================================================

SELECT 
    '✓ Database Setup & Seeding Complete!' as Status,
    '' as '',
    'Security Features Enabled:' as Info,
    '✓ Enhanced Authentication System' as Feature_1,
    '✓ Session Management with Tokens' as Feature_2,
    '✓ Rate Limiting Protection' as Feature_3,
    '✓ Two-Factor Authentication Ready' as Feature_4,
    '✓ Comprehensive Audit Logging' as Feature_5,
    '✓ Security Event Tracking' as Feature_6,
    '✓ Auto-cleanup Procedures' as Feature_7,
    '✓ UUID Support for Public IDs' as Feature_8,
    '' as '',
    'ADMIN CREDENTIALS LOADED:' as Admin_Info,
    'Email: chandramakumar2004@gmail.com' as Admin_Email,
    'Note: Use the password you provided' as Admin_Pass;

-- Show table status
SELECT 
    TABLE_NAME, 
    ENGINE, 
    TABLE_ROWS as Rows,
    AVG_ROW_LENGTH as Avg_Row_Length,
    DATA_LENGTH as Data_Size
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'event_management'
ORDER BY TABLE_NAME;