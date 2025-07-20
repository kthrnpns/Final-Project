-- =============================================
-- Database Schema for Virlanie Foundation System
-- Complete version with all tables and relationships
-- Now with enhanced role management and auto-admin assignment
-- =============================================

-- Users table with enhanced role management
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user','volunteer','staff') DEFAULT 'user' NOT NULL,
    status ENUM('active','inactive','pending') DEFAULT 'pending' NOT NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Workshops table with enhanced fields
CREATE TABLE IF NOT EXISTS workshops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(255),
    date DATE NOT NULL,
    time TIME NOT NULL,
    end_time TIME,
    location VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    image_url VARCHAR(255),
    category ENUM('education','arts','sports','life_skills','vocational') DEFAULT 'education',
    status ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_date (date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Workshop registrations with attendance tracking
CREATE TABLE IF NOT EXISTS workshop_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workshop_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attended BOOLEAN DEFAULT FALSE,
    attendance_marked_at TIMESTAMP NULL,
    feedback TEXT,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (workshop_id, user_id),
    INDEX idx_attended (attended)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- System logs for admin actions
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Triggers for Auto-Admin Assignment
-- =============================================

DELIMITER //

-- Trigger to auto-assign admin role for @virlanie.org emails
CREATE TRIGGER before_user_insert
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.email LIKE '%@virlanie.org' THEN
        SET NEW.role = 'admin';
        SET NEW.status = 'active';
    END IF;
END//

-- Trigger to auto-approve volunteers
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.role = 'volunteer' THEN
        UPDATE users SET status = 'active' WHERE id = NEW.id;
    END IF;
END//

DELIMITER ;

-- =============================================
-- Stored Procedures for User Management
-- =============================================

DELIMITER //

-- Procedure to create first admin user
CREATE PROCEDURE CreateFirstAdmin()
BEGIN
    DECLARE user_count INT;
    SELECT COUNT(*) INTO user_count FROM users;
    
    IF user_count = 0 THEN
        INSERT INTO users (name, email, password, role, status)
        VALUES (
            'System Admin',
            'admin@virlanie.org',
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
            'admin',
            'active'
        );
    END IF;
END //

-- Procedure to approve pending users
CREATE PROCEDURE ApproveUser(IN user_email VARCHAR(100))
BEGIN
    UPDATE users SET status = 'active' WHERE email = user_email AND status = 'pending';
END //

DELIMITER ;

-- =============================================
-- Initial Data Setup
-- =============================================

CALL CreateFirstAdmin();
DROP PROCEDURE IF EXISTS CreateFirstAdmin;

-- =============================================
-- Index Optimization
-- =============================================

CREATE INDEX idx_user_role_status ON users(role, status);
CREATE INDEX idx_workshop_category_status ON workshops(category, status);
CREATE INDEX idx_registrations_workshop_user ON workshop_registrations(workshop_id, user_id);
CREATE INDEX idx_users_email_role ON users(email, role);