# Phase 1: Project Setup & Database Foundation

## Pre-Phase Checklist
- [ ] XAMPP installed and running (Apache, PHP 8+, MySQL 8+)
- [ ] Code editor with PHP support (VS Code/Cursor)
- [ ] Git repository initialized
- [ ] Basic understanding of Laravel MVC architecture

## Phase Objectives
1. Set up project structure following Laravel MVC patterns
2. Create and configure database
3. Implement basic configuration system
4. Set up development environment
5. Create initial database schema

## Implementation Steps

### Step 1: Project Structure Setup
**Duration**: 2-3 hours

Create the following directory structure:
```
ithm-system/
├── config/
│   ├── database.php
│   ├── constants.php
│   └── config.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── auth.php
│   ├── functions.php
│   └── validation.php
├── assets/
│   ├── css/
│   │   └── custom.css
│   ├── js/
│   │   ├── main.js
│   │   ├── components.js
│   │   └── ajax.js
│   └── images/
├── uploads/
│   ├── photos/
│   ├── documents/
│   └── payment_proofs/
├── components/
│   ├── dashboard/
│   ├── forms/
│   └── tables/
├── api/
│   ├── auth.php
│   ├── applications.php
│   ├── files.php
│   └── users.php
├── modules/
│   ├── auth/
│   ├── student/
│   ├── admin/
│   ├── accounts/
│   └── teacher/
├── pdf/
│   ├── admission_form.php
│   └── fee_voucher.php
├── database/
│   ├── schema.sql
│   └── demo_data.sql
└── index.php
```

### Step 2: Database Configuration
**Duration**: 1-2 hours

**File: `config/database.php`**
```php
<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ithm_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
```

**File: `config/constants.php`**
```php
<?php
// System Constants
define('SITE_NAME', 'ITHM College Management System');
define('SITE_URL', 'http://localhost/ithm-cms');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// User Roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN', 'admin');
define('ROLE_ACCOUNTS', 'accounts');
define('ROLE_TEACHER', 'teacher');
define('ROLE_STUDENT', 'student');

// Application Status
define('STATUS_PENDING', 'pending');
define('STATUS_UNDER_REVIEW', 'under_review');
define('STATUS_ACCEPTED', 'accepted');
define('STATUS_REJECTED', 'rejected');
define('STATUS_ONBOARDED', 'onboarded');

// Payment Status
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_PAID', 'paid');
define('PAYMENT_OVERDUE', 'overdue');
define('PAYMENT_CANCELLED', 'cancelled');
?>
```

### Step 3: Database Schema Creation
**Duration**: 2-3 hours

**File: `database/schema.sql`**
```sql
-- Create Database
CREATE DATABASE IF NOT EXISTS ithm_db;
USE ithm_db;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'accounts', 'teacher', 'student') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    campus_id INT,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_role (role),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- Campuses Table
CREATE TABLE campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(15),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    duration VARCHAR(20) NOT NULL,
    campus_id INT NOT NULL,
    admission_fee DECIMAL(10,2) NOT NULL,
    tuition_fee DECIMAL(10,2) NOT NULL,
    security_deposit DECIMAL(10,2) DEFAULT 0,
    other_charges DECIMAL(10,2) DEFAULT 0,
    total_fee DECIMAL(10,2) GENERATED ALWAYS AS (admission_fee + tuition_fee + security_deposit + other_charges) STORED,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campus_id) REFERENCES campuses(id) ON DELETE CASCADE,
    INDEX idx_campus (campus_id),
    INDEX idx_status (status)
);

-- Applications Table
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    status ENUM('pending', 'under_review', 'accepted', 'rejected', 'onboarded') DEFAULT 'pending',
    
    -- Personal Information
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    father_name VARCHAR(50) NOT NULL,
    cnic VARCHAR(15) UNIQUE NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    
    -- Educational Information
    education_level VARCHAR(50) NOT NULL,
    institution VARCHAR(100) NOT NULL,
    passing_year YEAR NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    
    -- Guardian Information
    guardian_name VARCHAR(50) NOT NULL,
    guardian_cnic VARCHAR(15) NOT NULL,
    guardian_phone VARCHAR(15) NOT NULL,
    guardian_relation VARCHAR(20) NOT NULL,
    
    -- System Information
    roll_number VARCHAR(20) NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_tracking (tracking_id),
    INDEX idx_status (status),
    INDEX idx_course (course_id),
    INDEX idx_cnic (cnic)
);

-- Documents Table
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type ENUM('photo', 'cnic', 'certificate', 'payment_proof', 'other') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    INDEX idx_application (application_id),
    INDEX idx_type (file_type)
);

-- Payments Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    voucher_number VARCHAR(20) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('admission_fee', 'tuition_fee', 'security_deposit', 'other') NOT NULL,
    status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
    due_date DATE NOT NULL,
    payment_date DATE NULL,
    payment_method VARCHAR(50) NULL,
    proof_document VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    INDEX idx_voucher (voucher_number),
    INDEX idx_status (status),
    INDEX idx_application (application_id)
);

-- Password Reset Tokens Table
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token)
);
```

### Step 4: Basic Configuration Files
**Duration**: 1-2 hours

**File: `config/config.php`**
```php
<?php
// General Configuration
return [
    'app' => [
        'name' => 'ITHM College Management System',
        'version' => '1.0.0',
        'url' => 'http://localhost/ithm-cms',
        'timezone' => 'Asia/Karachi'
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'ithm_db',
        'username' => 'root',
        'password' => ''
    ],
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => [
            'images' => ['image/jpeg', 'image/png'],
            'documents' => ['application/pdf', 'image/jpeg', 'image/png']
        ],
        'paths' => [
            'photos' => 'uploads/photos/',
            'documents' => 'uploads/documents/',
            'payment_proofs' => 'uploads/payment_proofs/'
        ]
    ],
    'security' => [
        'session_timeout' => 1800, // 30 minutes
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900 // 15 minutes
    ]
];
?>
```

### Step 5: Basic Utility Functions
**Duration**: 2-3 hours

**File: `includes/functions.php`**
```php
<?php
// Utility Functions

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateTrackingId() {
    return 'ITHM' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function generateVoucherNumber() {
    return 'FV' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function formatCurrency($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}

function createUploadDirectories() {
    $directories = [
        'uploads/photos',
        'uploads/documents', 
        'uploads/payment_proofs'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isValidImageType($mimeType) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    return in_array($mimeType, $allowedTypes);
}

function isValidDocumentType($mimeType) {
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    return in_array($mimeType, $allowedTypes);
}
?>
```

### Step 6: Basic HTML Structure
**Duration**: 1-2 hours

**File: `includes/header.php`**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>ITHM College Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-gray-50">
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">ITHM CMS</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        Welcome, <?= $_SESSION['first_name'] ?> <?= $_SESSION['last_name'] ?>
                    </span>
                    <a href="modules/auth/logout.php" class="text-sm text-red-600 hover:text-red-800">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
```

**File: `includes/footer.php`**
```php
    <script src="assets/js/main.js"></script>
    <script src="assets/js/components.js"></script>
    <script src="assets/js/ajax.js"></script>
</body>
</html>
```

### Step 7: Entry Point
**Duration**: 30 minutes

**File: `index.php`**
```php
<?php
session_start();
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Create upload directories
createUploadDirectories();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on role
    switch ($_SESSION['role']) {
        case 'super_admin':
            header('Location: modules/admin/super_dashboard.php');
            break;
        case 'admin':
            header('Location: modules/admin/dashboard.php');
            break;
        case 'accounts':
            header('Location: modules/accounts/dashboard.php');
            break;
        case 'teacher':
            header('Location: modules/teacher/dashboard.php');
            break;
        case 'student':
            header('Location: modules/student/dashboard.php');
            break;
        default:
            header('Location: modules/auth/login.php');
    }
} else {
    header('Location: modules/auth/login.php');
}
exit;
?>
```

## Post-Phase Checklist
- [ ] Database created successfully
- [ ] All tables created with proper relationships
- [ ] Configuration files working
- [ ] Project structure in place
- [ ] Basic entry point functional
- [ ] Upload directories created
- [ ] No PHP errors in basic setup

## Testing Procedures
1. **Database Connection Test**
   ```php
   // Test database connection
   $db = new Database();
   $conn = $db->getConnection();
   if ($conn) {
       echo "Database connection successful";
   }
   ```

2. **Directory Structure Test**
   - Verify all directories exist
   - Check file permissions (755 for directories)
   - Test file creation in upload directories

3. **Configuration Test**
   - Load constants file
   - Test database connection
   - Verify configuration values

## Summary
**Key Achievements:**
- Complete project structure established
- Database schema implemented
- Basic configuration system in place
- Foundation for MVC architecture set

**Next Phase Dependencies:**
- Database must be fully functional
- Configuration system must be working
- Basic project structure must be in place

**Files Created:**
- Complete directory structure
- Database schema (schema.sql)
- Configuration files (database.php, constants.php, config.php)
- Utility functions (functions.php)
- Basic HTML structure (header.php, footer.php)
- Entry point (index.php)

**Estimated Completion Time:** 2-3 days
**Ready for Phase 2:** ✅
