<?php
// Use environment variables when present; fall back to current production values.
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: 3306;
$dbName = getenv('DB_DATABASE') ?: 'ithmpwus_ithm_cms';
$dbUser = getenv('DB_USERNAME') ?: 'ithmpwus_ztdcp';
$dbPass = getenv('DB_PASSWORD') ?: 'u2AIg*F4?~.+o;e!';

return [
    'host' => $dbHost,
    'port' => (int)$dbPort,
    'database' => $dbName,
    'username' => $dbUser,
    'password' => $dbPass,
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];
