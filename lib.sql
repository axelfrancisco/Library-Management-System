-- ============================================================
-- NEU Library Management System — Database Setup
-- Import this into your existing InfinityFree database:
--   if0_41422333_neulibrary
-- DO NOT run CREATE DATABASE or USE — not allowed on shared hosting
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    role_name VARCHAR(50) DEFAULT 'Student Member',
    student_id VARCHAR(30) UNIQUE,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) DEFAULT '',
    course VARCHAR(100),
    year_level VARCHAR(50),
    contact_number VARCHAR(20),
    address VARCHAR(255),
    profile_image TEXT,
    google_sub VARCHAR(255) UNIQUE NULL,
    college VARCHAR(150) NULL,
    user_category VARCHAR(100) NULL,
    is_employee TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_roles (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_key ENUM('user', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_role (user_id, role_key),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blocked_users (
    block_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blocked_by_user_id INT NULL,
    reason VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_blocked_users_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_blocked_users_admin FOREIGN KEY (blocked_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS visitor_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    google_email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    selected_role ENUM('user', 'admin') DEFAULT 'user',
    user_category VARCHAR(100) NULL,
    reason VARCHAR(150) NULL,
    college VARCHAR(150) NULL,
    is_employee TINYINT(1) DEFAULT 0,
    status ENUM('allowed', 'blocked') DEFAULT 'allowed',
    blocked_reason VARCHAR(255) NULL,
    login_at DATETIME NOT NULL,
    logout_at DATETIME NULL,
    CONSTRAINT fk_visitor_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    book_code VARCHAR(30) UNIQUE NOT NULL,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    cover_image TEXT,
    status ENUM('available', 'borrowed', 'reserved', 'overdue', 'new_arrival') DEFAULT 'available',
    is_new_arrival TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS liked_books (
    liked_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS borrowed_books (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE,
    return_status ENUM('not_returned', 'returned') DEFAULT 'not_returned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS returned_books (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    return_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_id) REFERENCES borrowed_books(borrow_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reserved_books (
    reserve_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reserve_date DATE NOT NULL,
    reserve_status ENUM('active', 'cancelled', 'claimed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS fines (
    fine_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    days_late INT DEFAULT 0,
    amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_id) REFERENCES borrowed_books(borrow_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- ============================================================
-- Seed users
-- Admin password: Admin2026!
-- (hashed with bcrypt via PHP password_hash)
-- ============================================================
INSERT INTO users (full_name, role_name, student_id, email, password, course, year_level, contact_number, address, college, user_category, is_employee)
VALUES
(
    'Axel Dela Cruz',
    'Student Member',
    '2026-00124',
    'axel@email.com',
    '',
    'BS Information Technology',
    '2nd Year',
    '09123456789',
    'Quezon City, Philippines',
    'College of Computer Studies',
    'Student',
    0
),
(
    'Prof. J.C. Esperanza',
    'Admin',
    NULL,
    'jcesperanza@neu.edu.ph',
    '$2b$12$qxij1N8cEuuE.b7GyMO9WOCOS1nHqx7eNqG20i1IKgudTpXmSsmae',
    NULL,
    NULL,
    NULL,
    NULL,
    'NEU',
    'Employee',
    1
)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- Assign roles
INSERT IGNORE INTO user_roles (user_id, role_key)
SELECT user_id, 'user' FROM users WHERE email IN ('axel@email.com', 'jcesperanza@neu.edu.ph');

INSERT IGNORE INTO user_roles (user_id, role_key)
SELECT user_id, 'admin' FROM users WHERE email = 'jcesperanza@neu.edu.ph';

-- ============================================================
-- Seed books
-- ============================================================
INSERT INTO books (book_code, title, author, category, cover_image, status, is_new_arrival) VALUES
('3011', 'Atomic Habits',       'James Clear',    'Self Help', 'https://covers.openlibrary.org/b/id/10521270-L.jpg', 'available',   0),
('3012', 'The Alchemist',       'Paulo Coelho',   'Fiction',   'https://covers.openlibrary.org/b/id/8231996-L.jpg',  'available',   0),
('3013', 'The Hobbit',          'J.R.R Tolkien',  'Fantasy',   'https://covers.openlibrary.org/b/id/6979861-L.jpg',  'available',   0),
('6011', 'It Ends With Us',     'Colleen Hoover', 'Romance',   'https://covers.openlibrary.org/b/id/11153254-L.jpg', 'new_arrival', 1),
('6012', 'The Midnight Library','Matt Haig',      'Fiction',   'https://covers.openlibrary.org/b/id/10594765-L.jpg', 'new_arrival', 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);