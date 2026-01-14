<?php
/**
 * Run Campus Migration
 * Adds bank account, contact details, and logo fields to campuses table
 */

$dbConfig = require __DIR__ . '/../config/database.php';

$migrationFile = __DIR__ . '/migrations/add_campus_bank_contact_logo.sql';

if (!file_exists($migrationFile)) {
    die("Migration file not found: $migrationFile\n");
}

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    
    echo "Running migration: add_campus_bank_contact_logo.sql\n";
    echo "==================================================\n\n";
    
    $sql = file_get_contents($migrationFile);
    
    // Remove comments and split by semicolon
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt);
        }
    );
    
    // Check if columns already exist
    $checkColumns = [
        'bank_account_name', 'bank_account_number', 'bank_name', 
        'bank_branch', 'iban', 'contact_person_name', 
        'contact_person_phone', 'contact_person_email', 'logo'
    ];
    
    $existingColumns = [];
    foreach ($checkColumns as $col) {
        $stmt = $pdo->query("SHOW COLUMNS FROM campuses LIKE '$col'");
        if ($stmt->rowCount() > 0) {
            $existingColumns[] = $col;
        }
    }
    
    if (!empty($existingColumns)) {
        echo "⚠️  Warning: Some columns already exist: " . implode(', ', $existingColumns) . "\n";
        echo "Migration will skip existing columns.\n\n";
    }
    
    $pdo->beginTransaction();
    
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // If column already exists, skip it
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "ℹ Skipping existing column\n";
                    continue;
                }
                throw $e;
            }
        }
    }
    
    $pdo->commit();
    
    echo "✅ Migration completed successfully!\n";
    echo "\nAdded columns to campuses table:\n";
    echo "  - Bank Account Information (bank_account_name, bank_account_number, bank_name, bank_branch, iban)\n";
    echo "  - Contact Person Details (contact_person_name, contact_person_phone, contact_person_email)\n";
    echo "  - Logo upload field (logo)\n";
    
} catch (PDOException $e) {
    // Try to rollback if transaction is active
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $rollbackError) {
        // Ignore rollback errors
    }
    
    // Check if error is due to duplicate column
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "⚠️  Some columns already exist. This is okay - migration partially applied.\n";
        echo "Error details: " . $e->getMessage() . "\n";
    } else {
        echo "❌ Migration failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
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
    
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
