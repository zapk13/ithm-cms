<?php
/**
 * Reset database to a clean launch state.
 * - Truncates all data tables.
 * - Seeds core reference data: roles, a main campus, and a system admin user.
 *
 * Usage (from project root):
 *   php database/reset_fresh_launch.php
 *
 * Default admin login after reset:
 *   Email: admin@ithm.edu.pk
 *   Password: Admin@123
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
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ Connected to database '{$dbConfig['database']}'\n";
    echo "WARNING: This will remove ALL data and reseed minimal defaults.\n";

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
        'campus_courses',
        'fee_structures',
        'courses',
        'users',
        'roles',
        'campuses',
        'settings'
    ];

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($tables as $table) {
        echo " - Truncating {$table}...\n";
        $pdo->exec("TRUNCATE TABLE `{$table}`");
    }

    // Seed roles
    echo "Seeding roles...\n";
    $roles = [
        ['id' => 1, 'name' => 'System Admin', 'slug' => 'system_admin'],
        ['id' => 2, 'name' => 'Main Campus Admin', 'slug' => 'main_campus_admin'],
        ['id' => 3, 'name' => 'Sub Campus Admin', 'slug' => 'sub_campus_admin'],
        ['id' => 4, 'name' => 'Student', 'slug' => 'student'],
    ];
    $roleStmt = $pdo->prepare("INSERT INTO roles (id, name, slug) VALUES (:id, :name, :slug)");
    foreach ($roles as $role) {
        $roleStmt->execute($role);
    }

    // Seed main campus
    echo "Seeding main campus...\n";
    $campusStmt = $pdo->prepare("
        INSERT INTO campuses 
            (name, type, code, address, city, phone, email, focal_person, is_active) 
        VALUES 
            (:name, 'main', :code, :address, :city, :phone, :email, :focal_person, 1)
    ");
    $campusStmt->execute([
        'name' => 'ITHM Main Campus',
        'code' => 'MAIN',
        'address' => '',
        'city' => '',
        'phone' => '',
        'email' => '',
        'focal_person' => ''
    ]);
    $mainCampusId = (int)$pdo->lastInsertId();
    echo "   Main campus ID: {$mainCampusId}\n";

    // Seed system admin user
    echo "Seeding system admin user...\n";
    $passwordHash = password_hash('Admin@123', PASSWORD_BCRYPT);
    $userStmt = $pdo->prepare("
        INSERT INTO users 
            (name, email, password, phone, role_id, campus_id, is_active) 
        VALUES 
            (:name, :email, :password, :phone, :role_id, :campus_id, 1)
    ");
    $userStmt->execute([
        'name' => 'System Administrator',
        'email' => 'admin@ithm.edu.pk',
        'password' => $passwordHash,
        'phone' => '',
        'role_id' => 1,
        'campus_id' => null
    ]);
    echo "   Admin user created: admin@ithm.edu.pk / Admin@123\n";

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "✓ Reset complete. Database is clean with core roles, a main campus, and an admin user.\n";
} catch (PDOException $e) {
    try {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    } catch (\Exception $ignored) {
    }
    die("✗ Reset failed: " . $e->getMessage() . "\n");
}


