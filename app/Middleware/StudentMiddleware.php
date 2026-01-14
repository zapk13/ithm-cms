<?php
/**
 * Student Middleware
 * Only allows student users
 */

namespace App\Middleware;

use App\Helpers\SessionHelper;

class StudentMiddleware
{
    /**
     * Handle the request
     */
    public function handle(): bool
    {
        SessionHelper::start();
        
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        $role = SessionHelper::userRole();
        
        if ($role !== 'student') {
            if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }
            
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
        
        return true;
    }
}

