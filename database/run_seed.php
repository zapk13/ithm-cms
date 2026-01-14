<?php
/**
 * Seed Courses Script
 * Run: php database/run_seed.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$dbConfig = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    
    echo "✓ Connected to database\n\n";
    
    // Add sample courses
    echo "Adding courses...\n";
    $courses = [
        ['Diploma in Hotel Management', 'DHM', 'Comprehensive diploma covering all aspects of hotel management including front office, housekeeping, and food service.', 12, 50],
        ['Certificate in Culinary Arts', 'CCA', 'Professional culinary training covering international cuisines, pastry making, and kitchen management.', 6, 40],
        ['Diploma in Tourism Management', 'DTM', 'Learn travel agency operations, tour planning, destination management, and tourism marketing.', 12, 40],
        ['Certificate in Food & Beverage Service', 'CFBS', 'Training in restaurant service, beverage management, and customer hospitality.', 6, 35],
        ['Diploma in Hospitality & Tourism', 'DHT', 'Combined program covering both hospitality and tourism sectors.', 18, 45],
        ['Certificate in Front Office Management', 'CFOM', 'Specialized training in hotel reception, reservations, and guest relations.', 4, 30]
    ];
    
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO courses (name, code, description, duration_months, total_seats, is_active) 
        VALUES (?, ?, ?, ?, ?, 1)
    ");
    
    $added = 0;
    foreach ($courses as $course) {
        $stmt->execute($course);
        if ($stmt->rowCount() > 0) {
            $added++;
            echo "  ✓ Added: {$course[0]} ({$course[1]})\n";
        } else {
            echo "  - Already exists: {$course[0]} ({$course[1]})\n";
        }
    }
    
    echo "\n✓ Courses processed: {$added} new, " . (count($courses) - $added) . " already existed\n\n";
    
    // Get main campus ID
    $campusStmt = $pdo->query("SELECT id FROM campuses WHERE type = 'main' LIMIT 1");
    $campus = $campusStmt->fetch();
    
    if (!$campus) {
        throw new Exception("Main campus not found!");
    }
    
    $campusId = $campus['id'];
    echo "Main campus ID: {$campusId}\n\n";
    
    // Link courses to main campus
    echo "Linking courses to campus...\n";
    $linkStmt = $pdo->prepare("
        INSERT IGNORE INTO campus_courses (course_id, campus_id, available_seats, is_active)
        SELECT id, ?, total_seats, 1
        FROM courses
        WHERE NOT EXISTS (
            SELECT 1 FROM campus_courses cc 
            WHERE cc.course_id = courses.id AND cc.campus_id = ?
        )
    ");
    
    $linkStmt->execute([$campusId, $campusId]);
    $linked = $linkStmt->rowCount();
    echo "  ✓ Linked {$linked} courses to campus\n\n";
    
    // Add fee structures
    echo "Adding fee structures...\n";
    $feeStmt = $pdo->prepare("
        INSERT INTO fee_structures 
        (course_id, campus_id, admission_fee, tuition_fee, semester_fee, monthly_fee, exam_fee, other_charges, is_active)
        SELECT 
            c.id, 
            ?,
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
            SELECT 1 FROM fee_structures fs 
            WHERE fs.course_id = c.id AND fs.campus_id = ?
        )
    ");
    
    $feeStmt->execute([$campusId, $campusId]);
    $feeAdded = $feeStmt->rowCount();
    echo "  ✓ Added {$feeAdded} fee structures\n\n";
    
    // Show summary
    echo "═══════════════════════════════════════════════════════════\n";
    echo "SUMMARY\n";
    echo "═══════════════════════════════════════════════════════════\n";
    
    $summaryStmt = $pdo->query("
        SELECT 
            COUNT(DISTINCT c.id) as total_courses,
            COUNT(DISTINCT cc.course_id) as linked_courses,
            COUNT(DISTINCT fs.course_id) as courses_with_fees
        FROM courses c
        LEFT JOIN campus_courses cc ON c.id = cc.course_id AND cc.campus_id = {$campusId}
        LEFT JOIN fee_structures fs ON c.id = fs.course_id AND fs.campus_id = {$campusId}
        WHERE c.is_active = 1
    ");
    $summary = $summaryStmt->fetch();
    
    echo "Total Active Courses: {$summary['total_courses']}\n";
    echo "Courses Linked to Campus: {$summary['linked_courses']}\n";
    echo "Courses with Fee Structures: {$summary['courses_with_fees']}\n";
    echo "═══════════════════════════════════════════════════════════\n";
    
    echo "\n✓ Database seeding completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

