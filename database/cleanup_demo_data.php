<?php
/**
 * One-time cleanup script to remove demo/test transactional data.
 *
 * IMPORTANT:
 * - Run a full DB backup BEFORE executing this script.
 * - This will TRUNCATE (empty) key transactional tables, but will keep
 *   master data like campuses, courses, fee structures, users, settings.
 *
 * Usage (from project root):
 *   php database/cleanup_demo_data.php
 */

require_once __DIR__ . '/../config/config.php';
$dbConfig = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );

    echo "✓ Connected to database '{$dbConfig['database']}'\n";
    echo "WARNING: This will remove demo/test transactional data.\n";

    // List of tables to truncate (order does not matter with FK checks disabled)
    $tables = [
        'notifications',
        'attendance_records',
        'attendance_sessions',
        'exam_marks',
        'exam_registrations',
        'exams',
        'exam_terms',
        'course_results',
        'fee_payments',
        'fee_vouchers',
        'admission_documents',
        'admissions',
        'certificates',
    ];

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    foreach ($tables as $table) {
        echo " - Truncating {$table}...\n";
        $pdo->exec("TRUNCATE TABLE `{$table}`");
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "✓ Cleanup completed. Transactional demo data has been removed.\n";
    echo "   Master data (users, campuses, courses, fee_structures, settings) was left intact.\n";
} catch (PDOException $e) {
    die("✗ Cleanup failed: " . $e->getMessage() . "\n");
}


