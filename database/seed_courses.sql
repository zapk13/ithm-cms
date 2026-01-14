-- ========================================================
-- ITHM CMS - Add Sample Courses
-- Run this script in phpMyAdmin to add courses
-- ========================================================

USE `ithm_cms`;

-- Insert Sample Courses (ignore if they already exist)
INSERT IGNORE INTO `courses` (`name`, `code`, `description`, `duration_months`, `total_seats`, `is_active`) VALUES
('Diploma in Hotel Management', 'DHM', 'Comprehensive diploma covering all aspects of hotel management including front office, housekeeping, and food service.', 12, 50, 1),
('Certificate in Culinary Arts', 'CCA', 'Professional culinary training covering international cuisines, pastry making, and kitchen management.', 6, 40, 1),
('Diploma in Tourism Management', 'DTM', 'Learn travel agency operations, tour planning, destination management, and tourism marketing.', 12, 40, 1),
('Certificate in Food & Beverage Service', 'CFBS', 'Training in restaurant service, beverage management, and customer hospitality.', 6, 35, 1),
('Diploma in Hospitality & Tourism', 'DHT', 'Combined program covering both hospitality and tourism sectors.', 18, 45, 1),
('Certificate in Front Office Management', 'CFOM', 'Specialized training in hotel reception, reservations, and guest relations.', 4, 30, 1);

-- Get the main campus ID
SET @main_campus_id = (SELECT id FROM campuses WHERE type = 'main' LIMIT 1);

-- Link Courses to Main Campus (skip if already linked)
INSERT IGNORE INTO `campus_courses` (`course_id`, `campus_id`, `available_seats`, `is_active`)
SELECT c.id, @main_campus_id, c.total_seats, 1
FROM courses c
WHERE NOT EXISTS (
    SELECT 1 FROM campus_courses cc WHERE cc.course_id = c.id AND cc.campus_id = @main_campus_id
);

-- Insert Fee Structures (update if exists)
INSERT INTO `fee_structures` (`course_id`, `campus_id`, `admission_fee`, `tuition_fee`, `semester_fee`, `monthly_fee`, `exam_fee`, `other_charges`, `is_active`)
SELECT c.id, @main_campus_id, 
    CASE 
        WHEN c.duration_months <= 6 THEN 10000
        WHEN c.duration_months <= 12 THEN 15000
        ELSE 18000
    END as admission_fee,
    CASE 
        WHEN c.duration_months <= 6 THEN 50000
        WHEN c.duration_months <= 12 THEN 80000
        ELSE 95000
    END as tuition_fee,
    CASE 
        WHEN c.duration_months <= 6 THEN 25000
        WHEN c.duration_months <= 12 THEN 40000
        ELSE 47500
    END as semester_fee,
    CASE 
        WHEN c.duration_months <= 6 THEN 6000
        WHEN c.duration_months <= 12 THEN 8000
        ELSE 9000
    END as monthly_fee,
    2500 as exam_fee,
    2000 as other_charges,
    1 as is_active
FROM courses c
WHERE NOT EXISTS (
    SELECT 1 FROM fee_structures fs WHERE fs.course_id = c.id AND fs.campus_id = @main_campus_id
)
ON DUPLICATE KEY UPDATE admission_fee = VALUES(admission_fee);

-- Show what was added
SELECT 'Courses added:' as status;
SELECT id, name, code, duration_months FROM courses;

SELECT 'Campus-Course links:' as status;
SELECT cc.*, c.name as course_name, cp.name as campus_name 
FROM campus_courses cc 
JOIN courses c ON cc.course_id = c.id 
JOIN campuses cp ON cc.campus_id = cp.id;

SELECT 'Fee structures:' as status;
SELECT fs.*, c.name as course_name 
FROM fee_structures fs 
JOIN courses c ON fs.course_id = c.id;

