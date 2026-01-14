<?php
/**
 * ITHM CMS Test Runner Script
 * 
 * Run: php tests/run_tests.php
 */

// Bootstrap
require_once __DIR__ . '/../config/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    // App namespace
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return;
    }
    
    // Tests namespace
    $prefix = 'Tests\\';
    $baseDir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// Test classes to run
$testClasses = [
    Tests\DatabaseTest::class,
    Tests\AuthenticationTest::class,
    Tests\ValidationTest::class,
    Tests\CampusTest::class,
    Tests\CourseTest::class,
    Tests\FeeStructureTest::class,
    Tests\AdmissionTest::class,
    Tests\FeeVoucherTest::class,
    Tests\NotificationTest::class,
    Tests\SettingsTest::class,
    Tests\SecurityTest::class,
    Tests\RouteTest::class,
];

// Run tests
$runner = new Tests\TestRunner();
$runner->run($testClasses);

