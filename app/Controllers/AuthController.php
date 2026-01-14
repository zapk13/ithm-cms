<?php
/**
 * Authentication Controller
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;

class AuthController extends Controller
{
    private User $userModel;
    private ValidationHelper $validator;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->validator = new ValidationHelper();
    }
    
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        $this->view('auth.login', [
            'title' => 'Login'
        ]);
    }
    
    /**
     * Handle login
     */
    public function login(): void
    {
        // Validate CSRF
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request. Please try again.');
            $this->redirect('login');
        }
        
        // Validate input
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', $this->validator->allErrors()[0]);
            $this->redirect('login');
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        
        // Find user
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->flash('error', 'Invalid email or password.');
            $this->redirect('login');
        }
        
        // Check if user is active
        if (!$user['is_active']) {
            $this->flash('error', 'Your account has been deactivated. Please contact administrator.');
            $this->redirect('login');
        }
        
        // Check if password needs to be reset (for admin-created accounts)
        if (!empty($user['password_needs_reset'])) {
            // Generate reset token if not exists or expired
            $resetToken = $this->userModel->createResetToken($user['id'], 24); // 24 hours expiry
            $this->flash('info', 'Please set your password to continue. A password reset link has been sent to your email, or you can use the link below.');
            $this->redirect('reset-password/' . $resetToken);
        }
        
        // Start session and store user data
        SessionHelper::regenerate();
        SessionHelper::set('user_id', $user['id']);
        SessionHelper::set('user_name', $user['name']);
        SessionHelper::set('user_email', $user['email']);
        SessionHelper::set('user_role', $user['role_slug']);
        SessionHelper::set('user_role_id', $user['role_id']);
        SessionHelper::set('user_campus_id', $user['campus_id']);
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Redirect based on role
        $this->redirectByRole($user['role_slug']);
    }
    
    /**
     * Handle logout
     */
    public function logout(): void
    {
        SessionHelper::destroy();
        $this->flash('success', 'You have been logged out successfully.');
        $this->redirect('login');
    }
    
    /**
     * Show registration page
     */
    public function showRegister(): void
    {
        $this->view('auth.register', [
            'title' => 'Register'
        ]);
    }
    
    /**
     * Handle registration
     */
    public function register(): void
    {
        // Validate CSRF
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request. Please try again.');
            $this->redirect('register');
        }
        
        // Validate input
        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|phone',
            'cnic' => 'required|cnic',
            'password' => 'required|min:8|confirmed'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            SessionHelper::set('old_input', $_POST);
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('register');
        }
        
        // Create user
        $userId = $this->userModel->createUser([
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'cnic' => $this->input('cnic'),
            'password' => $this->input('password'),
            'role_id' => ROLE_STUDENT,
            'is_active' => 1
        ]);
        
        if (!$userId) {
            $this->flash('error', 'Registration failed. Please try again.');
            $this->redirect('register');
        }
        
        // Auto login
        $user = $this->userModel->getWithDetails($userId);
        
        SessionHelper::regenerate();
        SessionHelper::set('user_id', $user['id']);
        SessionHelper::set('user_name', $user['name']);
        SessionHelper::set('user_email', $user['email']);
        SessionHelper::set('user_role', $user['role_slug']);
        SessionHelper::set('user_role_id', $user['role_id']);
        SessionHelper::set('user_campus_id', $user['campus_id']);
        
        $this->flash('success', 'Registration successful! Welcome to ITHM CMS.');
        $this->redirect('student/dashboard');
    }
    
    /**
     * Show forgot password page
     */
    public function showForgotPassword(): void
    {
        $this->view('auth.forgot-password', [
            'title' => 'Forgot Password'
        ]);
    }
    
    /**
     * Handle forgot password
     */
    public function forgotPassword(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('forgot-password');
        }
        
        $email = $this->input('email');
        $user = $this->userModel->findByEmail($email);
        
        // Always show success message (security)
        $this->flash('success', 'If an account exists with this email, you will receive password reset instructions.');
        
        if ($user) {
            $token = $this->userModel->createResetToken($user['id']);
            // In production, send email with reset link
            // For now, we'll just store the token
        }
        
        $this->redirect('forgot-password');
    }
    
    /**
     * Show reset password page
     */
    public function showResetPassword(string $token): void
    {
        $user = $this->userModel->findByResetToken($token);
        
        if (!$user) {
            $this->flash('error', 'Invalid or expired reset token.');
            $this->redirect('forgot-password');
        }
        
        // Check if this is a first-time password setup
        $isFirstTime = !empty($user['password_needs_reset']);
        
        $this->view('auth.reset-password', [
            'title' => $isFirstTime ? 'Set Your Password' : 'Reset Password',
            'token' => $token,
            'isFirstTime' => $isFirstTime,
            'userName' => $user['name'] ?? ''
        ]);
    }
    
    /**
     * Handle reset password
     */
    public function resetPassword(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->back();
        }
        
        $token = $this->input('token');
        $user = $this->userModel->findByResetToken($token);
        
        if (!$user) {
            $this->flash('error', 'Invalid or expired reset token.');
            $this->redirect('forgot-password');
        }
        
        $rules = [
            'password' => 'required|min:8|confirmed'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', $this->validator->allErrors()[0]);
            $this->redirect('reset-password/' . $token);
        }
        
        $this->userModel->updatePassword($user['id'], $this->input('password'));
        $this->userModel->clearResetToken($user['id']);
        
        // Clear password_needs_reset flag
        $this->userModel->clearPasswordNeedsReset($user['id']);
        
        $this->flash('success', 'Password set successfully. Please login with your new password.');
        $this->redirect('login');
    }
    
    /**
     * Redirect user by role
     */
    private function redirectByRole(string $role): void
    {
        $routes = [
            'system_admin' => 'admin/dashboard',
            'main_campus_admin' => 'admin/dashboard',
            'sub_campus_admin' => 'admin/dashboard',
            'student' => 'student/dashboard'
        ];
        
        $this->redirect($routes[$role] ?? '/');
    }
}

