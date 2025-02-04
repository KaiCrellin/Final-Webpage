CREATE DATABASE IF NOT EXISTS db CHARACTER SET utf8 COLLATE utf8_general_ci;

USE db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_logged_in TIMESTAMP,
    UNIQUE KEY unique_name(name)
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    tutor_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE 
);

CREATE TABLE IF NOT EXISTS students_courses (
    student_id INT,
    course_id INT,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100),
    token VARCHAR(64) NOT NULL,
    expire INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expire INT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Inserting example user values to use in code
INSERT INTO users (name, email, password) VALUES
('Alice', 'alice@example.com', 'pass'),
('Bob', 'bob@example.com', 'password2'),
('Charlie', 'charlie@example.com', 'password3'),
('David', 'david@example.com', 'password4'),
('Eve', 'eve@example.com', 'password5');

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
INSERT INTO courses (name, description, tutor_id) VALUES
('Math', 'Math Course', 1),
('Science', 'Science Course', 2);

-- Inserting example students_courses values to use in code
INSERT INTO students_courses (student_id, course_id) VALUES
(1, 1),
(2, 2);