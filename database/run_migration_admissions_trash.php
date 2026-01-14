<?php
/**
 * Run Migration: Add is_trashed to admissions
 */

require_once __DIR__ . '/../config/config.php';
$dbConfig = require __DIR__ . '/../config/database.php';

$migrationFile = __DIR__ . '/migrations/add_is_trashed_to_admissions.sql';

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
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    if (!empty($statements)) {
        $pdo->beginTransaction();
        foreach ($statements as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
        $pdo->commit();
    }

    echo "✓ Migration completed (or no changes needed): added is_trashed to admissions.\n";
} catch (PDOException $e) {
    // If column already exists, treat as successful; otherwise fail
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ Column is_trashed already exists on admissions. Migration skipped.\n";
        exit(0);
    }
    try {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (\Exception $ignored) {
    }
    die("✗ Migration failed: " . $e->getMessage() . \"\n\");
}


