<?php
/**
 * Guest Middleware
 * Only allows non-authenticated users
 */

namespace App\Middleware;

use App\Helpers\SessionHelper;

class GuestMiddleware
{
    /**
     * Handle the request
     */
    public function handle(): bool
    {
        SessionHelper::start();
        
        if (SessionHelper::isLoggedIn()) {
            // Redirect to dashboard based on role
            $role = SessionHelper::userRole();
            
            $redirectMap = [
                'system_admin' => '/admin/dashboard',
                'main_campus_admin' => '/admin/dashboard',
                'sub_campus_admin' => '/admin/dashboard',
                'student' => '/student/dashboard'
            ];
            
            $redirect = $redirectMap[$role] ?? '/';
            header('Location: ' . BASE_URL . $redirect);
            exit;
        }
        
        return true;
    }
}

