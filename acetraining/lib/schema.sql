CREATE DATABASE IF NOT EXISTS db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db;

-- Base tables
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_logged_in TIMESTAMP
);

CREATE TABLE tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    tutor_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id)
);

CREATE TABLE course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    UNIQUE KEY unique_enrollment (course_id, student_id)
);

-- Information display tables
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    due_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    quiz_name VARCHAR(100) NOT NULL,
    quiz_description TEXT,
    quiz_date TIMESTAMP,
    quiz_time TIME,
    duration INT,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    class_name VARCHAR(100) NOT NULL,
    class_description TEXT,
    class_date DATE NOT NULL,
    class_time TIME NOT NULL,
    duration INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Insert test users.
INSERT INTO users (name, email, password) VALUES
('John Tutor', 'john@test.com', 'test123'),
('Jane Tutor', 'jane@test.com', 'test123'),
('Mike Student', 'mike@test.com', 'test123'),
('Sarah Student', 'sarah@test.com', 'test123');

-- Assign roles
INSERT INTO tutors (user_id) 
SELECT id FROM users WHERE email IN ('john@test.com', 'jane@test.com');

INSERT INTO students (user_id)
SELECT id FROM users WHERE email IN ('mike@test.com', 'sarah@test.com');

-- Add courses
INSERT INTO courses (name, description, tutor_id) VALUES
('Mathematics 101', 'Introduction to Mathematics', 1),
('Physics 101', 'Introduction to Physics', 2);

-- Enroll students
INSERT INTO course_enrollments (course_id, student_id) VALUES
(1, 1), -- Mike in Mathematics
(1, 2), -- Sarah in Mathematics
(2, 1), -- Mike in Physics
(2, 2); -- Sarah in Physics

-- Add display information
INSERT INTO assignments (course_id, name, description, due_date) VALUES
(1, 'Linear Algebra Basics', 'Chapter 1-3 Review', CURRENT_DATE + INTERVAL 7 DAY),
(2, 'Classical Mechanics', 'Newtons Laws', CURRENT_DATE + INTERVAL 14 DAY);

INSERT INTO quizzes (course_id, quiz_name, quiz_description, quiz_date, quiz_time, duration) VALUES
(1, 'Algebra Quiz', 'Basic algebraic concepts', CURRENT_DATE + INTERVAL 5 DAY, '10:00:00', 60),
(2, 'Physics Quiz', 'Mechanics fundamentals', CURRENT_DATE + INTERVAL 10 DAY, '14:00:00', 90);

INSERT INTO classes (course_id, class_name, class_description, class_date, class_time, duration) VALUES
(1, 'Algebra Fundamentals', 'Introduction to basic concepts', CURRENT_DATE + INTERVAL 1 DAY, '09:00:00', 120),
(2, 'Physics Basics', 'Introduction to mechanics', CURRENT_DATE + INTERVAL 2 DAY, '14:00:00', 120);