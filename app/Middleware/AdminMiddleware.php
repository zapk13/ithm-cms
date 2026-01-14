<?php
/**
 * Admin Middleware
 * Only allows admin users
 */

namespace App\Middleware;

use App\Helpers\SessionHelper;

class AdminMiddleware
{
    /**
     * Handle the request
     */
    public function handle(): bool
    {
        SessionHelper::start();
        
        $isApiRequest = strpos($_SERVER['REQUEST_URI'], '/api/') !== false || 
                        strpos($_SERVER['REQUEST_URI'], '/admin/api/') !== false;
        
        if (!SessionHelper::isLoggedIn()) {
            if ($isApiRequest) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $role = SessionHelper::userRole();
        $adminRoles = ['system_admin', 'main_campus_admin', 'sub_campus_admin'];
        
        if (!in_array($role, $adminRoles)) {
            if ($isApiRequest) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }
            
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        }
        
        return true;
    }
}

