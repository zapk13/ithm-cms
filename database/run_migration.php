<?php
/**
 * Run Migration: Add password_needs_reset column
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$dbConfig = require __DIR__ . '/../config/database.php';

$migrationFile = __DIR__ . '/migrations/add_password_needs_reset.sql';

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
    
    $pdo->beginTransaction();
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }
    
    $pdo->commit();
    
    echo "✓ Migration completed successfully!\n";
    echo "Added 'password_needs_reset' column to users table.\n";
    
} catch (PDOException $e) {
    // Check if column already exists
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ Column 'password_needs_reset' already exists. Migration skipped.\n";
    } else {
        // Try to rollback if transaction is active
        try {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
        } catch (\Exception $rollbackError) {
            // Ignore rollback errors
        }
        die("✗ Migration failed: " . $e->getMessage() . "\n");
    }
} catch (\Exception $e) {
    // Try to rollback if transaction is active
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $rollbackError) {
        // Ignore rollback errors
    }
    die("✗ Error: " . $e->getMessage() . "\n");
}

