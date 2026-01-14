<?php
/**
 * Run Migration: Exams, Results, Attendance core tables
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$dbConfig = require __DIR__ . '/../config/database.php';

$migrationFile = __DIR__ . '/migrations/create_academics.sql';

if (!file_exists($migrationFile)) {
    die("Migration file not found: {$migrationFile}\n");
}

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    
    echo "✓ Connected to database\n\n";
    
    $sql = file_get_contents($migrationFile);
    
    // Remove comments and split by semicolon
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt);
        }
    );
    
    // Start transaction (guard: some drivers may already be in auto-commit/off)
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
    }
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }
    
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    
    echo "✓ Migration completed successfully!\n";
    echo "Added exams, results, and attendance core tables.\n";
    
} catch (PDOException $e) {
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $rollbackError) {
        // Ignore rollback errors
    }
    
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "ℹ Tables already exist. Migration skipped.\n";
    } else {
        die("✗ Migration failed: " . $e->getMessage() . "\n");
    }
} catch (\Exception $e) {
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $rollbackError) {
        // Ignore rollback errors
    }
    die("✗ Error: " . $e->getMessage() . "\n");
}


