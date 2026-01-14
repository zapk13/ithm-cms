-- ITHM CMS Database Schema
-- Run this file in phpMyAdmin or MySQL CLI

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS `ithm_cms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ithm_cms`;

-- --------------------------------------------------------
-- Table: roles
-- --------------------------------------------------------
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `permissions` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: campuses
-- --------------------------------------------------------
CREATE TABLE `campuses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `type` ENUM('main', 'sub') NOT NULL DEFAULT 'sub',
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `address` TEXT,
    `city` VARCHAR(100),
    `phone` VARCHAR(30),
    `email` VARCHAR(150),
    `focal_person` VARCHAR(150),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(30),
    `cnic` VARCHAR(20),
    `role_id` INT UNSIGNED NOT NULL,
    `campus_id` INT UNSIGNED DEFAULT NULL,
    `profile_image` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `email_verified_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `reset_token` VARCHAR(100) DEFAULT NULL,
    `reset_token_expiry` TIMESTAMP NULL,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`campus_id`) REFERENCES `campuses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: courses
-- --------------------------------------------------------
CREATE TABLE `courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `description` TEXT,
    `duration_months` INT DEFAULT 12,
    `total_seats` INT DEFAULT 50,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: campus_courses (Many-to-Many)
-- --------------------------------------------------------
CREATE TABLE `campus_courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `campus_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `available_seats` INT DEFAULT 50,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`campus_id`) REFERENCES `campuses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_campus_course` (`campus_id`, `course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: fee_structures
-- --------------------------------------------------------
CREATE TABLE `fee_structures` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `campus_id` INT UNSIGNED NOT NULL,
    `admission_fee` DECIMAL(12,2) DEFAULT 0.00,
    `tuition_fee` DECIMAL(12,2) DEFAULT 0.00,
    `semester_fee` DECIMAL(12,2) DEFAULT 0.00,
    `monthly_fee` DECIMAL(12,2) DEFAULT 0.00,
    `exam_fee` DECIMAL(12,2) DEFAULT 0.00,
    `other_charges` DECIMAL(12,2) DEFAULT 0.00,
    `currency` VARCHAR(10) DEFAULT 'PKR',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`campus_id`) REFERENCES `campuses`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_fee_structure` (`course_id`, `campus_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: admissions
-- --------------------------------------------------------
CREATE TABLE `admissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `campus_id` INT UNSIGNED NOT NULL,
    `application_no` VARCHAR(30) UNIQUE,
    `roll_number` VARCHAR(30) DEFAULT NULL,
    `batch` VARCHAR(30) DEFAULT NULL,
    `shift` ENUM('morning', 'evening') DEFAULT 'morning',
    `status` ENUM('pending', 'approved', 'rejected', 'update_required') DEFAULT 'pending',
    `personal_info` JSON NOT NULL,
    `guardian_info` JSON NOT NULL,
    `academic_info` JSON NOT NULL,
    `admin_remarks` TEXT DEFAULT NULL,
    `reviewed_by` INT UNSIGNED DEFAULT NULL,
    `reviewed_at` TIMESTAMP NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`campus_id`) REFERENCES `campuses`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: admission_documents
-- --------------------------------------------------------
CREATE TABLE `admission_documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admission_id` INT UNSIGNED NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255),
    `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    `remarks` TEXT DEFAULT NULL,
    `verified_by` INT UNSIGNED DEFAULT NULL,
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admission_id`) REFERENCES `admissions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: fee_vouchers
-- --------------------------------------------------------
CREATE TABLE `fee_vouchers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `voucher_no` VARCHAR(30) NOT NULL UNIQUE,
    `user_id` INT UNSIGNED NOT NULL,
    `admission_id` INT UNSIGNED DEFAULT NULL,
    `campus_id` INT UNSIGNED NOT NULL,
    `fee_type` ENUM('admission', 'semester', 'monthly', 'exam', 'other') DEFAULT 'admission',
    `amount` DECIMAL(12,2) NOT NULL,
    `due_date` DATE NOT NULL,
    `status` ENUM('unpaid', 'pending_verification', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid',
    `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admission_id`) REFERENCES `admissions`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`campus_id`) REFERENCES `campuses`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: fee_payments
-- --------------------------------------------------------
CREATE TABLE `fee_payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `voucher_id` INT UNSIGNED NOT NULL,
    `amount_paid` DECIMAL(12,2) NOT NULL,
    `transaction_id` VARCHAR(100),
    `payment_method` VARCHAR(50) DEFAULT 'bank_transfer',
    `proof_file` VARCHAR(255),
    `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    `remarks` TEXT DEFAULT NULL,
    `verified_by` INT UNSIGNED DEFAULT NULL,
    `verified_at` TIMESTAMP NULL,
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`voucher_id`) REFERENCES `fee_vouchers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: notifications
-- --------------------------------------------------------
CREATE TABLE `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('system', 'manual', 'admission', 'fee', 'certificate') DEFAULT 'system',
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` INT UNSIGNED DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: certificates
-- --------------------------------------------------------
CREATE TABLE `certificates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `admission_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `certificate_type` VARCHAR(50) DEFAULT 'completion',
    `file_path` VARCHAR(255) NOT NULL,
    `issued_by` INT UNSIGNED DEFAULT NULL,
    `issued_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admission_id`) REFERENCES `admissions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`issued_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    `type` VARCHAR(20) DEFAULT 'string',
    `group` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: activity_logs
-- --------------------------------------------------------
CREATE TABLE `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `model_type` VARCHAR(100) DEFAULT NULL,
    `model_id` INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_action` (`action`),
    INDEX `idx_model` (`model_type`, `model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert Default Roles
-- --------------------------------------------------------
INSERT INTO `roles` (`name`, `slug`, `permissions`) VALUES
('System Admin', 'system_admin', '{"all": true}'),
('Main Campus Admin', 'main_campus_admin', '{"campuses": ["view", "manage"], "courses": ["view", "create", "edit"], "fees": ["view", "create", "edit"], "admissions": ["view", "review", "approve"], "users": ["view"]}'),
('Sub Campus Admin', 'sub_campus_admin', '{"admissions": ["view", "review", "approve"], "fees": ["view", "verify"], "certificates": ["upload"]}'),
('Student', 'student', '{"admissions": ["apply", "view_own"], "fees": ["view_own", "pay"], "certificates": ["view_own", "download"]}');

-- --------------------------------------------------------
-- Insert Default Settings
-- --------------------------------------------------------
INSERT INTO `settings` (`key`, `value`, `type`, `group`) VALUES
('institute_name', 'Institute of Tourism and Hospitality Management', 'string', 'general'),
('institute_short_name', 'ITHM', 'string', 'general'),
('institute_logo', NULL, 'string', 'general'),
('institute_email', 'info@ithm.edu.pk', 'string', 'general'),
('institute_phone', '+92-XXX-XXXXXXX', 'string', 'general'),
('institute_address', 'Lahore, Pakistan', 'string', 'general'),
('fee_due_reminder_days', '7', 'integer', 'notifications'),
('admission_fee_due_days', '14', 'integer', 'fees'),
('smtp_host', '', 'string', 'email'),
('smtp_port', '587', 'string', 'email'),
('smtp_username', '', 'string', 'email'),
('smtp_password', '', 'string', 'email'),
('smtp_encryption', 'tls', 'string', 'email');

-- --------------------------------------------------------
-- Insert Main Campus
-- --------------------------------------------------------
INSERT INTO `campuses` (`name`, `type`, `code`, `address`, `city`, `phone`, `email`, `focal_person`) VALUES
('ITHM Main Campus', 'main', 'MAIN', 'Main Campus Address', 'Lahore', '+92-XXX-XXXXXXX', 'main@ithm.edu.pk', 'Admin');

-- --------------------------------------------------------
-- Insert Default System Admin (Password: Admin@123)
-- --------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `campus_id`, `is_active`) VALUES
('System Administrator', 'admin@ithm.edu.pk', '$2y$10$iuSduTHxcSanp0/5d9Q3PukHHyBZGSyLMukytnBSn4oj/44jjUqyy', 1, NULL, 1);

-- --------------------------------------------------------
-- Insert Sample Courses
-- --------------------------------------------------------
INSERT INTO `courses` (`name`, `code`, `description`, `duration_months`, `total_seats`, `is_active`) VALUES
('Diploma in Hotel Management', 'DHM', 'Comprehensive diploma covering all aspects of hotel management including front office, housekeeping, and food service.', 12, 50, 1),
('Certificate in Culinary Arts', 'CCA', 'Professional culinary training covering international cuisines, pastry making, and kitchen management.', 6, 40, 1),
('Diploma in Tourism Management', 'DTM', 'Learn travel agency operations, tour planning, destination management, and tourism marketing.', 12, 40, 1),
('Certificate in Food & Beverage Service', 'CFBS', 'Training in restaurant service, beverage management, and customer hospitality.', 6, 35, 1),
('Diploma in Hospitality & Tourism', 'DHT', 'Combined program covering both hospitality and tourism sectors.', 18, 45, 1),
('Certificate in Front Office Management', 'CFOM', 'Specialized training in hotel reception, reservations, and guest relations.', 4, 30, 1);

-- --------------------------------------------------------
-- Link Courses to Main Campus
-- --------------------------------------------------------
INSERT INTO `campus_courses` (`course_id`, `campus_id`, `available_seats`, `is_active`) VALUES
(1, 1, 50, 1),
(2, 1, 40, 1),
(3, 1, 40, 1),
(4, 1, 35, 1),
(5, 1, 45, 1),
(6, 1, 30, 1);

-- --------------------------------------------------------
-- Insert Sample Fee Structures
-- --------------------------------------------------------
INSERT INTO `fee_structures` (`course_id`, `campus_id`, `admission_fee`, `tuition_fee`, `semester_fee`, `monthly_fee`, `exam_fee`, `other_charges`, `is_active`) VALUES
(1, 1, 15000, 80000, 40000, 8000, 3000, 2000, 1),
(2, 1, 10000, 50000, 25000, 6000, 2000, 1500, 1),
(3, 1, 15000, 75000, 37500, 7500, 3000, 2000, 1),
(4, 1, 10000, 45000, 22500, 5500, 2000, 1500, 1),
(5, 1, 18000, 95000, 47500, 9000, 3500, 2500, 1),
(6, 1, 8000, 35000, 17500, 4500, 1500, 1000, 1);

COMMIT;

