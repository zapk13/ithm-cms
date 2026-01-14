<?php
/**
 * Authentication Middleware
 */

namespace App\Middleware;

use App\Helpers\SessionHelper;

class AuthMiddleware
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
            
            // Store intended URL
            SessionHelper::set('intended_url', $_SERVER['REQUEST_URI']);
            
            // Redirect to login
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        return true;
    }
}

