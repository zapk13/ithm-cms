-- Exams, Results, Attendance core tables

-- Exam terms/semesters
CREATE TABLE IF NOT EXISTS `exam_terms` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` ENUM('draft','active','closed') DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_exam_terms_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exams (per course/section)
CREATE TABLE IF NOT EXISTS `exams` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `exam_term_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `exam_type` ENUM('midterm','final','quiz','assignment','practical','other') DEFAULT 'other',
    `exam_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `venue` VARCHAR(200) DEFAULT NULL,
    `total_marks` DECIMAL(6,2) DEFAULT 100.00,
    `weightage` DECIMAL(5,2) DEFAULT 0.00,
    `status` ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_exams_term` (`exam_term_id`),
    INDEX `idx_exams_course` (`course_id`),
    FOREIGN KEY (`exam_term_id`) REFERENCES `exam_terms`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exam registrations (eligibility + payment clearance ready)
CREATE TABLE IF NOT EXISTS `exam_registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `status` ENUM('registered','ineligible','withdrawn') DEFAULT 'registered',
    `remarks` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_exam_reg_user` (`exam_id`,`user_id`),
    INDEX `idx_exam_reg_user` (`user_id`),
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exam marks (per component)
CREATE TABLE IF NOT EXISTS `exam_marks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `obtained_marks` DECIMAL(6,2) DEFAULT 0.00,
    `is_finalized` TINYINT(1) DEFAULT 0,
    `graded_by` INT UNSIGNED DEFAULT NULL,
    `graded_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_exam_mark` (`exam_id`,`user_id`),
    INDEX `idx_exam_mark_user` (`user_id`),
    FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`graded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course-level results (aggregated)
CREATE TABLE IF NOT EXISTS `course_results` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `attempt` INT UNSIGNED DEFAULT 1,
    `total_marks` DECIMAL(6,2) DEFAULT 0.00,
    `percentage` DECIMAL(5,2) DEFAULT 0.00,
    `grade` VARCHAR(10) DEFAULT NULL,
    `status` ENUM('in_progress','passed','failed','incomplete') DEFAULT 'in_progress',
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_course_result` (`course_id`,`user_id`,`attempt`),
    INDEX `idx_course_result_user` (`user_id`),
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance sessions (per class meeting)
CREATE TABLE IF NOT EXISTS `attendance_sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `instructor_id` INT UNSIGNED DEFAULT NULL,
    `session_date` DATE NOT NULL,
    `start_time` TIME DEFAULT NULL,
    `end_time` TIME DEFAULT NULL,
    `session_type` ENUM('lecture','lab','tutorial','exam') DEFAULT 'lecture',
    `topic` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_attendance_course` (`course_id`),
    INDEX `idx_attendance_instructor` (`instructor_id`),
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance records (per student per session)
CREATE TABLE IF NOT EXISTS `attendance_records` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `attendance_session_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `status` ENUM('present','absent','late','excused') DEFAULT 'present',
    `checkin_time` DATETIME DEFAULT NULL,
    `remarks` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_attendance_record` (`attendance_session_id`,`user_id`),
    INDEX `idx_attendance_user` (`user_id`),
    FOREIGN KEY (`attendance_session_id`) REFERENCES `attendance_sessions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


