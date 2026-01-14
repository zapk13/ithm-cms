<?php
/**
 * ITHM CMS Test Runner
 * Simple testing framework for validating all system requirements
 */

namespace Tests;

class TestRunner
{
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;
    private int $total = 0;
    
    public function run(array $testClasses): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║           ITHM CMS - COMPREHENSIVE TEST SUITE                ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n\n";
        
        foreach ($testClasses as $className) {
            $this->runTestClass($className);
        }
        
        $this->printSummary();
    }
    
    private function runTestClass(string $className): void
    {
        $class = new $className();
        $reflection = new \ReflectionClass($class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $testName = str_replace('Tests\\', '', $className);
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        echo "│ " . str_pad($testName, 59) . " │\n";
        echo "└─────────────────────────────────────────────────────────────┘\n";
        
        // Run setUp if exists
        if (method_exists($class, 'setUp')) {
            $class->setUp();
        }
        
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'test') === 0) {
                $this->runTest($class, $method->getName());
            }
        }
        
        // Run tearDown if exists
        if (method_exists($class, 'tearDown')) {
            $class->tearDown();
        }
        
        echo "\n";
    }
    
    private function runTest($class, string $method): void
    {
        $this->total++;
        $testName = $this->formatTestName($method);
        
        try {
            $result = $class->$method();
            
            if ($result === true) {
                $this->passed++;
                echo "  ✓ {$testName}\n";
                $this->results[] = ['test' => $testName, 'status' => 'passed'];
            } else {
                $this->failed++;
                echo "  ✗ {$testName} - FAILED\n";
                if (is_string($result)) {
                    echo "    └─ {$result}\n";
                }
                $this->results[] = ['test' => $testName, 'status' => 'failed', 'message' => $result];
            }
        } catch (\Exception $e) {
            $this->failed++;
            echo "  ✗ {$testName} - ERROR\n";
            echo "    └─ " . $e->getMessage() . "\n";
            $this->results[] = ['test' => $testName, 'status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function formatTestName(string $method): string
    {
        $name = str_replace('test', '', $method);
        $name = preg_replace('/([A-Z])/', ' $1', $name);
        return trim($name);
    }
    
    private function printSummary(): void
    {
        $percentage = $this->total > 0 ? round(($this->passed / $this->total) * 100, 1) : 0;
        $status = $this->failed === 0 ? '✓ ALL TESTS PASSED' : '✗ SOME TESTS FAILED';
        $statusColor = $this->failed === 0 ? 'green' : 'red';
        
        echo "══════════════════════════════════════════════════════════════\n";
        echo "                        TEST SUMMARY                          \n";
        echo "══════════════════════════════════════════════════════════════\n";
        echo "  Total Tests:  {$this->total}\n";
        echo "  Passed:       {$this->passed}\n";
        echo "  Failed:       {$this->failed}\n";
        echo "  Success Rate: {$percentage}%\n";
        echo "──────────────────────────────────────────────────────────────\n";
        echo "  {$status}\n";
        echo "══════════════════════════════════════════════════════════════\n\n";
    }
    
    // Assertion helpers
    public static function assertEquals($expected, $actual, string $message = ''): bool
    {
        if ($expected === $actual) {
            return true;
        }
        return $message ?: "Expected '{$expected}' but got '{$actual}'";
    }
    
    public static function assertTrue($value, string $message = ''): bool
    {
        if ($value === true) {
            return true;
        }
        return $message ?: "Expected true but got false";
    }
    
    public static function assertFalse($value, string $message = ''): bool
    {
        if ($value === false) {
            return true;
        }
        return $message ?: "Expected false but got true";
    }
    
    public static function assertNotNull($value, string $message = ''): bool
    {
        if ($value !== null) {
            return true;
        }
        return $message ?: "Expected non-null value";
    }
    
    public static function assertNull($value, string $message = ''): bool
    {
        if ($value === null) {
            return true;
        }
        return $message ?: "Expected null value";
    }
    
    public static function assertCount(int $expected, $array, string $message = ''): bool
    {
        $count = is_array($array) ? count($array) : 0;
        if ($count === $expected) {
            return true;
        }
        return $message ?: "Expected count {$expected} but got {$count}";
    }
    
    public static function assertGreaterThan(int $expected, $actual, string $message = ''): bool
    {
        if ($actual > $expected) {
            return true;
        }
        return $message ?: "Expected greater than {$expected} but got {$actual}";
    }
    
    public static function assertArrayHasKey(string $key, array $array, string $message = ''): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }
        return $message ?: "Array does not have key '{$key}'";
    }
    
    public static function assertContains($needle, $haystack, string $message = ''): bool
    {
        if (is_array($haystack) && in_array($needle, $haystack)) {
            return true;
        }
        if (is_string($haystack) && strpos($haystack, $needle) !== false) {
            return true;
        }
        return $message ?: "Value not found in array/string";
    }
}

