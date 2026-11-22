CREATE DATABASE IF NOT EXISTS `jerro_cbt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jerro_cbt`;

-- Table for Programs
CREATE TABLE IF NOT EXISTS `programs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `program_key` VARCHAR(50) NOT NULL UNIQUE,
  `program_name` VARCHAR(100) NOT NULL
);

INSERT INTO `programs` (`program_key`, `program_name`) VALUES
('diploma', 'Diploma in Computer Studies'),
('adv_diploma', 'Advanced Diploma in Computer Studies'),
('fullstack', 'Full Stack Web Development');

-- Table for Subjects
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_name` VARCHAR(50) NOT NULL
);

INSERT INTO `subjects` (`subject_name`) VALUES
('MS Word'), ('PowerPoint'), ('Publisher'), ('CorelDraw'), ('Excel'),
('HTML'), ('CSS'), ('JavaScript'), ('GitHub'), ('PHP'), ('MySQL');

-- Table mapping Programs to Subjects
CREATE TABLE IF NOT EXISTS `program_subjects` (
  `program_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  PRIMARY KEY (`program_id`, `subject_id`),
  FOREIGN KEY (`program_id`) REFERENCES `programs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
);

-- Populate Program-Subject mappings
-- Diploma: MS Word, PowerPoint, Publisher, CorelDraw, Excel
INSERT INTO `program_subjects` (`program_id`, `subject_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5);

-- Advanced Diploma: MS Word, PowerPoint, Publisher, CorelDraw
INSERT INTO `program_subjects` (`program_id`, `subject_id`) VALUES
(2, 1), (2, 2), (2, 3), (2, 4);

-- Full Stack: HTML, CSS, JavaScript, GitHub, PHP, MySQL
INSERT INTO `program_subjects` (`program_id`, `subject_id`) VALUES
(3, 6), (3, 7), (3, 8), (3, 9), (3, 10), (3, 11);

-- Table for Students
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `program_key` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `students` (`student_id`, `full_name`, `program_key`) VALUES
('2026085825', 'Nwabueze Obuikem Princewill', 'fullstack'),
('2026001002', 'Amina Bello', 'diploma'),
('2026001003', 'Chidi Okonkwo', 'adv_diploma')
('2026001004', 'Chukwuma kelvin', 'adv_diploma');

-- Table for Questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `type` ENUM('exam', 'test') DEFAULT 'exam',
  `question_text` TEXT NOT NULL,
  `option_a` VARCHAR(255) NOT NULL,
  `option_b` VARCHAR(255) NOT NULL,
  `option_c` VARCHAR(255) NOT NULL,
  `option_d` VARCHAR(255) NOT NULL,
  `correct_option` CHAR(1) NOT NULL,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
);

-- Sample Questions
INSERT INTO `questions` (`subject_id`, `type`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(6, 'exam', 'What does HTML stand for?', 'Hyper Text Markup Language', 'High Text Machine Language', 'Hyperlink and Text Marking Language', 'Home Tool Markup Language', 'A'),
(6, 'exam', 'Which HTML element is used for the largest heading?', '<heading>', '<h6>', '<h1>', '<head>', 'C'),
(7, 'exam', 'What does CSS stand for?', 'Cascading Style Sheets', 'Creative Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'A'),
(8, 'exam', 'Inside which HTML element do we put JavaScript code?', '<js>', '<scripting>', '<javascript>', '<script>', 'D');

-- Table for Test/Exam Submissions
CREATE TABLE IF NOT EXISTS `submissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` VARCHAR(50) NOT NULL,
  `type` ENUM('exam', 'test') DEFAULT 'exam',
  `total_score` INT NOT NULL,
  `max_score` INT NOT NULL,
  `percentage` DECIMAL(5,2) NOT NULL,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Detailed Answers (for Admin Diagnostic View)
CREATE TABLE IF NOT EXISTS `submission_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `submission_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `user_answer` CHAR(1) NOT NULL,
  `is_correct` TINYINT(1) NOT NULL,
  FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
);