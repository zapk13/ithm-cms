<?php
/**
 * Run Fee Structures Migration
 * Adds shift column to fee_structures and fee_breakdown to fee_vouchers
 */

$dbConfig = require __DIR__ . '/../config/database.php';

$migrations = [
    __DIR__ . '/migrations/add_shift_to_fee_structures.sql',
    __DIR__ . '/migrations/add_fee_breakdown_to_vouchers.sql'
];

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    
    echo "Running fee structure migrations\n";
    echo "=================================\n\n";
    
    foreach ($migrations as $migrationFile) {
        if (!file_exists($migrationFile)) {
            echo "⚠️  Migration file not found: $migrationFile\n";
            continue;
        }
        
        $migrationName = basename($migrationFile);
        echo "Running: $migrationName\n";
        
        $sql = file_get_contents($migrationFile);
        
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt);
            }
        );
        
        // Special handling for shift migration
        if (strpos($migrationName, 'add_shift_to_fee_structures') !== false) {
            // Check if shift column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM fee_structures LIKE 'shift'");
            $shiftExists = $stmt->rowCount() > 0;
            
            if (!$shiftExists) {
                $pdo->beginTransaction();
                try {
                    // Add shift column
                    $pdo->exec("ALTER TABLE `fee_structures` ADD COLUMN `shift` ENUM('morning', 'evening') DEFAULT 'morning' AFTER `campus_id`");
                    
                    // Update existing records
                    $pdo->exec("UPDATE `fee_structures` SET `shift` = 'morning' WHERE `shift` IS NULL");
                    
                    // Check if unique constraint exists and needs updating
                    $indexStmt = $pdo->query("SHOW INDEX FROM fee_structures WHERE Key_name = 'unique_fee_structure'");
                    $indexExists = $indexStmt->rowCount() > 0;
                    
                    if ($indexExists) {
                        // Check if shift is already in the index
                        $indexCols = $pdo->query("SHOW INDEX FROM fee_structures WHERE Key_name = 'unique_fee_structure'");
                        $hasShift = false;
                        while ($row = $indexCols->fetch()) {
                            if ($row['Column_name'] === 'shift') {
                                $hasShift = true;
                                break;
                            }
                        }
                        
                        if (!$hasShift) {
                            // Drop and recreate with shift
                            $pdo->exec("ALTER TABLE `fee_structures` DROP INDEX `unique_fee_structure`");
                            $pdo->exec("ALTER TABLE `fee_structures` ADD UNIQUE KEY `unique_fee_structure` (`course_id`, `campus_id`, `shift`)");
                        }
                    } else {
                        // Create new unique constraint with shift
                        $pdo->exec("ALTER TABLE `fee_structures` ADD UNIQUE KEY `unique_fee_structure` (`course_id`, `campus_id`, `shift`)");
                    }
                    
                    $pdo->commit();
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                        throw $e;
                    }
                    echo "ℹ Shift column already exists\n";
                }
            } else {
                echo "ℹ Shift column already exists\n";
            }
        } else {
            // Regular migration handling
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    try {
                        // Check if it's an ALTER TABLE for adding column
                        if (preg_match('/ALTER TABLE.*ADD COLUMN.*fee_breakdown/i', $statement)) {
                            // Check if column exists first
                            $checkStmt = $pdo->query("SHOW COLUMNS FROM fee_vouchers LIKE 'fee_breakdown'");
                            if ($checkStmt->rowCount() > 0) {
                                echo "ℹ fee_breakdown column already exists\n";
                                continue;
                            }
                        }
                        
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // If column already exists, skip it
                        if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
                            strpos($e->getMessage(), 'Duplicate key name') !== false) {
                            echo "ℹ Skipping existing column/index\n";
                            continue;
                        }
                        throw $e;
                    }
                }
            }
        }
        echo "✅ $migrationName completed\n\n";
    }
    
    echo "✅ All migrations completed successfully!\n";
    echo "\nChanges:\n";
    echo "  - Added 'shift' column to fee_structures table\n";
    echo "  - Updated unique constraint to include shift\n";
    echo "  - Added 'fee_breakdown' JSON column to fee_vouchers table\n";
    
} catch (PDOException $e) {
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $rollbackError) {
        // Ignore rollback errors
    }
    
    if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
        strpos($e->getMessage(), 'Duplicate key name') !== false) {
        echo "⚠️  Some columns/indexes already exist. This is okay.\n";
        echo "Error details: " . $e->getMessage() . "\n";
    } else {
        echo "❌ Migration failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
} catch (\Exception $e) {
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

