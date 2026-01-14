<?php
/**
 * Web Routes
 */

use App\Core\Router;

$router = new Router();

// ===================== PUBLIC ROUTES =====================

// Home redirect
$router->get('/', function() {
    header('Location: ' . BASE_URL . '/login');
    exit;
});

// Auth routes (Guest only)
$router->group(['prefix' => '', 'middleware' => ['GuestMiddleware']], function($router) {
    $router->get('/login', 'AuthController@showLogin');
    $router->post('/login', 'AuthController@login');
    $router->get('/register', 'AuthController@showRegister');
    $router->post('/register', 'AuthController@register');
    $router->get('/forgot-password', 'AuthController@showForgotPassword');
    $router->post('/forgot-password', 'AuthController@forgotPassword');
    $router->get('/reset-password/{token}', 'AuthController@showResetPassword');
    $router->post('/reset-password', 'AuthController@resetPassword');
});

// Logout (Authenticated)
$router->post('/logout', 'AuthController@logout', ['AuthMiddleware']);

// ===================== ADMIN ROUTES =====================

$router->group(['prefix' => '/admin', 'middleware' => ['AdminMiddleware']], function($router) {
    // Dashboard
    $router->get('/dashboard', 'AdminController@dashboard');
    
    // Campus Management
    $router->get('/campuses', 'AdminController@campuses');
    $router->get('/campuses/create', 'AdminController@createCampus');
    $router->post('/campuses', 'AdminController@storeCampus');
    $router->get('/campuses/{id}/edit', 'AdminController@editCampus');
    $router->post('/campuses/{id}', 'AdminController@updateCampus');
    $router->post('/campuses/{id}/delete', 'AdminController@deleteCampus');
    
    // Course Management
    $router->get('/courses', 'AdminController@courses');
    $router->post('/courses', 'AdminController@storeCourse');
    $router->post('/courses/{id}', 'AdminController@updateCourse');
    $router->post('/courses/{id}/delete', 'AdminController@deleteCourse');
    
    // Fee Structures
    $router->get('/fee-structures', 'AdminController@feeStructures');
    $router->post('/fee-structures', 'AdminController@storeFeeStructure');
    $router->post('/fee-structures/{id}/delete', 'AdminController@deleteFeeStructure');
    
    // Admission Management
    $router->get('/admissions', 'AdminController@admissions');
    $router->get('/admissions/new', 'AdminController@newAdmission');
    $router->post('/admissions/submit', 'AdminController@submitAdmission');
    $router->get('/admissions/{id}', 'AdminController@viewAdmission');
    $router->get('/admissions/{id}/pdf', 'AdminController@downloadAdmissionPdf');
    $router->post('/admissions/{id}/status', 'AdminController@updateAdmissionStatus');
    $router->post('/admissions/{id}/fee-challan', 'AdminController@generateFeeChallan');
    $router->post('/admissions/{id}/roll-number', 'AdminController@assignRollNumber');
    $router->post('/admissions/{id}/trash', 'AdminController@trashAdmission');
    
    // Fee Management
    $router->get('/fee-vouchers', 'AdminController@feeVouchers');
    $router->get('/fee-vouchers/{id}/pdf', 'AdminController@downloadFeeVoucherPdf');
    $router->post('/fee-vouchers/{id}/cancel', 'AdminController@cancelFeeVoucher');
    $router->get('/pending-payments', 'AdminController@pendingPayments');
    $router->post('/payments/{id}/verify', 'AdminController@verifyPayment');
    $router->post('/fee-reminders', 'AdminController@sendFeeReminder');
    $router->post('/generate-fee-vouchers', 'AdminController@generateFeeVouchers');
    
    // User Management
    $router->get('/users', 'AdminController@users');
    $router->post('/users', 'AdminController@storeUser');
    $router->post('/users/{id}', 'AdminController@updateUser');
    
    // Certificate Management
    $router->get('/certificates', 'AdminController@certificates');
    $router->post('/certificates', 'AdminController@uploadCertificate');
    
    // Exams & Attendance
    $router->get('/exams', 'AdminController@exams');
    $router->post('/exams/terms', 'AdminController@storeExamTerm');
    $router->post('/exams', 'AdminController@storeExam');
    $router->get('/results', 'AdminController@results');
    $router->get('/attendance', 'AdminController@attendance');
    $router->post('/attendance/sessions', 'AdminController@storeAttendanceSession');
    
    // Settings
    $router->get('/settings', 'AdminController@settings');
    $router->post('/settings', 'AdminController@updateSettings');
    $router->post('/test-smtp', 'AdminController@testSmtp');
    
    // API endpoints for Admin
    $router->get('/api/campuses/{id}/courses', 'AdminController@getCourses');
});

// ===================== STUDENT ROUTES =====================

$router->group(['prefix' => '/student', 'middleware' => ['StudentMiddleware']], function($router) {
    // Dashboard
    $router->get('/dashboard', 'StudentController@dashboard');
    
    // Admission
    $router->get('/admission/new', 'StudentController@newAdmission');
    $router->post('/admission', 'StudentController@submitAdmission');
    $router->get('/applications/{id}', 'StudentController@viewApplication');
    $router->get('/applications/{id}/pdf', 'StudentController@downloadAdmissionPdf');
    $router->get('/applications/{id}/edit', 'StudentController@editApplication');
    $router->post('/applications/{id}', 'StudentController@updateApplication');
    
    // Fee Vouchers
    $router->get('/fees', 'StudentController@feeVouchers');
    $router->get('/fees/{id}', 'StudentController@viewVoucher');
    $router->get('/fees/{id}/pdf', 'StudentController@downloadFeeVoucherPdf');
    $router->post('/fees/{id}/pay', 'StudentController@submitPayment');
    
    // Notifications
    $router->get('/notifications', 'StudentController@notifications');
    $router->post('/notifications/{id}/read', 'StudentController@markNotificationRead');
    $router->post('/notifications/read-all', 'StudentController@markAllNotificationsRead');
    
    // Certificates
    $router->get('/certificates', 'StudentController@certificates');
    
    // Profile
    $router->get('/profile', 'StudentController@profile');
    $router->post('/profile', 'StudentController@updateProfile');
    $router->post('/change-password', 'StudentController@changePassword');
});

// ===================== API ROUTES =====================

$router->group(['prefix' => '/api', 'middleware' => ['AuthMiddleware']], function($router) {
    // Courses by campus
    $router->get('/campuses/{id}/courses', 'StudentController@getCourses');
    
    // Fee structure
    $router->get('/fee-structure', 'StudentController@getFeeStructure');
    
    // Notifications count
    $router->get('/notifications/count', function() {
        $user = $_SESSION['user_id'] ?? null;
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $notification = new \App\Models\Notification();
        echo json_encode(['count' => $notification->countUnread($user)]);
    });
});

return $router;

