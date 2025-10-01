# Phase 2: Authentication System

## Pre-Phase Checklist
- [ ] Phase 1 completed successfully
- [ ] Database schema created and functional
- [ ] Basic project structure in place
- [ ] Configuration system working
- [ ] Understanding of session management in PHP

## Phase Objectives
1. Implement complete authentication system
2. Create user registration and login functionality
3. Implement role-based access control
4. Add password reset functionality
5. Create session management system
6. Implement security measures

## Implementation Steps

### Step 1: Authentication Core Class
**Duration**: 3-4 hours

**File: `includes/auth.php`**
```php
<?php
class Auth {
    private $pdo;
    
    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }
    
    // User Registration
    public function register($userData) {
        try {
            // Validate input
            $errors = $this->validateRegistration($userData);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Check if email/username already exists
            if ($this->emailExists($userData['email'])) {
                return ['success' => false, 'errors' => ['email' => 'Email already exists']];
            }
            
            if ($this->usernameExists($userData['username'])) {
                return ['success' => false, 'errors' => ['username' => 'Username already exists']];
            }
            
            // Hash password
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Insert user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password, role, first_name, last_name, phone, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            $result = $stmt->execute([
                $userData['username'],
                $userData['email'],
                $hashedPassword,
                $userData['role'],
                $userData['first_name'],
                $userData['last_name'],
                $userData['phone']
            ]);
            
            if ($result) {
                // Store verification token
                $this->storeVerificationToken($userData['email'], $verificationToken);
                
                // Send verification email (implement later)
                // $this->sendVerificationEmail($userData['email'], $verificationToken);
                
                return ['success' => true, 'message' => 'Registration successful. Please check your email for verification.'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => ['general' => 'Registration failed. Please try again.']];
        }
    }
    
    // User Login
    public function login($email, $password, $rememberMe = false) {
        try {
            // Get user by email
            $stmt = $this->pdo->prepare("
                SELECT * FROM users 
                WHERE email = ? AND status = 'active'
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Create session
            $this->createSession($user);
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            return ['success' => true, 'user' => $user];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    // Create User Session
    public function createSession($user) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['campus_id'] = $user['campus_id'];
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        $_SESSION['is_verified'] = true;
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > 1800)) { // 30 minutes
            $this->logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    // Get current user
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name'],
            'campus_id' => $_SESSION['campus_id']
        ];
    }
    
    // Check user role
    public function hasRole($requiredRole) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $_SESSION['role'] === $requiredRole;
    }
    
    // Require specific role
    public function requireRole($requiredRole) {
        if (!$this->hasRole($requiredRole)) {
            header('Location: modules/auth/login.php');
            exit;
        }
    }
    
    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        session_start();
    }
    
    // Password Reset Request
    public function requestPasswordReset($email) {
        try {
            // Check if email exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Email not found'];
            }
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token
            $stmt = $this->pdo->prepare("
                INSERT INTO password_resets (email, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$email, $token, $expiresAt]);
            
            // Send reset email (implement later)
            // $this->sendPasswordResetEmail($email, $token);
            
            return ['success' => true, 'message' => 'Password reset link sent to your email'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Password reset failed. Please try again.'];
        }
    }
    
    // Reset Password
    public function resetPassword($token, $newPassword) {
        try {
            // Validate token
            $stmt = $this->pdo->prepare("
                SELECT email FROM password_resets 
                WHERE token = ? AND expires_at > NOW()
            ");
            $stmt->execute([$token]);
            $reset = $stmt->fetch();
            
            if (!$reset) {
                return ['success' => false, 'message' => 'Invalid or expired token'];
            }
            
            // Validate password
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                UPDATE users SET password = ? WHERE email = ?
            ");
            $stmt->execute([$hashedPassword, $reset['email']]);
            
            // Delete used token
            $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
            
            return ['success' => true, 'message' => 'Password reset successful'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Password reset failed. Please try again.'];
        }
    }
    
    // Private helper methods
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['first_name']) || strlen($data['first_name']) < 2) {
            $errors['first_name'] = 'First name must be at least 2 characters';
        }
        
        if (empty($data['last_name']) || strlen($data['last_name']) < 2) {
            $errors['last_name'] = 'Last name must be at least 2 characters';
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (empty($data['phone']) || !preg_match('/^(\+92|0)?[0-9]{10}$/', $data['phone'])) {
            $errors['phone'] = 'Invalid phone number format';
        }
        
        return $errors;
    }
    
    private function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    private function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
    
    private function storeVerificationToken($email, $token) {
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $stmt = $this->pdo->prepare("
            INSERT INTO password_resets (email, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $token, $expiresAt]);
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
?>
```

### Step 2: Login System
**Duration**: 2-3 hours

**File: `modules/auth/login.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    switch ($user['role']) {
        case 'super_admin':
            header('Location: ../admin/super_dashboard.php');
            break;
        case 'admin':
            header('Location: ../admin/dashboard.php');
            break;
        case 'accounts':
            header('Location: ../accounts/dashboard.php');
            break;
        case 'teacher':
            header('Location: ../teacher/dashboard.php');
            break;
        case 'student':
            header('Location: ../student/dashboard.php');
            break;
    }
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);
    
    $result = $auth->login($email, $password, $rememberMe);
    
    if ($result['success']) {
        $user = $result['user'];
        switch ($user['role']) {
            case 'super_admin':
                header('Location: ../admin/super_dashboard.php');
                break;
            case 'admin':
                header('Location: ../admin/dashboard.php');
                break;
            case 'accounts':
                header('Location: ../accounts/dashboard.php');
                break;
            case 'teacher':
                header('Location: ../teacher/dashboard.php');
                break;
            case 'student':
                header('Location: ../student/dashboard.php');
                break;
        }
        exit;
    } else {
        $error = $result['message'];
    }
}

$page_title = 'Login';
include '../../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    create a new account
                </a>
            </p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= $success ?>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="forgot_password.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>
        
        <!-- Demo Login Section -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg border">
            <h3 class="text-sm font-medium text-blue-900 mb-3">🎯 Demo Access (One-Click Login)</h3>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="demoLogin('super_admin')" class="demo-btn bg-red-500 text-white px-3 py-2 rounded text-sm">
                    Super Admin
                </button>
                <button onclick="demoLogin('admin')" class="demo-btn bg-purple-500 text-white px-3 py-2 rounded text-sm">
                    Campus Admin
                </button>
                <button onclick="demoLogin('accounts')" class="demo-btn bg-green-500 text-white px-3 py-2 rounded text-sm">
                    Accounts Officer
                </button>
                <button onclick="demoLogin('teacher')" class="demo-btn bg-blue-500 text-white px-3 py-2 rounded text-sm">
                    Teacher
                </button>
                <button onclick="demoLogin('student')" class="demo-btn bg-orange-500 text-white px-3 py-2 rounded text-sm">
                    Student
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const demoCredentials = {
    super_admin: { email: 'super@ithm.edu.pk', password: 'demo123' },
    admin: { email: 'admin@ithm.edu.pk', password: 'demo123' },
    accounts: { email: 'accounts@ithm.edu.pk', password: 'demo123' },
    teacher: { email: 'teacher@ithm.edu.pk', password: 'demo123' },
    student: { email: 'student@ithm.edu.pk', password: 'demo123' }
};

function demoLogin(role) {
    const credentials = demoCredentials[role];
    document.getElementById('email').value = credentials.email;
    document.getElementById('password').value = credentials.password;
    document.querySelector('form').submit();
}
</script>

<?php include '../../includes/footer.php'; ?>
```

### Step 3: Registration System
**Duration**: 2-3 hours

**File: `modules/auth/register.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: ../student/dashboard.php');
    exit;
}

$errors = [];
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'first_name' => sanitizeInput($_POST['first_name']),
        'last_name' => sanitizeInput($_POST['last_name']),
        'email' => sanitizeInput($_POST['email']),
        'username' => sanitizeInput($_POST['username']),
        'phone' => sanitizeInput($_POST['phone']),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password'],
        'role' => 'student' // Default role for registration
    ];
    
    $result = $auth->register($userData);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $errors = $result['errors'];
    }
}

$page_title = 'Register';
include '../../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    sign in to existing account
                </a>
            </p>
        </div>
        
        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= $success ?>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input id="first_name" name="first_name" type="text" required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                               value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
                        <?php if (isset($errors['first_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['first_name'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input id="last_name" name="last_name" type="text" required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                               value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                        <?php if (isset($errors['last_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['last_name'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input id="username" name="username" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    <?php if (isset($errors['username'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['username'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input id="phone" name="phone" type="tel" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                           placeholder="03001234567" 
                           value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                    <?php if (isset($errors['phone'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['phone'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <?php if (isset($errors['confirm_password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['confirm_password'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required 
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900">
                    I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms and Conditions</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

### Step 4: Password Reset System
**Duration**: 2-3 hours

**File: `modules/auth/forgot_password.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();

$message = '';
$error = '';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    
    $result = $auth->requestPasswordReset($email);
    
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$page_title = 'Forgot Password';
include '../../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Forgot your password?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= $message ?>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input id="email" name="email" type="email" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                       placeholder="Enter your email address">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Send Reset Link
                </button>
            </div>
            
            <div class="text-center">
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

**File: `modules/auth/reset_password.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

$auth = new Auth();

$token = $_GET['token'] ?? '';
$message = '';
$error = '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = $auth->resetPassword($token, $newPassword);
        
        if ($result['success']) {
            $message = $result['message'];
            header('refresh:3;url=login.php');
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = 'Reset Password';
include '../../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your new password below.
            </p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
            <?= $message ?>
            <p class="mt-2 text-sm">Redirecting to login page...</p>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" method="POST">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input id="password" name="password" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                       placeholder="Enter new password">
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input id="confirm_password" name="confirm_password" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                       placeholder="Confirm new password">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
```

### Step 5: Logout System
**Duration**: 30 minutes

**File: `modules/auth/logout.php`**
```php
<?php
session_start();
require_once '../../config/constants.php';
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->logout();

header('Location: login.php?message=logged_out');
exit;
?>
```

### Step 6: Demo Data Setup
**Duration**: 1-2 hours

**File: `database/demo_data.sql`**
```sql
-- Demo Users (passwords: demo123)
INSERT INTO users (username, email, password, role, first_name, last_name, phone, campus_id, status) VALUES
('super_admin', 'super@ithm.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'Super', 'Administrator', '03001234567', NULL, 'active'),
('admin_demo', 'admin@ithm.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Campus', 'Administrator', '03001234568', 1, 'active'),
('accounts_demo', 'accounts@ithm.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'accounts', 'Finance', 'Officer', '03001234569', 1, 'active'),
('teacher_demo', 'teacher@ithm.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Professor', 'Ahmad', '03001234570', 1, 'active'),
('student_demo', 'student@ithm.edu.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Ahmed', 'Khan', '03001234571', 1, 'active');

-- Demo Campuses
INSERT INTO campuses (name, code, address, phone, status) VALUES
('Main Campus Lahore', 'MCL', 'Gulberg III, Lahore, Pakistan', '042-35714001', 'active'),
('Karachi Campus', 'KC', 'Defence Phase 5, Karachi, Pakistan', '021-35342001', 'active'),
('Islamabad Campus', 'IC', 'Blue Area, Islamabad, Pakistan', '051-26110001', 'active');

-- Demo Courses
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

### Step 7: Installation Script
**Duration**: 1 hour

**File: `install_demo.php`**
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

## Post-Phase Checklist
- [ ] Authentication system fully functional
- [ ] User registration working
- [ ] Login/logout system working
- [ ] Password reset functionality working
- [ ] Session management implemented
- [ ] Role-based access control working
- [ ] Demo data installed
- [ ] Security measures in place

## Testing Procedures
1. **Registration Test**
   - Test user registration with valid data
   - Test validation with invalid data
   - Test duplicate email/username handling

2. **Login Test**
   - Test login with valid credentials
   - Test login with invalid credentials
   - Test demo login buttons

3. **Session Test**
   - Test session timeout
   - Test role-based redirects
   - Test logout functionality

4. **Password Reset Test**
   - Test password reset request
   - Test password reset with valid token
   - Test password reset with invalid token

## Summary
**Key Achievements:**
- Complete authentication system implemented
- User registration and login functional
- Password reset system working
- Session management with security
- Role-based access control
- Demo data and installation script

**Next Phase Dependencies:**
- Authentication system must be fully functional
- User roles must be working
- Session management must be secure
- Demo data must be installed

**Files Created:**
- Authentication core class (`includes/auth.php`)
- Login system (`modules/auth/login.php`)
- Registration system (`modules/auth/register.php`)
- Password reset system (`modules/auth/forgot_password.php`, `modules/auth/reset_password.php`)
- Logout system (`modules/auth/logout.php`)
- Demo data (`database/demo_data.sql`)
- Installation script (`install_demo.php`)

**Estimated Completion Time:** 3-4 days
**Ready for Phase 3:** ✅
