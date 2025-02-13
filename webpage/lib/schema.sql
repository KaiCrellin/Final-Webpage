-- This file contains the SQL schema for the database
CREATE DATABASE IF NOT EXISTS db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE db;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_logged_in TIMESTAMP,
    UNIQUE KEY unique_name(name)
);

-- Table for students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for tutors
CREATE TABLE IF NOT EXISTS tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for courses
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    tutor_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE
  
);

-- Table for students and courses relationship
CREATE TABLE IF NOT EXISTS students_courses (
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Table for password resets
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100),
    token VARCHAR(64) NOT NULL,
    expire INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table for sessions
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expire INT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for assignments
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    due_date TIMESTAMP,
    file_path VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Table for submissions
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_path VARCHAR(100) NOT NULL,
    grade INT,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Table for calendar events
CREATE TABLE IF NOT EXISTS calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    event_description TEXT,
    event_date TIMESTAMP,
    event_time TIME,
    duration INT,
    tutor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE
);

-- Table for downloadable and uploadable resources
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    tutor_id INT NOT NULL,
    file_name VARCHAR(100) NOT NULL,
    file_path VARCHAR(100) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE
);

-- Inserting example user values to use in code
INSERT INTO users (name, email, password) VALUES
('Alice', 'alice@example.com', 'pass'),
('Bob', 'bob@example.com', 'pass'),
('Charlie', 'charlie@example.com', 'pass'),
('David', 'david@example.com', 'pass'),
('Eve', 'eve@example.com', 'pass');

-- Inserting example tutor values to use in code
INSERT INTO tutors (user_id) VALUES
(1),
(2);

-- Inserting example student values to use in code
INSERT INTO students (user_id) VALUES
(3),
(4);

-- Inserting example admin values to use in code
INSERT INTO admins (user_id) VALUES
(5);

-- Inserting example course values to use in code
INSERT INTO courses (name, description, tutor_id, courses_id) VALUES
('Math', 'Math Course', 1,1),
('Science', 'Science Course', 2,2);

INSERT INTO students_courses (student_id, course_id) VALUES
(1, 1),
(2, 2);

-- Inserting example assignments values to use in code
INSERT INTO assignments (course_id, name, description, due_date) VALUES
(1, 'Advanced Topology', 'Math Assignments', '2025-05-31 23:59:59'),
(2, 'Theory Of Quantum Tunneling', 'Science Assignment', '2025-04-30 23:59:59');

-- Inserting example calendar events values to use in code
INSERT INTO calendar (course_id, event_name, event_description, event_date, event_time, duration, tutor_id) VALUES
(1, 'Topology Lecture', 'Topology Lecture', '2025-01-31', '10:00:00', 60, 1),
(2, 'Quantum Tunneling Lecture', 'Quantum Tunneling Lecture', '2025-01-31', '10:00:00', 60, 2);