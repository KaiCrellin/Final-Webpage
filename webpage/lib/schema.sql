-- PURPOSE: this file contains the SQL schema for the database
CREATE DATABASE IF NOT EXISTS my_database CHARACTER SET utf8 COLLATE utf8_general_ci;

USE my_database;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('student', 'tutor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_logged_in TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

CREATE TABLE IF NOT EXISTS tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE 
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    tutor_id INT,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE 
);

CREATE TABLE IF NOT EXISTS students_courses (
    student_id INT,
    course_id INT,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ,
    FOREIGN KEY (course_id) REFERENCES courses(id) 
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100),
    token VARCHAR(64) NOT NULL,
    expires INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ,
    FOREIGN KEY (email) REFERENCES users(email) 
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires INT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) 
);

-- Inserting example user values to use in code
INSERT INTO users (name, email, password, role) VALUES
('Alice', 'alice@example.com', 'Hashedpassword1', 'student'),
('Bob', 'bob@example.com', 'Hashedpassword2', 'student'),
('Charlie', 'charlie@example.com', 'Hashedpassword3', 'tutor'),
('David', 'david@example.com', 'Hashedpassword4', 'tutor'),
('Eve', 'eve@example.com', 'Hashedpassword5', 'admin');

-- Inserting example student values to use in code
INSERT INTO students (user_id) VALUES
(1),
(2);

-- Inserting example tutor values to use in code
INSERT INTO tutors (user_id) VALUES
(3),
(4);

-- Inserting example course values to use in code
INSERT INTO courses (name, description, tutor_id) VALUES
('Math', 'Math Course', 3),
('Science', 'Science Course', 4);

-- Inserting example students_courses values to use in code
INSERT INTO students_courses (student_id, course_id) VALUES
(1, 1),
(2, 2);