<?php
define('BASE_URL', 'https://cms.ithm.edu.pk');
define('ITEMS_PER_PAGE', 20);
define('UPLOADS_PATH', __DIR__ . '/../storage/uploads');
define('LOGS_PATH', __DIR__ . '/../storage/logs');
define('CSRF_TOKEN_NAME', 'csrf_token');

// Define core paths and environment
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('VIEWS_PATH', ROOT_PATH . '/resources/views');

// Role IDs (must match DB roles)
define('ROLE_SYSTEM_ADMIN', 1);
define('ROLE_MAIN_CAMPUS_ADMIN', 2);
define('ROLE_SUB_CAMPUS_ADMIN', 3);
define('ROLE_STUDENT', 4);

// Admission document types used in admin & student flows
if (!defined('DOCUMENT_TYPES')) {
    define('DOCUMENT_TYPES', [
        'photo',
        'cnic_front',
        'cnic_back',
        'matric_certificate',
        'inter_certificate',
    ]);
}
