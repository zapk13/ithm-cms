<?php
/**
 * ITHM CMS Entry Point
 * All requests are routed through this file
 */

// Start output buffering
ob_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Check if this is an API request
$isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false || 
                strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/api/') !== false;

// For API requests, suppress HTML error output and set error handler
if ($isApiRequest) {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    
    // Set custom error handler for API requests
    set_error_handler(function($errno, $errstr, $errfile, $errline) use ($isApiRequest) {
        if ($isApiRequest && ob_get_level() > 0) {
            ob_clean();
        }
        
        if ($isApiRequest) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => APP_ENV === 'development' ? "$errstr in $errfile:$errline" : 'An error occurred'
            ]);
            exit;
        }
        
        return false; // Let PHP handle it normally for non-API requests
    });
}

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';
    
    // Check if class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get relative class name
    $relativeClass = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Load the file if it exists
    if (file_exists($file)) {
        require $file;
    }
});

// Start session
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

// Load and dispatch routes
try {
    $router = require ROOT_PATH . '/routes/web.php';
    $router->dispatch();
} catch (\Throwable $e) {
    // Handle any uncaught exceptions
    if ($isApiRequest) {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => APP_ENV === 'development' ? $e->getMessage() : 'An error occurred'
        ]);
    } else {
        if (APP_ENV === 'development') {
            echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
        } else {
            http_response_code(500);
            include VIEWS_PATH . '/errors/500.php';
        }
    }
    exit;
}

// End output buffering
ob_end_flush();

