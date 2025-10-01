# ITHM College Management System - Complete Requirements Specification

## Technology Stack
- **Frontend**: HTML5, Tailwind CSS 3.x, Vanilla JavaScript (ES6+)
- **Backend**: PHP 8+ (Laravel 10.x (MVC Architecture))
- **Database**: MySQL 8.0 / MariaDB 10.x
- **Server**: XAMPP (Apache, PHP, MySQL)
- **PDF Generation**: TCPDF Library
- **File Upload**: Native PHP with validation

## Project Structure
```
ithm-system/
├── config/
│   ├── database.php          # Database connection
│   ├── constants.php         # System constants
│   └── config.php           # General configuration
├── includes/
│   ├── header.php           # Common header with Tailwind
│   ├── footer.php           # Common footer with JS
│   ├── auth.php             # Authentication functions
│   ├── functions.php        # Utility functions
│   └── validation.php       # Input validation
├── assets/
│   ├── css/
│   │   └── custom.css      # Custom styles
│   ├── js/
│   │   ├── main.js         # Core JavaScript functions
│   │   ├── components.js   # Component-based loading
│   │   └── ajax.js         # AJAX operations
│   └── images/
├── uploads/
│   ├── photos/             # Student photos
│   ├── documents/          # Application documents
│   └── payment_proofs/     # Payment receipts
├── components/
│   ├── dashboard/          # Dashboard components
│   ├── forms/              # Form components
│   └── tables/             # Table components
├── api/
│   ├── auth.php            # Authentication API
│   ├── applications.php    # Application management API
│   ├── files.php           # File operations API
│   └── users.php           # User management API
├── modules/
│   ├── auth/               # Authentication module
│   ├── student/            # Student module
│   ├── admin/              # Admin module
│   ├── accounts/           # Accounts module
│   └── teacher/            # Teacher module
├── pdf/
│   ├── admission_form.php  # PDF generation
│   └── fee_voucher.php     # Voucher generation
└── index.php               # Entry point
```

## Database Schema

### Table: users
```sql
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
```

### Table: campuses
```sql
CREATE TABLE campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(15),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Table: courses
```sql
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
```

### Table: applications
```sql
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
```

### Table: documents
```sql
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
```

### Table: payments
```sql
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
```

## User Roles and Permissions

### Super Admin
- Full system access
- User management (create, edit, delete all users)
- Campus management (add, edit, delete campuses)
- Course management (add, edit, delete courses)
- System settings and configuration
- View all data across all campuses
- Generate system reports

### Admin
- Campus-specific administration
- Application management (review, approve, reject)
- Student onboarding and roll number assignment
- View applications for their campus only
- Generate fee vouchers
- Manage course fees and schedules
- Generate campus reports

### Accounts
- Fee management and collection
- Payment verification and tracking
- Generate fee vouchers and receipts
- View payment reports
- Manage fee structures
- Handle refunds and adjustments
- Access financial data only

### Teacher
- View enrolled students
- Access student academic records
- Update attendance and grades
- Generate academic reports
- Limited access to student contact information

### Student
- Register and create account
- Submit admission applications
- Upload required documents
- View application status
- Download fee vouchers
- Upload payment proofs
- View fee status and payment history

## User Flow Specifications

### 1. Authentication Flows

#### 1.1 Student Registration Flow
**Route**: `/modules/auth/register.php`
```
Step 1: Registration Form
- First Name, Last Name (required, 2-50 characters)
- Email (required, unique, valid format)
- Username (required, unique, 3-20 characters, alphanumeric)
- Phone (required, Pakistani format validation)
- Password (required, min 8 characters, 1 uppercase, 1 number)
- Confirm Password (must match)
- Terms & Conditions acceptance (required checkbox)

Step 2: Email Verification
- Send verification email with unique token
- Store user with status 'pending' until verified
- Email link: /modules/auth/verify.php?token={token}

Step 3: Account Activation
- Verify token and activate account
- Redirect to login with success message
```

#### 1.2 User Login Flow
**Route**: `/modules/auth/login.php`
```
Step 1: Login Form
- Email/Username field (required)
- Password field (required)
- Remember Me checkbox (optional)
- "Forgot Password?" link

Step 2: Authentication
- Validate credentials against database
- Check account status (active/inactive/suspended)
- Create session with user data
- Update last_login timestamp

Step 3: Role-based Redirect
- Super Admin → /modules/admin/super_dashboard.php
- Admin → /modules/admin/dashboard.php
- Accounts → /modules/accounts/dashboard.php
- Teacher → /modules/teacher/dashboard.php
- Student → /modules/student/dashboard.php
```

#### 1.3 Password Reset Flow
**Route**: `/modules/auth/forgot_password.php`
```
Step 1: Request Form
- Email field (required)
- Submit button

Step 2: Token Generation
- Validate email exists in database
- Generate secure reset token (expires in 1 hour)
- Store token in password_resets table
- Send reset email with link

Step 3: Reset Form (/modules/auth/reset_password.php?token={token})
- Verify token validity and expiration
- New Password field (same validation as registration)
- Confirm Password field
- Submit to update password

Step 4: Password Update
- Hash new password
- Update user password
- Delete used token
- Force logout all sessions
- Redirect to login with success message
```

#### 1.4 Profile Management Flow
**Route**: `/modules/profile/edit.php`
```
Step 1: Profile Display
- Show current user information (non-editable: email, username, role)
- Editable fields: first_name, last_name, phone
- Profile photo upload section
- Password change section (separate)

Step 2: Profile Update
- Validate changed fields
- Check phone uniqueness (if changed)
- Update database with new information
- Show success/error messages

Step 3: Password Change (/modules/profile/change_password.php)
- Current Password field (required, verify against database)
- New Password field (same validation as registration)
- Confirm New Password field
- Update password with proper hashing
```

### 2. Application Management Flows

#### 2.1 Student Application Flow
**Route**: `/modules/student/apply.php`
```
Step 1: Check Eligibility
- Verify student has no pending/active application
- Check if admissions are open
- Display course options with fees

Step 2: Multi-Step Form
Step 2a: Personal Information
- Auto-populate from profile where possible
- Additional fields: father_name, cnic, date_of_birth, gender, address
- Client-side validation for CNIC format
- Age validation (minimum 16 years)

Step 2b: Educational Information  
- education_level (dropdown)
- institution (text, required)
- passing_year (dropdown, last 10 years)
- percentage (number, 0-100 range)

Step 2c: Guardian Information
- guardian_name, guardian_cnic, guardian_phone, guardian_relation
- CNIC format validation
- Phone format validation

Step 2d: Course Selection
- Display available courses with fees
- Show course duration and campus
- Calculate and display total fees

Step 2e: Document Upload
- Student Photo (JPEG/PNG, max 2MB, required)
- CNIC Copy (PDF/JPEG, max 5MB, required)
- Educational Certificates (PDF, max 5MB each, required)
- Real-time validation and preview

Step 2f: Review & Submit
- Display all entered information
- Show uploaded documents
- Declaration checkbox
- Final submission with tracking ID generation
```

#### 2.2 Admin Application Review Flow
**Route**: `/modules/admin/applications.php`
```
Step 1: Applications List
- Filterable table (status, course, date range)
- Search by student name/tracking ID
- Pagination (25 records per page)
- Sortable columns

Step 2: Application Details (/modules/admin/view_application.php?id={id})
- Complete application information display
- Document viewer/downloader
- Status change form
- Internal notes section
- History/timeline of changes

Step 3: Status Management
- Dropdown for status change
- Reason/comments field (required for rejection)
- Email notification to student
- Log status change with admin details
```

### 3. Fee Management Flows

#### 3.1 Fee Voucher Generation Flow
**Route**: `/modules/accounts/generate_voucher.php`
```
Step 1: Student Selection
- Search accepted students
- Filter by course/campus
- Display student details and course fees

Step 2: Voucher Details
- Fee type selection (admission, tuition, etc.)
- Amount calculation (auto from course or manual)
- Due date selection
- Payment instructions
- Generate unique voucher number

Step 3: Voucher Creation
- Save to payments table
- Generate PDF voucher
- Send email to student
- Download option for admin
```

#### 3.2 Payment Processing Flow
**Route**: `/modules/student/payments.php`
```
Step 1: View Vouchers
- List all generated vouchers
- Show status (pending, paid, overdue)
- Download voucher PDFs
- Upload payment proof option

Step 2: Payment Proof Upload
- Select voucher to pay
- Upload receipt/proof (PDF/JPEG, max 5MB)
- Add payment details (date, method, reference)
- Submit for admin verification

Step 3: Admin Verification (/modules/accounts/verify_payments.php)
- List payments awaiting verification
- View uploaded proof documents
- Approve/reject with comments
- Update payment status
- Send confirmation email to student
```

### 4. User Management Flows (Admin Only)

#### 4.1 Create User Flow
**Route**: `/modules/admin/create_user.php`
```
Step 1: User Type Selection
- Select role (admin, accounts, teacher, student)
- Show role permissions information

Step 2: User Information
- Basic details (name, email, username, phone)
- Role-specific fields (campus assignment, etc.)
- Generate temporary password option
- Email welcome message checkbox

Step 3: Account Creation
- Validate all inputs
- Create user account
- Send welcome email with login details
- Redirect to users list with success message
```

#### 4.2 User Management Flow
**Route**: `/modules/admin/users.php`
```
Step 1: Users List
- Filterable by role, campus, status
- Search functionality
- Sortable columns
- Bulk actions (activate/deactivate)

Step 2: Edit User (/modules/admin/edit_user.php?id={id})
- Update user information
- Change role/permissions
- Reset password
- Activate/deactivate account
- View login history

Step 3: User Profile View
- Complete user information
- Activity history
- Login logs
- Associated data (applications for students)
```

## Session Management

### Session Structure
```php
$_SESSION = [
    'user_id' => 123,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'role' => 'student',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'campus_id' => 1,
    'last_activity' => time(),
    'login_time' => time(),
    'is_verified' => true
];
```

### Session Security
- Auto-logout after 30 minutes inactivity
- Regenerate session ID on login
- Validate session on each request
- Clear sensitive data on logout

## Error Handling for User Flows

### Form Validation Errors
- Client-side: Real-time validation with Tailwind error classes
- Server-side: Return JSON with field-specific errors
- Display errors below respective fields
- Prevent form submission until all errors resolved

### Authentication Errors
- Invalid credentials: "Invalid email or password"
- Account inactive: "Your account is not active. Please contact admin."
- Account suspended: "Your account has been suspended. Contact support."
- Too many attempts: "Too many login attempts. Try again in 15 minutes."

### File Upload Errors
- File too large: "File size must be less than 5MB"
- Invalid type: "Only PDF, JPEG, and PNG files are allowed"
- Upload failed: "Upload failed. Please try again."
- Missing required file: "This document is required"

## Core Components

#### File: includes/auth.php
```php
<?php
class Auth {
    // Login validation
    public static function login($username, $password) {}
    
    // Session management
    public static function createSession($user_data) {}
    public static function destroySession() {}
    public static function isLoggedIn() {}
    public static function getCurrentUser() {}
    
    // Role verification
    public static function hasRole($required_role) {}
    public static function requireRole($required_role) {}
    public static function checkPermission($permission) {}
    
    // Password management
    public static function hashPassword($password) {}
    public static function verifyPassword($password, $hash) {}
}
?>
```

#### File: assets/js/auth.js
```javascript
class AuthManager {
    static async login(credentials) {
        // AJAX login request
    }
    
    static logout() {
        // Clear session and redirect
    }
    
    static checkSession() {
        // Verify active session
    }
}
```

### 2. Form Component

#### File: components/forms/application-form.php
```php
<?php
class ApplicationForm {
    public function renderStep($step_number) {
        // Render form step with validation
    }
    
    public function validateStep($step_number, $data) {
        // Server-side validation
    }
    
    public function saveStep($step_number, $data) {
        // Save step data to session/database
    }
}
?>
```

#### File: assets/js/components.js
```javascript
class FormWizard {
    constructor(formId) {
        this.currentStep = 1;
        this.totalSteps = 6;
        this.formData = {};
    }
    
    nextStep() {
        // Move to next step with validation
    }
    
    previousStep() {
        // Move to previous step
    }
    
    validateCurrentStep() {
        // Client-side validation
    }
    
    async saveStep() {
        // AJAX save step data
    }
    
    async submitForm() {
        // Final form submission
    }
}
```

### 3. Dashboard Component

#### File: components/dashboard/stats-widget.php
```php
<?php
class StatsWidget {
    private $user_role;
    
    public function __construct($user_role) {
        $this->user_role = $user_role;
    }
    
    public function getApplicationStats() {
        // Return application statistics based on role
    }
    
    public function getPaymentStats() {
        // Return payment statistics
    }
    
    public function render() {
        // Render dashboard widget
    }
}
?>
```

#### File: assets/js/dashboard.js
```javascript
class Dashboard {
    constructor(userRole) {
        this.userRole = userRole;
        this.widgets = [];
    }
    
    async loadStats() {
        // Load dashboard statistics
    }
    
    async refreshWidget(widgetId) {
        // Refresh specific widget
    }
    
    setupRealTimeUpdates() {
        // Setup periodic updates
    }
}
```

### 4. File Upload Component

#### File: api/files.php
```php
<?php
class FileUploadHandler {
    private $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    private $max_size = 5242880; // 5MB
    
    public function uploadFile($file, $application_id, $file_type) {
        // Validate and upload file
    }
    
    public function validateFile($file) {
        // File validation logic
    }
    
    public function generateFileName($original_name) {
        // Generate unique file name
    }
    
    public function deleteFile($file_id) {
        // Delete file from system
    }
}
?>
```

#### File: assets/js/upload.js
```javascript
class FileUploader {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = options;
        this.files = [];
    }
    
    setupDropZone() {
        // Drag and drop functionality
    }
    
    validateFile(file) {
        // Client-side file validation
    }
    
    async uploadFile(file, applicationId, fileType) {
        // AJAX file upload with progress
    }
    
    showProgress(percentage) {
        // Show upload progress
    }
}
```

### 5. Data Table Component

#### File: components/tables/applications-table.php
```php
<?php
class ApplicationsTable {
    private $user_role;
    private $campus_id;
    
    public function __construct($user_role, $campus_id = null) {
        $this->user_role = $user_role;
        $this->campus_id = $campus_id;
    }
    
    public function getApplications($filters = [], $limit = 25, $offset = 0) {
        // Get applications with filters and pagination
    }
    
    public function renderTable($applications) {
        // Render HTML table with Tailwind classes
    }
    
    public function getFilterOptions() {
        // Get available filter options
    }
}
?>
```

#### File: assets/js/datatable.js
```javascript
class DataTable {
    constructor(tableId, options = {}) {
        this.table = document.getElementById(tableId);
        this.options = options;
        this.currentPage = 1;
        this.filters = {};
        this.sortBy = null;
        this.sortOrder = 'asc';
    }
    
    async loadData() {
        // Load table data via AJAX
    }
    
    applyFilters(filters) {
        // Apply filters and reload data
    }
    
    sortColumn(column) {
        // Sort table by column
    }
    
    changePage(page) {
        // Navigate to specific page
    }
    
    setupSearch() {
        // Setup real-time search
    }
}
```

## API Endpoints

### Authentication API (api/auth.php)
```php
// POST /api/auth.php?action=login
// POST /api/auth.php?action=logout
// GET /api/auth.php?action=check_session
// POST /api/auth.php?action=change_password
```

### Applications API (api/applications.php)
```php
// GET /api/applications.php - Get applications list
// POST /api/applications.php - Create new application
// PUT /api/applications.php?id={id} - Update application
// GET /api/applications.php?id={id} - Get single application
// POST /api/applications.php?action=change_status&id={id} - Change status
```

### Files API (api/files.php)
```php
// POST /api/files.php - Upload file
// GET /api/files.php?id={id} - Download file
// DELETE /api/files.php?id={id} - Delete file
// GET /api/files.php?application_id={id} - Get application files
```

### Users API (api/users.php)
```php
// GET /api/users.php - Get users list (admin only)
// POST /api/users.php - Create new user (admin only)
// PUT /api/users.php?id={id} - Update user (admin only)
// DELETE /api/users.php?id={id} - Delete user (super admin only)
```

## Error Handling Standards

### PHP Error Response Format
```php
function sendJsonResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}
```

### JavaScript Error Handling
```javascript
class ErrorHandler {
    static showError(message, type = 'error') {
        // Display error toast/notification
    }
    
    static handleAjaxError(xhr) {
        // Handle AJAX errors consistently
    }
    
    static logError(error) {
        // Log errors for debugging
    }
}
```

## Validation Rules

### Input Validation (includes/validation.php)
```php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateCNIC($cnic) {
        return preg_match('/^\d{5}-\d{7}-\d$/', $cnic);
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^(\+92|0)?[0-9]{10}$/', $phone);
    }
    
    public static function validateRequired($value) {
        return !empty(trim($value));
    }
    
    public static function validateLength($value, $min, $max) {
        $length = strlen(trim($value));
        return $length >= $min && $length <= $max;
    }
}
```

## Security Measures

### SQL Injection Prevention
```php
// Always use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = ?");
$stmt->execute([$email, 'active']);
```

### XSS Prevention
```php
// Sanitize all output
function safe_output($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
```

### File Upload Security
```php
// Validate file type and size
// Store files outside web root
// Generate unique file names
// Scan for malware (if available)
```

### Session Security
```php
// Configure secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
```

## Development Guidelines

### File Naming Conventions
- PHP files: lowercase with underscores (user_management.php)
- CSS classes: kebab-case (form-container)
- JavaScript functions: camelCase (getUserData)
- Database tables: lowercase with underscores (user_applications)

### Code Structure
- All PHP files must start with <?php (no short tags)
- Always use prepared statements for database queries
- Implement proper error handling in all functions
- Use meaningful variable and function names
- Comment complex logic and business rules

### Testing Requirements
- Test all user roles and permissions
- Validate all form inputs with invalid data
- Test file upload with various file types
- Test database operations with edge cases
- Verify responsive design on different devices

This specification ensures a bug-free, secure, and maintainable application when implemented correctly by Cursor AI.

## Demo & Development Features

### Demo Login System
**Route**: `/modules/auth/login.php`

#### One-Click Demo Logins
Below the main login form, add a demo section:

```html
<div class="mt-8 p-4 bg-blue-50 rounded-lg border">
    <h3 class="text-sm font-medium text-blue-900 mb-3">🎯 Demo Access (One-Click Login)</h3>
    <div class="grid grid-cols-2 gap-2">
        <button onclick="demoLogin('super_admin')" class="demo-btn bg-red-500 text-white">
            Super Admin
        </button>
        <button onclick="demoLogin('admin')" class="demo-btn bg-purple-500 text-white">
            Campus Admin
        </button>
        <button onclick="demoLogin('accounts')" class="demo-btn bg-green-500 text-white">
            Accounts Officer
        </button>
        <button onclick="demoLogin('teacher')" class="demo-btn bg-blue-500 text-white">
            Teacher
        </button>
        <button onclick="demoLogin('student')" class="demo-btn bg-orange-500 text-white">
            Student
        </button>
    </div>
</div>
```

#### Demo User Credentials
**File**: `database/demo_data.sql`
```sql
-- Demo Users (passwords: demo123)
INSERT INTO users (username, email, password, role, first_name, last_name, phone, campus_id, status) VALUES
('super_admin', 'super@ithm.edu.pk', '$2y$10$hash...', 'super_admin', 'Super', 'Administrator', '03001234567', NULL, 'active'),
('admin_demo', 'admin@ithm.edu.pk', '$2y$10$hash...', 'admin', 'Campus', 'Administrator', '03001234568', 1, 'active'),
('accounts_demo', 'accounts@ithm.edu.pk', '$2y$10$hash...', 'accounts', 'Finance', 'Officer', '03001234569', 1, 'active'),
('teacher_demo', 'teacher@ithm.edu.pk', '$2y$10$hash...', 'teacher', 'Professor', 'Ahmad', '03001234570', 1, 'active'),
('student_demo', 'student@ithm.edu.pk', '$2y$10$hash...', 'student', 'Ahmed', 'Khan', '03001234571', 1, 'active');
```

#### JavaScript Demo Login Function
**File**: `assets/js/demo.js`
```javascript
const demoCredentials = {
    super_admin: { email: 'super@ithm.edu.pk', password: 'demo123' },
    admin: { email: 'admin@ithm.edu.pk', password: 'demo123' },
    accounts: { email: 'accounts@ithm.edu.pk', password: 'demo123' },
    teacher: { email: 'teacher@ithm.edu.pk', password: 'demo123' },
    student: { email: 'student@ithm.edu.pk', password: 'demo123' }
};

async function demoLogin(role) {
    const credentials = demoCredentials[role];
    document.getElementById('email').value = credentials.email;
    document.getElementById('password').value = credentials.password;
    
    // Auto-submit form
    document.getElementById('loginForm').submit();
}
```

### Demo Data Structure

#### Demo Campuses
```sql
INSERT INTO campuses (name, code, address, phone, status) VALUES
('Main Campus Lahore', 'MCL', 'Gulberg III, Lahore, Pakistan', '042-35714001', 'active'),
('Karachi Campus', 'KC', 'Defence Phase 5, Karachi, Pakistan', '021-35342001', 'active'),
('Islamabad Campus', 'IC', 'Blue Area, Islamabad, Pakistan', '051-26110001', 'active');
```

#### Demo Courses with Realistic Fees
```sql
INSERT INTO courses (name, code, duration, campus_id, admission_fee, tuition_fee, security_deposit, other_charges, status) VALUES
-- Main Campus Lahore
('Hotel Management', 'HM-2024', '2 Years', 1, 25000.00, 150000.00, 10000.00, 5000.00, 'active'),
('Tourism Management', 'TM-2024', '2 Years', 1, 20000.00, 120000.00, 10000.00, 5000.00, 'active'),
('Culinary Arts', 'CA-2024', '18 Months', 1, 30000.00, 180000.00, 15000.00, 7000.00, 'active'),
('Event Management', 'EM-2024', '1 Year', 1, 15000.00, 80000.00, 5000.00, 3000.00, 'active'),
-- Karachi Campus  
('Hotel Management', 'HM-K-2024', '2 Years', 2, 25000.00, 140000.00, 10000.00, 5000.00, 'active'),
('Tourism Management', 'TM-K-2024', '2 Years', 2, 20000.00, 110000.00, 10000.00, 5000.00, 'active'),
-- Islamabad Campus
('Hotel Management', 'HM-I-2024', '2 Years', 3, 25000.00, 160000.00, 10000.00, 5000.00, 'active');
```

#### Demo Student Applications with Varied Statuses
```sql
INSERT INTO applications (tracking_id, user_id, course_id, status, first_name, last_name, father_name, cnic, date_of_birth, gender, phone, address, education_level, institution, passing_year, percentage, guardian_name, guardian_cnic, guardian_phone, guardian_relation, roll_number, admin_notes) VALUES
-- Recent Applications (Last 30 days)
('ITHM2024001', 5, 1, 'onboarded', 'Ahmed', 'Khan', 'Muhammad Khan', '35202-1234567-1', '2002-05-15', 'male', '03001234571', 'House 123, Model Town, Lahore', 'Intermediate', 'Government College Lahore', 2022, 85.50, 'Muhammad Khan', '35202-1234567-2', '03009876543', 'father', 'HM24001', 'Excellent academic record'),
('ITHM2024002', 6, 2, 'accepted', 'Fatima', 'Ali', 'Ali Hassan', '35202-2345678-2', '2003-08-22', 'female', '03002345672', 'Street 45, Johar Town, Lahore', 'Intermediate', 'Lahore College for Women', 2023, 78.25, 'Ali Hassan', '35202-2345678-3', '03008765432', 'father', NULL, 'Good candidate for tourism'),
('ITHM2024003', 7, 1, 'under_review', 'Hassan', 'Ahmed', 'Ahmed Malik', '35202-3456789-3', '2001-12-10', 'male', '03003456673', 'Block B, DHA, Lahore', 'Bachelor', 'University of Punjab', 2023, 65.75, 'Ahmed Malik', '35202-3456789-4', '03007654321', 'father', NULL, 'Under document verification'),
('ITHM2024004', 8, 3, 'pending', 'Ayesha', 'Butt', 'Umer Butt', '35202-4567890-4', '2004-03-18', 'female', '03004567674', 'Garden Town, Lahore', 'Intermediate', 'Kinnaird College', 2024, 92.00, 'Umer Butt', '35202-4567890-5', '03006543210', 'father', NULL, NULL),
('ITHM2024005', 9, 2, 'rejected', 'Usman', 'Sheikh', 'Tariq Sheikh', '35202-5678901-5', '2000-07-25', 'male', '03005678675', 'Gulshan Ravi, Lahore', 'Matric', 'Government High School', 2020, 55.50, 'Tariq Sheikh', '35202-5678901-6', '03005432109', 'father', NULL, 'Does not meet minimum education requirement'),
-- Older Applications (2-6 months ago)
('ITHM2023045', 10, 1, 'onboarded', 'Zainab', 'Malik', 'Rashid Malik', '35202-6789012-6', '2002-11-30', 'female', '03006789676', 'Wapda Town, Lahore', 'Intermediate', 'Punjab College', 2022, 88.75, 'Rashid Malik', '35202-6789012-7', '03004321098', 'father', 'HM23045', 'Outstanding student'),
('ITHM2023046', 11, 4, 'onboarded', 'Bilal', 'Raza', 'Akram Raza', '35202-7890123-7', '2003-04-12', 'male', '03007890677', 'Samanabad, Lahore', 'Intermediate', 'Government College', 2023, 71.25, 'Akram Raza', '35202-7890123-8', '03003210987', 'father', 'EM23001', 'Good for event management');
```

#### Demo Users for Applications
```sql
INSERT INTO users (username, email, password, role, first_name, last_name, phone, campus_id, status) VALUES
('ahmed_khan', 'ahmed.khan@student.ithm.pk', '$2y$10$hash...', 'student', 'Ahmed', 'Khan', '03001234571', 1, 'active'),
('fatima_ali', 'fatima.ali@student.ithm.pk', '$2y$10$hash...', 'student', 'Fatima', 'Ali', '03002345672', 1, 'active'),
('hassan_ahmed', 'hassan.ahmed@student.ithm.pk', '$2y$10$hash...', 'student', 'Hassan', 'Ahmed', '03003456673', 1, 'active'),
('ayesha_butt', 'ayesha.butt@student.ithm.pk', '$2y$10$hash...', 'student', 'Ayesha', 'Butt', '03004567674', 1, 'active'),
('usman_sheikh', 'usman.sheikh@student.ithm.pk', '$2y$10$hash...', 'student', 'Usman', 'Sheikh', '03005678675', 1, 'active'),
('zainab_malik', 'zainab.malik@student.ithm.pk', '$2y$10$hash...', 'student', 'Zainab', 'Malik', '03006789676', 1, 'active'),
('bilal_raza', 'bilal.raza@student.ithm.pk', '$2y$10$hash...', 'student', 'Bilal', 'Raza', '03007890677', 1, 'active');
```

#### Demo Payment Records
```sql
INSERT INTO payments (application_id, voucher_number, amount, payment_type, status, due_date, payment_date, payment_method, admin_notes) VALUES
(1, 'FV2024001', 25000.00, 'admission_fee', 'paid', '2024-01-15', '2024-01-12', 'Bank Transfer', 'Admission fee paid on time'),
(1, 'FV2024002', 37500.00, 'tuition_fee', 'paid', '2024-03-01', '2024-02-28', 'Cash', 'First semester fee'),
(2, 'FV2024003', 20000.00, 'admission_fee', 'pending', '2024-02-01', NULL, NULL, 'Admission fee voucher generated'),
(3, 'FV2024004', 25000.00, 'admission_fee', 'pending', '2024-01-30', NULL, NULL, 'Pending document verification'),
(6, 'FV2023101', 25000.00, 'admission_fee', 'paid', '2023-08-15', '2023-08-10', 'Online', 'Previous batch admission'),
(7, 'FV2023102', 15000.00, 'admission_fee', 'paid', '2023-09-01', '2023-08-30', 'Bank Draft', 'Event management course');
```

## Role-Based Dashboard Specifications

### Super Admin Dashboard
**Route**: `/modules/admin/super_dashboard.php`

#### Widgets Layout (4 columns, 3 rows)
```html
<!-- Row 1: System Overview -->
<div class="grid grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Total Users</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $total_users ?></p>
        <p class="text-sm text-gray-500">Across all roles</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Active Campuses</h3>
        <p class="text-3xl font-bold text-green-600"><?= $active_campuses ?></p>
        <p class="text-sm text-gray-500">Operating locations</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Total Applications</h3>
        <p class="text-3xl font-bold text-purple-600"><?= $total_applications ?></p>
        <p class="text-sm text-gray-500">All time submissions</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">System Health</h3>
        <p class="text-3xl font-bold text-green-600">98.5%</p>
        <p class="text-sm text-gray-500">Uptime</p>
    </div>
</div>

<!-- Row 2: Campus Performance -->
<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow col-span-2">
        <h3 class="text-lg font-semibold mb-4">Campus Applications Overview</h3>
        <canvas id="campusChart"></canvas>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Role Distribution</h3>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>Students</span>
                <span class="font-bold"><?= $role_counts['student'] ?></span>
            </div>
            <div class="flex justify-between">
                <span>Teachers</span>
                <span class="font-bold"><?= $role_counts['teacher'] ?></span>
            </div>
            <div class="flex justify-between">
                <span>Admin Staff</span>
                <span class="font-bold"><?= $role_counts['admin'] + $role_counts['accounts'] ?></span>
            </div>
        </div>
    </div>
</div>
```

#### Super Admin Sidebar Options
```html
<nav class="mt-8">
    <a href="/modules/admin/super_dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <a href="/modules/admin/manage_users.php" class="nav-item">👥 User Management</a>
    <a href="/modules/admin/manage_campuses.php" class="nav-item">🏢 Campus Management</a>
    <a href="/modules/admin/system_settings.php" class="nav-item">⚙️ System Settings</a>
    <a href="/modules/admin/view_all_applications.php" class="nav-item">📋 All Applications</a>
    <a href="/modules/admin/financial_overview.php" class="nav-item">💰 Financial Overview</a>
    <a href="/modules/admin/reports.php" class="nav-item">📊 System Reports</a>
    <a href="/modules/admin/backup.php" class="nav-item">💾 Backup & Restore</a>
    <a href="/modules/admin/activity_logs.php" class="nav-item">📝 Activity Logs</a>
</nav>
```

### Campus Admin Dashboard
**Route**: `/modules/admin/dashboard.php`

#### Widgets Layout (Campus-Specific Data)
```html
<!-- Row 1: Campus Overview -->
<div class="grid grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Pending Reviews</h3>
        <p class="text-3xl font-bold text-orange-600"><?= $pending_applications ?></p>
        <p class="text-sm text-gray-500">Need attention</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">This Month</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $monthly_applications ?></p>
        <p class="text-sm text-gray-500">New applications</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Accepted</h3>
        <p class="text-3xl font-bold text-green-600"><?= $accepted_applications ?></p>
        <p class="text-sm text-gray-500">Ready for onboarding</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Onboarded</h3>
        <p class="text-3xl font-bold text-purple-600"><?= $onboarded_students ?></p>
        <p class="text-sm text-gray-500">Active students</p>
    </div>
</div>

<!-- Recent Applications Table -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Recent Applications</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_applications as $app): ?>
                <tr>
                    <td class="px-6 py-4"><?= $app['tracking_id'] ?></td>
                    <td class="px-6 py-4"><?= $app['first_name'] . ' ' . $app['last_name'] ?></td>
                    <td class="px-6 py-4"><?= $app['course_name'] ?></td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-<?= $app['status'] ?>"><?= ucwords(str_replace('_', ' ', $app['status'])) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="viewApplication(<?= $app['id'] ?>)" class="btn-sm btn-primary">View</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

#### Admin Sidebar Options
```html
<nav class="mt-8">
    <a href="/modules/admin/dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <a href="/modules/admin/applications.php" class="nav-item">📋 Applications</a>
    <a href="/modules/admin/students.php" class="nav-item">🎓 Students</a>
    <a href="/modules/admin/courses.php" class="nav-item">📚 Courses</a>
    <a href="/modules/admin/reports.php" class="nav-item">📊 Reports</a>
    <a href="/modules/admin/settings.php" class="nav-item">⚙️ Settings</a>
    <a href="/modules/profile/edit.php" class="nav-item">👤 Profile</a>
</nav>
```

### Accounts Officer Dashboard
**Route**: `/modules/accounts/dashboard.php`

#### Financial Widgets
```html
<!-- Row 1: Financial Overview -->
<div class="grid grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Outstanding</h3>
        <p class="text-3xl font-bold text-red-600">Rs. <?= number_format($outstanding_amount) ?></p>
        <p class="text-sm text-gray-500">Pending payments</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">This Month</h3>
        <p class="text-3xl font-bold text-green-600">Rs. <?= number_format($monthly_collection) ?></p>
        <p class="text-sm text-gray-500">Collected</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Overdue</h3>
        <p class="text-3xl font-bold text-orange-600"><?= $overdue_payments ?></p>
        <p class="text-sm text-gray-500">Payment vouchers</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Today's Collection</h3>
        <p class="text-3xl font-bold text-blue-600">Rs. <?= number_format($daily_collection) ?></p>
        <p class="text-sm text-gray-500">Verified payments</p>
    </div>
</div>

<!-- Payment Verification Queue -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Payment Verification Queue</h3>
        <p class="text-sm text-gray-600">Payments awaiting verification: <?= count($pending_verifications) ?></p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pending_verifications as $payment): ?>
                <tr>
                    <td class="px-6 py-4"><?= $payment['voucher_number'] ?></td>
                    <td class="px-6 py-4"><?= $payment['student_name'] ?></td>
                    <td class="px-6 py-4">Rs. <?= number_format($payment['amount']) ?></td>
                    <td class="px-6 py-4"><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                    <td class="px-6 py-4">
                        <button onclick="verifyPayment(<?= $payment['id'] ?>)" class="btn-sm btn-green">Verify</button>
                        <button onclick="viewProof(<?= $payment['id'] ?>)" class="btn-sm btn-blue">View Proof</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
```

#### Accounts Sidebar Options
```html
<nav class="mt-8">
    <a href="/modules/accounts/dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <a href="/modules/accounts/payments.php" class="nav-item">💳 Payments</a>
    <a href="/modules/accounts/generate_voucher.php" class="nav-item">📄 Generate Voucher</a>
    <a href="/modules/accounts/verify_payments.php" class="nav-item">✅ Verify Payments</a>
    <a href="/modules/accounts/fee_structure.php" class="nav-item">💰 Fee Structure</a>
    <a href="/modules/accounts/reports.php" class="nav-item">📊 Financial Reports</a>
    <a href="/modules/accounts/collections.php" class="nav-item">📈 Collections</a>
    <a href="/modules/profile/edit.php" class="nav-item">👤 Profile</a>
</nav>
```

### Teacher Dashboard
**Route**: `/modules/teacher/dashboard.php`

#### Academic Widgets
```html
<!-- Row 1: Teaching Overview -->
<div class="grid grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">My Students</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $total_students ?></p>
        <p class="text-sm text-gray-500">Currently teaching</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Courses</h3>
        <p class="text-3xl font-bold text-green-600"><?= $assigned_courses ?></p>
        <p class="text-sm text-gray-500">This semester</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Attendance</h3>
        <p class="text-3xl font-bold text-orange-600"><?= $attendance_percentage ?>%</p>
        <p class="text-sm text-gray-500">Average class attendance</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibent text-gray-900">Pending Grades</h3>
        <p class="text-3xl font-bold text-red-600"><?= $pending_grades ?></p>
        <p class="text-sm text-gray-500">Assignments to grade</p>
    </div>
</div>

<!-- Recent Student Activities -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Recent Student Activities</h3>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <?php foreach($recent_activities as $activity): ?>
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                        <?= substr($activity['student_name'], 0, 1) ?>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900"><?= $activity['student_name'] ?></p>
                    <p class="text-sm text-gray-600"><?= $activity['activity'] ?></p>
                </div>
                <div class="text-sm text-gray-500">
                    <?= timeAgo($activity['created_at']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
```

#### Teacher Sidebar Options
```html
<nav class="mt-8">
    <a href="/modules/teacher/dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <a href="/modules/teacher/students.php" class="nav-item">🎓 My Students</a>
    <a href="/modules/teacher/courses.php" class="nav-item">📚 My Courses</a>
    <a href="/modules/teacher/attendance.php" class="nav-item">📊 Attendance</a>
    <a href="/modules/teacher/grades.php" class="nav-item">📝 Grades</a>
    <a href="/modules/teacher/schedule.php" class="nav-item">📅 Class Schedule</a>
    <a href="/modules/teacher/resources.php" class="nav-item">📁 Resources</a>
    <a href="/modules/profile/edit.php" class="nav-item">👤 Profile</a>
</nav>
```

### Student Dashboard
**Route**: `/modules/student/dashboard.php`

#### Student-Specific Widgets
```html
<!-- Row 1: Student Overview -->
<div class="grid grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Application Status</h3>
        <p class="text-3xl font-bold <?= getStatusColor($application_status) ?>"><?= ucwords(str_replace('_', ' ', $application_status)) ?></p>
        <p class="text-sm text-gray-500">Current status</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Outstanding Fee</h3>
        <p class="text-3xl font-bold <?= $outstanding_fee > 0 ? 'text-red-600' : 'text-green-600' ?>">
            Rs. <?= number_format($outstanding_fee) ?>
        </p>
        <p class="text-sm text-gray-500">Pending payments</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
        <p class="text-3xl font-bold <?= $documents_complete ? 'text-green-600' : 'text-orange-600' ?>">
            <?= $uploaded_documents ?>/<?= $required_documents ?>
        </p>
        <p class="text-sm text-gray-500"><?= $documents_complete ? 'All uploaded' : 'Incomplete' ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibent text-gray-900">Roll Number</h3>
        <p class="text-3xl font-bold <?= $roll_number ? 'text-blue-600' : 'text-gray-400' ?>">
            <?= $roll_number ?: 'Not Assigned' ?>
        </p>
        <p class="text-sm text-gray-500"><?= $roll_number ? 'Student ID' : 'Pending onboarding' ?></p>
    </div>
</div>

<!-- Application Timeline -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Application Timeline</h3>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Application Submitted</div>
                    <div class="text-sm text-gray-500"><?= date('M d, Y', strtotime($application_date)) ?></div>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 <?= in_array($application_status, ['under_review', 'accepted', 'onboarded']) ? 'bg-green-500' : 'bg-gray-300' ?> rounded-full flex items-center justify-center">
                        <?php if(in_array($application_status, ['under_review', 'accepted', 'onboarded'])): ?>
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                            </svg>
                        <?php else: ?>
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Under Review</div>
                    <div class="text-sm text-gray-500"><?= in_array($application_status, ['under_review', 'accepted', 'onboarded']) ? 'Application being processed' : 'Pending review' ?></div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 <?= in_array($application_status, ['accepted', 'onboarded']) ? 'bg-green-500' : 'bg-gray-300' ?> rounded-full flex items-center justify-center">
                        <?php if(in_array($application_status, ['accepted', 'onboarded'])): ?>
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                            </svg>
                        <?php else: ?>
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Accepted</div>
                    <div class="text-sm text-gray-500"><?= $application_status === 'accepted' ? 'Congratulations! Pay fees to proceed' : 'Pending acceptance' ?></div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 <?= $application_status === 'onboarded' ? 'bg-green-500' : 'bg-gray-300' ?> rounded-full flex items-center justify-center">
                        <?php if($application_status === 'onboarded'): ?>
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                            </svg>
                        <?php else: ?>
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">Onboarded</div>
                    <div class="text-sm text-gray-500"><?= $application_status === 'onboarded' ? 'Welcome to ITHM!' : 'Final step' ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Payment Vouchers</h3>
        <?php if(empty($payment_vouchers)): ?>
            <p class="text-gray-500">No payment vouchers generated yet.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach($payment_vouchers as $voucher): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium"><?= $voucher['voucher_number'] ?></p>
                        <p class="text-sm text-gray-600">Rs. <?= number_format($voucher['amount']) ?> - <?= ucwords(str_replace('_', ' ', $voucher['payment_type'])) ?></p>
                        <p class="text-xs text-gray-500">Due: <?= date('M d, Y', strtotime($voucher['due_date'])) ?></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-1 text-xs rounded <?= getPaymentStatusColor($voucher['status']) ?>">
                            <?= ucwords($voucher['status']) ?>
                        </span>
                        <div class="mt-2">
                            <button onclick="downloadVoucher(<?= $voucher['id'] ?>)" class="text-blue-600 text-xs hover:underline">Download</button>
                            <?php if($voucher['status'] === 'pending'): ?>
                                <button onclick="uploadProof(<?= $voucher['id'] ?>)" class="text-green-600 text-xs hover:underline ml-2">Upload Proof</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
        <div class="space-y-3">
            <?php foreach($recent_activities as $activity): ?>
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div>
                    <p class="text-sm font-medium"><?= $activity['title'] ?></p>
                    <p class="text-xs text-gray-600"><?= $activity['description'] ?></p>
                    <p class="text-xs text-gray-500"><?= timeAgo($activity['created_at']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
```

#### Student Sidebar Options
```html
<nav class="mt-8">
    <a href="/modules/student/dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <?php if(!$has_application): ?>
        <a href="/modules/student/apply.php" class="nav-item">📝 Apply Now</a>
    <?php else: ?>
        <a href="/modules/student/application_status.php" class="nav-item">📋 Application Status</a>
    <?php endif; ?>
    <a href="/modules/student/documents.php" class="nav-item">📄 My Documents</a>
    <a href="/modules/student/payments.php" class="nav-item">💳 Payments</a>
    <a href="/modules/student/fee_vouchers.php" class="nav-item">📄 Fee Vouchers</a>
    <?php if($application_status === 'onboarded'): ?>
        <a href="/modules/student/academic.php" class="nav-item">📚 Academic Info</a>
        <a href="/modules/student/schedule.php" class="nav-item">📅 Class Schedule</a>
    <?php endif; ?>
    <a href="/modules/profile/edit.php" class="nav-item">👤 Profile</a>
</nav>
```

## Database Functions for Dashboard Data

### File: `includes/dashboard_functions.php`
```php
<?php
class DashboardData {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Super Admin Dashboard Data
    public function getSuperAdminStats() {
        $stats = [];
        
        // Total users
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Active campuses
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM campuses WHERE status = 'active'");
        $stats['active_campuses'] = $stmt->fetchColumn();
        
        // Total applications
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM applications");
        $stats['total_applications'] = $stmt->fetchColumn();
        
        // Role distribution
        $stmt = $this->pdo->query("SELECT role, COUNT(*) as count FROM users WHERE status = 'active' GROUP BY role");
        $stats['role_counts'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Campus applications breakdown
        $stmt = $this->pdo->query("
            SELECT c.name, COUNT(a.id) as application_count
            FROM campuses c
            LEFT JOIN courses co ON c.id = co.campus_id
            LEFT JOIN applications a ON co.id = a.course_id
            WHERE c.status = 'active'
            GROUP BY c.id, c.name
        ");
        $stats['campus_applications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    // Admin Dashboard Data
    public function getAdminStats($campus_id) {
        $stats = [];
        
        // Pending applications
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM applications a
            JOIN courses c ON a.course_id = c.id
            WHERE c.campus_id = ? AND a.status = 'pending'
        ");
        $stmt->execute([$campus_id]);
        $stats['pending_applications'] = $stmt->fetchColumn();
        
        // Monthly applications
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM applications a
            JOIN courses c ON a.course_id = c.id
            WHERE c.campus_id = ? AND DATE_FORMAT(a.created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
        ");
        $stmt->execute([$campus_id]);
        $stats['monthly_applications'] = $stmt->fetchColumn();
        
        // Accepted applications
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM applications a
            JOIN courses c ON a.course_id = c.id
            WHERE c.campus_id = ? AND a.status = 'accepted'
        ");
        $stmt->execute([$campus_id]);
        $stats['accepted_applications'] = $stmt->fetchColumn();
        
        // Onboarded students
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM applications a
            JOIN courses c ON a.course_id = c.id
            WHERE c.campus_id = ? AND a.status = 'onboarded'
        ");
        $stmt->execute([$campus_id]);
        $stats['onboarded_students'] = $stmt->fetchColumn();
        
        // Recent applications
        $stmt = $this->pdo->prepare("
            SELECT a.*, c.name as course_name
            FROM applications a
            JOIN courses c ON a.course_id = c.id
            WHERE c.campus_id = ?
            ORDER BY a.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$campus_id]);
        $stats['recent_applications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    // Accounts Dashboard Data
    public function getAccountsStats($campus_id = null) {
        $stats = [];
        $whereClause = $campus_id ? "WHERE c.campus_id = ?" : "";
        $params = $campus_id ? [$campus_id] : [];
        
        // Outstanding amount
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(p.amount), 0) FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN courses c ON a.course_id = c.id
            $whereClause AND p.status = 'pending'
        ");
        $stmt->execute($params);
        $stats['outstanding_amount'] = $stmt->fetchColumn();
        
        // Monthly collection
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(p.amount), 0) FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN courses c ON a.course_id = c.id
            $whereClause AND p.status = 'paid' 
            AND DATE_FORMAT(p.payment_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
        ");
        $stmt->execute($params);
        $stats['monthly_collection'] = $stmt->fetchColumn();
        
        // Overdue payments
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN courses c ON a.course_id = c.id
            $whereClause AND p.status = 'pending' AND p.due_date < CURDATE()
        ");
        $stmt->execute($params);
        $stats['overdue_payments'] = $stmt->fetchColumn();
        
        // Daily collection
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(p.amount), 0) FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN courses c ON a.course_id = c.id
            $whereClause AND p.status = 'paid' AND DATE(p.payment_date) = CURDATE()
        ");
        $stmt->execute($params);
        $stats['daily_collection'] = $stmt->fetchColumn();
        
        // Pending verifications
        $stmt = $this->pdo->prepare("
            SELECT p.*, CONCAT(a.first_name, ' ', a.last_name) as student_name
            FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN courses c ON a.course_id = c.id
            $whereClause AND p.proof_document IS NOT NULL AND p.status = 'pending'
            ORDER BY p.updated_at DESC
            LIMIT 10
        ");
        $stmt->execute($params);
        $stats['pending_verifications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    // Student Dashboard Data
    public function getStudentStats($user_id) {
        $stats = [];
        
        // Get application
        $stmt = $this->pdo->prepare("
            SELECT a.*, c.name as course_name 
            FROM applications a 
            LEFT JOIN courses c ON a.course_id = c.id 
            WHERE a.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($application) {
            $stats['application_status'] = $application['status'];
            $stats['application_date'] = $application['created_at'];
            $stats['roll_number'] = $application['roll_number'];
            
            // Outstanding fees
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(amount), 0) FROM payments 
                WHERE application_id = ? AND status = 'pending'
            ");
            $stmt->execute([$application['id']]);
            $stats['outstanding_fee'] = $stmt->fetchColumn();
            
            // Documents count
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM documents WHERE application_id = ?");
            $stmt->execute([$application['id']]);
            $stats['uploaded_documents'] = $stmt->fetchColumn();
            $stats['required_documents'] = 3; // photo, cnic, certificates
            $stats['documents_complete'] = $stats['uploaded_documents'] >= $stats['required_documents'];
            
            // Payment vouchers
            $stmt = $this->pdo->prepare("
                SELECT * FROM payments 
                WHERE application_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$application['id']]);
            $stats['payment_vouchers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } else {
            $stats['application_status'] = null;
            $stats['outstanding_fee'] = 0;
            $stats['uploaded_documents'] = 0;
            $stats['required_documents'] = 3;
            $stats['documents_complete'] = false;
            $stats['payment_vouchers'] = [];
            $stats['roll_number'] = null;
        }
        
        // Recent activities (mock data for demo)
        $stats['recent_activities'] = [
            ['title' => 'Application Submitted', 'description' => 'Your application has been submitted successfully', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
            ['title' => 'Documents Verified', 'description' => 'All required documents have been verified', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['title' => 'Payment Voucher Generated', 'description' => 'Admission fee voucher has been generated', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))]
        ];
        
        return $stats;
    }
}

// Helper functions
function getStatusColor($status) {
    switch($status) {
        case 'pending': return 'text-yellow-600';
        case 'under_review': return 'text-blue-600';
        case 'accepted': return 'text-green-600';
        case 'rejected': return 'text-red-600';
        case 'onboarded': return 'text-purple-600';
        default: return 'text-gray-600';
    }
}

function getPaymentStatusColor($status) {
    switch($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'paid': return 'bg-green-100 text-green-800';
        case 'overdue': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time<1)? 1 : $time;
    $tokens = array (
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
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    }
}
?>
```

## Installation & Demo Setup Script

### File: `install_demo.php`
```php
<?php
// Demo installation script
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ithm_db");
    $pdo->exec("USE ithm_db");
    
    // Read and execute SQL files
    $sqlFiles = [
        'database/schema.sql',
        'database/demo_data.sql'
    ];
    
    foreach($sqlFiles as $file) {
        if(file_exists($file)) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            echo "✅ Executed: $file\n";
        }
    }
    
    echo "🎉 Demo installation completed successfully!\n";
    echo "🎯 You can now access demo accounts:\n";
    echo "   Super Admin: super@ithm.edu.pk / demo123\n";
    echo "   Admin: admin@ithm.edu.pk / demo123\n";
    echo "   Accounts: accounts@ithm.edu.pk / demo123\n";
    echo "   Teacher: teacher@ithm.edu.pk / demo123\n";
    echo "   Student: student@ithm.edu.pk / demo123\n";
    
} catch(PDOException $e) {
    echo "❌ Installation failed: " . $e->getMessage() . "\n";
}
?>
```

This comprehensive demo system provides:

1. **One-Click Demo Logins** for all user roles
2. **Realistic Demo Data** with varied application statuses and payment records  
3. **Role-Specific Dashboards** with appropriate widgets and navigation
4. **Complete Database Functions** to populate dashboard statistics
5. **Professional UI Components** with Tailwind CSS styling
6. **Interactive Features** with proper data relationships

The system is now demonstration-ready with realistic data that shows the full workflow from student application to onboarding across all user roles.# ITHM College Management System - Complete Requirements Specification

## Technology Stack
- **Frontend**: HTML5, Tailwind CSS 3.x, Vanilla JavaScript (ES6+)
- **Backend**: PHP 8+ (Laravel 10.x (MVC Architecture))
- **Database**: MySQL 8.0 / MariaDB 10.x
- **Server**: XAMPP (Apache, PHP, MySQL)
- **PDF Generation**: TCPDF Library
- **File Upload**: Native PHP with validation

## Project Structure
```
ithm-system/
├── config/
│   ├── database.php          # Database connection
│   ├── constants.php         # System constants
│   └── config.php           # General configuration
├── includes/
│   ├── header.php           # Common header with Tailwind
│   ├── footer.php           # Common footer with JS
│   ├── auth.php             # Authentication functions
│   ├── functions.php        # Utility functions
│   └── validation.php       # Input validation
├── assets/
│   ├── css/
│   │   └── custom.css      # Custom styles
│   ├── js/
│   │   ├── main.js         # Core JavaScript functions
│   │   ├── components.js   # Component-based loading
│   │   └── ajax.js         # AJAX operations
│   └── images/
├── uploads/
│   ├── photos/             # Student photos
│   ├── documents/          # Application documents
│   └── payment_proofs/     # Payment receipts
├── components/
│   ├── dashboard/          # Dashboard components
│   ├── forms/              # Form components
│   └── tables/             # Table components
├── api/
│   ├── auth.php            # Authentication API
│   ├── applications.php    # Application management API
│   ├── files.php           # File operations API
│   └── users.php           # User management API
├── modules/
│   ├── auth/               # Authentication module
│   ├── student/            # Student module
│   ├── admin/              # Admin module
│   ├── accounts/           # Accounts module
│   └── teacher/            # Teacher module
├── pdf/
│   ├── admission_form.php  # PDF generation
│   └── fee_voucher.php     # Voucher generation
└── index.php               # Entry point
```

## Database Schema

### Table: users
```sql
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
```

### Table: campuses
```sql
CREATE TABLE campuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(15),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Table: courses
```sql
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
```

### Table: applications
```sql
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
```

### Table: documents
```sql
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
```

### Table: payments
```sql
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
```

## User Roles and Permissions

### Super Admin
- Full system access
- User management (create, edit, delete all users)
- Campus management (add, edit, delete campuses)
- Course management (add, edit, delete courses)
- System settings and configuration
- View all data across all campuses
- Generate system reports

### Admin
- Campus-specific administration
- Application management (review, approve, reject)
- Student onboarding and roll number assignment
- View applications for their campus only
- Generate fee vouchers
- Manage course fees and schedules
- Generate campus reports

### Accounts
- Fee management and collection
- Payment verification and tracking
- Generate fee vouchers and receipts
- View payment reports
- Manage fee structures
- Handle refunds and adjustments
- Access financial data only

### Teacher
- View enrolled students
- Access student academic records
- Update attendance and grades
- Generate academic reports
- Limited access to student contact information

### Student
- Register and create account
- Submit admission applications
- Upload required documents
- View application status
- Download fee vouchers
- Upload payment proofs
- View fee status and payment history

## User Flow Specifications

### 1. Authentication Flows

#### 1.1 Student Registration Flow
**Route**: `/modules/auth/register.php`
```
Step 1: Registration Form
- First Name, Last Name (required, 2-50 characters)
- Email (required, unique, valid format)
- Username (required, unique, 3-20 characters, alphanumeric)
- Phone (required, Pakistani format validation)
- Password (required, min 8 characters, 1 uppercase, 1 number)
- Confirm Password (must match)
- Terms & Conditions acceptance (required checkbox)

Step 2: Email Verification
- Send verification email with unique token
- Store user with status 'pending' until verified
- Email link: /modules/auth/verify.php?token={token}

Step 3: Account Activation
- Verify token and activate account
- Redirect to login with success message
```

#### 1.2 User Login Flow
**Route**: `/modules/auth/login.php`
```
Step 1: Login Form
- Email/Username field (required)
- Password field (required)
- Remember Me checkbox (optional)
- "Forgot Password?" link

Step 2: Authentication
- Validate credentials against database
- Check account status (active/inactive/suspended)
- Create session with user data
- Update last_login timestamp

Step 3: Role-based Redirect
- Super Admin → /modules/admin/super_dashboard.php
- Admin → /modules/admin/dashboard.php
- Accounts → /modules/accounts/dashboard.php
- Teacher → /modules/teacher/dashboard.php
- Student → /modules/student/dashboard.php
```

#### 1.3 Password Reset Flow
**Route**: `/modules/auth/forgot_password.php`
```
Step 1: Request Form
- Email field (required)
- Submit button

Step 2: Token Generation
- Validate email exists in database
- Generate secure reset token (expires in 1 hour)
- Store token in password_resets table
- Send reset email with link

Step 3: Reset Form (/modules/auth/reset_password.php?token={token})
- Verify token validity and expiration
- New Password field (same validation as registration)
- Confirm Password field
- Submit to update password

Step 4: Password Update
- Hash new password
- Update user password
- Delete used token
- Force logout all sessions
- Redirect to login with success message
```

#### 1.4 Profile Management Flow
**Route**: `/modules/profile/edit.php`
```
Step 1: Profile Display
- Show current user information (non-editable: email, username, role)
- Editable fields: first_name, last_name, phone
- Profile photo upload section
- Password change section (separate)

Step 2: Profile Update
- Validate changed fields
- Check phone uniqueness (if changed)
- Update database with new information
- Show success/error messages

Step 3: Password Change (/modules/profile/change_password.php)
- Current Password field (required, verify against database)
- New Password field (same validation as registration)
- Confirm New Password field
- Update password with proper hashing
```

### 2. Application Management Flows

#### 2.1 Student Application Flow
**Route**: `/modules/student/apply.php`
```
Step 1: Check Eligibility
- Verify student has no pending/active application
- Check if admissions are open
- Display course options with fees

Step 2: Multi-Step Form
Step 2a: Personal Information
- Auto-populate from profile where possible
- Additional fields: father_name, cnic, date_of_birth, gender, address
- Client-side validation for CNIC format
- Age validation (minimum 16 years)

Step 2b: Educational Information  
- education_level (dropdown)
- institution (text, required)
- passing_year (dropdown, last 10 years)
- percentage (number, 0-100 range)

Step 2c: Guardian Information
- guardian_name, guardian_cnic, guardian_phone, guardian_relation
- CNIC format validation
- Phone format validation

Step 2d: Course Selection
- Display available courses with fees
- Show course duration and campus
- Calculate and display total fees

Step 2e: Document Upload
- Student Photo (JPEG/PNG, max 2MB, required)
- CNIC Copy (PDF/JPEG, max 5MB, required)
- Educational Certificates (PDF, max 5MB each, required)
- Real-time validation and preview

Step 2f: Review & Submit
- Display all entered information
- Show uploaded documents
- Declaration checkbox
- Final submission with tracking ID generation
```

#### 2.2 Admin Application Review Flow
**Route**: `/modules/admin/applications.php`
```
Step 1: Applications List
- Filterable table (status, course, date range)
- Search by student name/tracking ID
- Pagination (25 records per page)
- Sortable columns

Step 2: Application Details (/modules/admin/view_application.php?id={id})
- Complete application information display
- Document viewer/downloader
- Status change form
- Internal notes section
- History/timeline of changes

Step 3: Status Management
- Dropdown for status change
- Reason/comments field (required for rejection)
- Email notification to student
- Log status change with admin details
```

### 3. Fee Management Flows

#### 3.1 Fee Voucher Generation Flow
**Route**: `/modules/accounts/generate_voucher.php`
```
Step 1: Student Selection
- Search accepted students
- Filter by course/campus
- Display student details and course fees

Step 2: Voucher Details
- Fee type selection (admission, tuition, etc.)
- Amount calculation (auto from course or manual)
- Due date selection
- Payment instructions
- Generate unique voucher number

Step 3: Voucher Creation
- Save to payments table
- Generate PDF voucher
- Send email to student
- Download option for admin
```

#### 3.2 Payment Processing Flow
**Route**: `/modules/student/payments.php`
```
Step 1: View Vouchers
- List all generated vouchers
- Show status (pending, paid, overdue)
- Download voucher PDFs
- Upload payment proof option

Step 2: Payment Proof Upload
- Select voucher to pay
- Upload receipt/proof (PDF/JPEG, max 5MB)
- Add payment details (date, method, reference)
- Submit for admin verification

Step 3: Admin Verification (/modules/accounts/verify_payments.php)
- List payments awaiting verification
- View uploaded proof documents
- Approve/reject with comments
- Update payment status
- Send confirmation email to student
```

### 4. User Management Flows (Admin Only)

#### 4.1 Create User Flow
**Route**: `/modules/admin/create_user.php`
```
Step 1: User Type Selection
- Select role (admin, accounts, teacher, student)
- Show role permissions information

Step 2: User Information
- Basic details (name, email, username, phone)
- Role-specific fields (campus assignment, etc.)
- Generate temporary password option
- Email welcome message checkbox

Step 3: Account Creation
- Validate all inputs
- Create user account
- Send welcome email with login details
- Redirect to users list with success message
```

#### 4.2 User Management Flow
**Route**: `/modules/admin/users.php`
```
Step 1: Users List
- Filterable by role, campus, status
- Search functionality
- Sortable columns
- Bulk actions (activate/deactivate)

Step 2: Edit User (/modules/admin/edit_user.php?id={id})
- Update user information
- Change role/permissions
- Reset password
- Activate/deactivate account
- View login history

Step 3: User Profile View
- Complete user information
- Activity history
- Login logs
- Associated data (applications for students)
```

## Session Management

### Session Structure
```php
$_SESSION = [
    'user_id' => 123,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'role' => 'student',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'campus_id' => 1,
    'last_activity' => time(),
    'login_time' => time(),
    'is_verified' => true
];
```

### Session Security
- Auto-logout after 30 minutes inactivity
- Regenerate session ID on login
- Validate session on each request
- Clear sensitive data on logout

## Error Handling for User Flows

### Form Validation Errors
- Client-side: Real-time validation with Tailwind error classes
- Server-side: Return JSON with field-specific errors
- Display errors below respective fields
- Prevent form submission until all errors resolved

### Authentication Errors
- Invalid credentials: "Invalid email or password"
- Account inactive: "Your account is not active. Please contact admin."
- Account suspended: "Your account has been suspended. Contact support."
- Too many attempts: "Too many login attempts. Try again in 15 minutes."

### File Upload Errors
- File too large: "File size must be less than 5MB"
- Invalid type: "Only PDF, JPEG, and PNG files are allowed"
- Upload failed: "Upload failed. Please try again."
- Missing required file: "This document is required"

## Core Components

#### File: includes/auth.php
```php
<?php
class Auth {
    // Login validation
    public static function login($username, $password) {}
    
    // Session management
    public static function createSession($user_data) {}
    public static function destroySession() {}
    public static function isLoggedIn() {}
    public static function getCurrentUser() {}
    
    // Role verification
    public static function hasRole($required_role) {}
    public static function requireRole($required_role) {}
    public static function checkPermission($permission) {}
    
    // Password management
    public static function hashPassword($password) {}
    public static function verifyPassword($password, $hash) {}
}
?>
```

#### File: assets/js/auth.js
```javascript
class AuthManager {
    static async login(credentials) {
        // AJAX login request
    }
    
    static logout() {
        // Clear session and redirect
    }
    
    static checkSession() {
        // Verify active session
    }
}
```

### 2. Form Component

#### File: components/forms/application-form.php
```php
<?php
class ApplicationForm {
    public function renderStep($step_number) {
        // Render form step with validation
    }
    
    public function validateStep($step_number, $data) {
        // Server-side validation
    }
    
    public function saveStep($step_number, $data) {
        // Save step data to session/database
    }
}
?>
```

#### File: assets/js/components.js
```javascript
class FormWizard {
    constructor(formId) {
        this.currentStep = 1;
        this.totalSteps = 6;
        this.formData = {};
    }
    
    nextStep() {
        // Move to next step with validation
    }
    
    previousStep() {
        // Move to previous step
    }
    
    validateCurrentStep() {
        // Client-side validation
    }
    
    async saveStep() {
        // AJAX save step data
    }
    
    async submitForm() {
        // Final form submission
    }
}
```

### 3. Dashboard Component

#### File: components/dashboard/stats-widget.php
```php
<?php
class StatsWidget {
    private $user_role;
    
    public function __construct($user_role) {
        $this->user_role = $user_role;
    }
    
    public function getApplicationStats() {
        // Return application statistics based on role
    }
    
    public function getPaymentStats() {
        // Return payment statistics
    }
    
    public function render() {
        // Render dashboard widget
    }
}
?>
```

#### File: assets/js/dashboard.js
```javascript
class Dashboard {
    constructor(userRole) {
        this.userRole = userRole;
        this.widgets = [];
    }
    
    async loadStats() {
        // Load dashboard statistics
    }
    
    async refreshWidget(widgetId) {
        // Refresh specific widget
    }
    
    setupRealTimeUpdates() {
        // Setup periodic updates
    }
}
```

### 4. File Upload Component

#### File: api/files.php
```php
<?php
class FileUploadHandler {
    private $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    private $max_size = 5242880; // 5MB
    
    public function uploadFile($file, $application_id, $file_type) {
        // Validate and upload file
    }
    
    public function validateFile($file) {
        // File validation logic
    }
    
    public function generateFileName($original_name) {
        // Generate unique file name
    }
    
    public function deleteFile($file_id) {
        // Delete file from system
    }
}
?>
```

#### File: assets/js/upload.js
```javascript
class FileUploader {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = options;
        this.files = [];
    }
    
    setupDropZone() {
        // Drag and drop functionality
    }
    
    validateFile(file) {
        // Client-side file validation
    }
    
    async uploadFile(file, applicationId, fileType) {
        // AJAX file upload with progress
    }
    
    showProgress(percentage) {
        // Show upload progress
    }
}
```

### 5. Data Table Component

#### File: components/tables/applications-table.php
```php
<?php
class ApplicationsTable {
    private $user_role;
    private $campus_id;
    
    public function __construct($user_role, $campus_id = null) {
        $this->user_role = $user_role;
        $this->campus_id = $campus_id;
    }
    
    public function getApplications($filters = [], $limit = 25, $offset = 0) {
        // Get applications with filters and pagination
    }
    
    public function renderTable($applications) {
        // Render HTML table with Tailwind classes
    }
    
    public function getFilterOptions() {
        // Get available filter options
    }
}
?>
```

#### File: assets/js/datatable.js
```javascript
class DataTable {
    constructor(tableId, options = {}) {
        this.table = document.getElementById(tableId);
        this.options = options;
        this.currentPage = 1;
        this.filters = {};
        this.sortBy = null;
        this.sortOrder = 'asc';
    }
    
    async loadData() {
        // Load table data via AJAX
    }
    
    applyFilters(filters) {
        // Apply filters and reload data
    }
    
    sortColumn(column) {
        // Sort table by column
    }
    
    changePage(page) {
        // Navigate to specific page
    }
    
    setupSearch() {
        // Setup real-time search
    }
}
```

## API Endpoints

### Authentication API (api/auth.php)
```php
// POST /api/auth.php?action=login
// POST /api/auth.php?action=logout
// GET /api/auth.php?action=check_session
// POST /api/auth.php?action=change_password
```

### Applications API (api/applications.php)
```php
// GET /api/applications.php - Get applications list
// POST /api/applications.php - Create new application
// PUT /api/applications.php?id={id} - Update application
// GET /api/applications.php?id={id} - Get single application
// POST /api/applications.php?action=change_status&id={id} - Change status
```

### Files API (api/files.php)
```php
// POST /api/files.php - Upload file
// GET /api/files.php?id={id} - Download file
// DELETE /api/files.php?id={id} - Delete file
// GET /api/files.php?application_id={id} - Get application files
```

### Users API (api/users.php)
```php
// GET /api/users.php - Get users list (admin only)
// POST /api/users.php - Create new user (admin only)
// PUT /api/users.php?id={id} - Update user (admin only)
// DELETE /api/users.php?id={id} - Delete user (super admin only)
```

## Error Handling Standards

### PHP Error Response Format
```php
function sendJsonResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}
```

### JavaScript Error Handling
```javascript
class ErrorHandler {
    static showError(message, type = 'error') {
        // Display error toast/notification
    }
    
    static handleAjaxError(xhr) {
        // Handle AJAX errors consistently
    }
    
    static logError(error) {
        // Log errors for debugging
    }
}
```

## Validation Rules

### Input Validation (includes/validation.php)
```php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validateCNIC($cnic) {
        return preg_match('/^\d{5}-\d{7}-\d$/', $cnic);
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^(\+92|0)?[0-9]{10}$/', $phone);
    }
    
    public static function validateRequired($value) {
        return !empty(trim($value));
    }
    
    public static function validateLength($value, $min, $max) {
        $length = strlen(trim($value));
        return $length >= $min && $length <= $max;
    }
}
```

## Security Measures

### SQL Injection Prevention
```php
// Always use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = ?");
$stmt->execute([$email, 'active']);
```

### XSS Prevention
```php
// Sanitize all output
function safe_output($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
```

### File Upload Security
```php
// Validate file type and size
// Store files outside web root
// Generate unique file names
// Scan for malware (if available)
```

### Session Security
```php
// Configure secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
```

## Development Guidelines

### File Naming Conventions
- PHP files: lowercase with underscores (user_management.php)
- CSS classes: kebab-case (form-container)
- JavaScript functions: camelCase (getUserData)
- Database tables: lowercase with underscores (user_applications)

### Code Structure
- All PHP files must start with <?php (no short tags)
- Always use prepared statements for database queries
- Implement proper error handling in all functions
- Use meaningful variable and function names
- Comment complex logic and business rules

### Testing Requirements
- Test all user roles and permissions
- Validate all form inputs with invalid data
- Test file upload with various file types
- Test database operations with edge cases
- Verify responsive design on different devices

This specification ensures a bug-free, secure, and maintainable application when implemented correctly by Cursor AI.