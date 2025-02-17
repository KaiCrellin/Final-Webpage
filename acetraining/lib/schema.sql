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
    student_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

    -- Table for course_enrollments
CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);



-- Table for classes
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    tutor_id INT NOT NULL,
    student_id INT NOT NULL,
    class_name VARCHAR(100) NOT NULL,
    class_description TEXT,
    class_date TIMESTAMP,
    class_time TIME,
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);


-- Table for password resets
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100),
    token VARCHAR(64) NOT NULL,
    expire INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    index (token)
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
    tutor_id INT NOT NULL,
    student_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    due_date TIMESTAMP,
    file_path VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Table for holding quiz information
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    tutor_id INT NOT NULL,
    student_id INT NOT NULL,
    quiz_name VARCHAR(100) NOT NULL,
    quiz_description TEXT,
    quiz_questions_number TEXT,
    quiz_questions TEXT,
    quiz_question_wrong_answers TEXT,
    quiz_question_correct_answers TEXT,
    quiz_date TIMESTAMP,
    quiz_time TIME,
    duration INT,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
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
('Kai', 'kaicrellin1244@gmail.com', 'pass');

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
INSERT INTO courses (name, description, tutor_id, student_id) VALUES
('Math', 'Math Course', 1, 1),
('Science', 'Science Course', 2, 2);

-- Inserting example course enrollment values to use in code
INSERT INTO course_enrollments (course_id, student_id) VALUES
(1, 1),
(2, 2);

-- Inserting example assignments values to use in code
INSERT INTO assignments (course_id, tutor_id, student_id, name, description, due_date) VALUES
(1, 1, 1, 'Advanced Topology', 'Math Assignments', '2025-05-31 23:59:59'),
(2, 2, 2, 'Theory Of Quantum Tunneling', 'Science Assignment', '2025-04-30 23:59:59');

-- Inserting example calendar events values to use in code
INSERT INTO calendar (course_id, event_name, event_description, event_date, event_time, duration, tutor_id) VALUES
(1, 'Topology Lecture', 'Topology Lecture', '2025-01-31', '10:00:00', 60, 1),
(2, 'Quantum Tunneling Lecture', 'Quantum Tunneling Lecture', '2025-01-31', '10:00:00', 60, 2);

-- Inserting example questions into quizzes values to use in code
INSERT INTO quizzes (course_id, tutor_id, student_id, quiz_name, quiz_description, quiz_questions_number, quiz_questions, quiz_question_wrong_answers, quiz_question_correct_answers, quiz_date, quiz_time, duration) VALUES
(1, 1, 1, 'Topology Quiz', 'Topology Quiz', '1', 'What is the definition of a topological space?','A topological pace is a set of functions that define the spaces between atoms', 'A topological space is a set with a collection of open sets satisfying certain properties.', '2025-01-31', '10:00:00', 60),
(2, 2, 1, 'Quantum Tunneling Quiz', 'Quantum Tunneling Quiz', '1', 'What is the definition of quantum tunneling?','Quantum Tunneling is the process of splitting atoms', 'Quantum tunneling is a quantum mechanical phenomenon where a particle tunnels through a barrier that it classically cannot surmount.', '2025-01-31', '10:00:00', 60);

-- Inserting example classes values to use in code
INSERT INTO classes (course_id, tutor_id, student_id, class_name, class_description, class_date, class_time, duration) VALUES
(1, 1, 1, 'Topology Lecture', 'Topology Lecture', '2025-01-31', '10:00:00', 60),
(2, 2, 2, 'Quantum Tunneling Lecture', 'Quantum Tunneling Lecture', '2025-01-31', '10:00:00', 60);

-- Inserting example resources values to use in code
INSERT INTO resources (course_id, tutor_id, file_name, file_path) VALUES
(1, 1, 'Topology Lecture Notes', '/acetraining/uploads/topology.pdf'),
(2, 2, 'Quantum Tunneling Lecture Notes', '/acetraining/uploads/quantum_tunneling.pdf');