<?php
/**
 * Storage File Server
 * Serves files from the storage directory
 */

// Get the requested file path
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/storage/';

if (strpos($requestUri, $basePath) !== 0) {
    http_response_code(404);
    exit('Not Found');
}

$relativePath = substr($requestUri, strlen($basePath));
$filePath = __DIR__ . '/' . $relativePath;

// Security: Prevent directory traversal
$realPath = realpath($filePath);
$storageDir = realpath(__DIR__);

if (!$realPath || strpos($realPath, $storageDir) !== 0) {
    http_response_code(404);
    exit('Not Found');
}

// Check if file exists
if (!file_exists($realPath) || is_dir($realPath)) {
    http_response_code(404);
    exit('Not Found');
}

// Get mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $realPath);
finfo_close($finfo);

// Set headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($realPath));
header('Cache-Control: public, max-age=31536000');

// Output file
readfile($realPath);
exit;

