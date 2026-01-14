<?php
/**
 * One-time installer to apply schema.sql and all migrations on the server.
 * IMPORTANT: Protect this script. Set a strong token and delete the file after use.
 *
 * Browser usage (copy to public/ temporarily):
 *   1) Copy this file to public/install.php
 *   2) Browse to: https://cms.ithm.edu.pk/install.php?token=YOUR_TOKEN
 *   3) Delete public/install.php immediately after success.
 */

// --------- CONFIGURE A STRONG TOKEN BEFORE RUNNING ---------
$INSTALL_TOKEN = 'zapk131122';
// -----------------------------------------------------------

// Basic token guard
$token = $_GET['token'] ?? ($_SERVER['argv'][1] ?? null);
if (!$token || $token !== $INSTALL_TOKEN) {
    http_response_code(403);
    exit("Forbidden: invalid token\n");
}

header('Content-Type: text/plain');
echo "ITHM CMS Installer\n====================\n\n";

require_once __DIR__ . '/../config/config.php';
$dbConfig = require __DIR__ . '/../config/database.php';

// Helper to run SQL file
function runSqlFile(PDO $pdo, string $filePath, string $targetDb): void
{
    if (!file_exists($filePath)) {
        echo "Skip (missing): {$filePath}\n";
        return;
    }
    $sql = file_get_contents($filePath);
    // strip comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    // force target DB name and drop CREATE/USE statements that point elsewhere
    $sql = preg_replace('/CREATE\s+DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE\s+[`"]?[^`";]+[`"]?;/i', '', $sql);
    $sql = str_replace('ithm_cms', $targetDb, $sql);
    // make CREATE TABLE idempotent
    $sql = preg_replace('/CREATE\s+TABLE\s+/', 'CREATE TABLE IF NOT EXISTS ', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    echo "Running: {$filePath}\n";
    $useTx = !empty($statements);
    if ($useTx) {
        $pdo->beginTransaction();
    }
    try {
        foreach ($statements as $stmt) {
            if ($stmt === '') continue;
            try {
                $pdo->exec($stmt);
            } catch (Throwable $stmtError) {
                $code = $stmtError->getCode();
                // Skip benign duplicates (table exists, duplicate key/index)
                if (in_array($code, ['42S01', '23000'], true)) {
                    echo " ! Skipped duplicate: {$stmtError->getMessage()}\n";
                    continue;
                }
                throw $stmtError;
            }
        }
        if ($useTx && $pdo->inTransaction()) {
            $pdo->commit();
        }
        echo " ✓ Done: {$filePath}\n\n";
    } catch (Throwable $e) {
        if ($useTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'] ?? 3306,
        $dbConfig['database'],
        $dbConfig['charset']
    );
    $pdo = new PDO(
        $dsn,
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
    echo "Connected to database: {$dbConfig['database']}\n\n";

    // Run base schema then all migrations
    runSqlFile($pdo, __DIR__ . '/schema.sql', $dbConfig['database']);

    $migrations = [
        __DIR__ . '/migrations/add_password_needs_reset.sql',
        __DIR__ . '/migrations/add_campus_bank_contact_logo.sql',
        __DIR__ . '/migrations/add_shift_to_fee_structures.sql',
        __DIR__ . '/migrations/add_fee_breakdown_to_vouchers.sql',
        __DIR__ . '/migrations/create_academics.sql',
        __DIR__ . '/migrations/add_is_trashed_to_admissions.sql',
    ];

    foreach ($migrations as $migration) {
        runSqlFile($pdo, $migration, $dbConfig['database']);
    }

    echo "All done. Please DELETE this file (database/install.php) and any public copy.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}


