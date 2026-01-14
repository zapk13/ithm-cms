<?php
/**
 * Base Controller Class
 */

namespace App\Core;

class Controller
{
    protected Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Render a view
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View not found: {$view}");
        }
    }
    
    /**
     * Render view with layout
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts.app'): void
    {
        $data['_content'] = $view;
        $this->view($layout, $data);
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, int $statusCode = 200): void
    {
        // Clear any previous output
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Redirect back
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        header("Location: {$referer}");
        exit;
    }
    
    /**
     * Get POST data
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get all POST data
     */
    protected function allInput(): array
    {
        return $_POST;
    }
    
    /**
     * Get GET parameter
     */
    protected function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Normalize truthy/falsey form inputs (checkboxes sent as "on"/"1"/true/false).
     */
    protected function normalizeBoolean($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        $value = strtolower((string)$value);
        return in_array($value, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }
    
    /**
     * Get JSON input
     */
    protected function jsonInput(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }
    
    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }
    
    /**
     * Get flash messages
     */
    protected function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get authenticated user
     */
    protected function user(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT u.*, r.name as role_name, r.slug as role_slug, c.name as campus_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             LEFT JOIN campuses c ON u.campus_id = c.id 
             WHERE u.id = ?",
            [$_SESSION['user_id']]
        );
    }
    
    /**
     * Check user role
     */
    protected function hasRole(string $roleSlug): bool
    {
        $user = $this->user();
        return $user && $user['role_slug'] === $roleSlug;
    }
    
    /**
     * Check if user has any of the given roles
     */
    protected function hasAnyRole(array $roleSlugs): bool
    {
        $user = $this->user();
        return $user && in_array($user['role_slug'], $roleSlugs);
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken(): bool
    {
        $token = $this->input('_token')
            ?? $this->input('csrf_token')
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? '';
        return hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token);
    }
    
    /**
     * Abort with error
     */
    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        
        if ($this->isApiRequest()) {
            $this->json(['error' => $message ?: 'Error'], $code);
        }
        
        $errorView = VIEWS_PATH . "/errors/{$code}.php";
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo $message ?: "Error {$code}";
        }
        exit;
    }
    
    /**
     * Check if API request
     */
    protected function isApiRequest(): bool
    {
        return strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
    }
}

