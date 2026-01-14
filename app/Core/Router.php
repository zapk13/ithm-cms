<?php
/**
 * Router Class
 * Handles URL routing and dispatching
 */

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddleware = [];
    
    /**
     * Add a GET route
     */
    public function get(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    /**
     * Add a POST route
     */
    public function post(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    /**
     * Add a PUT route
     */
    public function put(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    /**
     * Add a DELETE route
     */
    public function delete(string $path, $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    /**
     * Group routes with prefix and middleware
     */
    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddleware = $this->currentGroupMiddleware;
        
        $this->currentGroupPrefix .= $options['prefix'] ?? '';
        $this->currentGroupMiddleware = array_merge(
            $this->currentGroupMiddleware,
            $options['middleware'] ?? []
        );
        
        $callback($this);
        
        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddleware = $previousMiddleware;
    }
    
    /**
     * Add a route
     */
    private function addRoute(string $method, string $path, $handler, array $middleware = []): self
    {
        $fullPath = $this->currentGroupPrefix . $path;
        $fullPath = $fullPath === '' ? '/' : $fullPath;
        
        // Convert route parameters to regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => array_merge($this->currentGroupMiddleware, $middleware)
        ];
        
        return $this;
    }
    
    /**
     * Dispatch the current request
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        // Check if API request early
        $isApi = $this->isApiRequest();
        
        // For API requests, ensure clean output
        if ($isApi && ob_get_level() > 0) {
            ob_clean();
        }
        
        // Handle PUT/DELETE via POST with _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters from regex matches
                $params = [];
                // Get parameter names from route path (e.g., {id} from /campuses/{id}/courses)
                if (preg_match_all('/\{([a-zA-Z_]+)\}/', $route['path'], $paramNames)) {
                    foreach ($paramNames[1] as $paramName) {
                        if (isset($matches[$paramName])) {
                            $params[] = $matches[$paramName];
                        }
                    }
                }
                
                // Debug: Log parameters if in development
                if (APP_ENV === 'development' && empty($params)) {
                    error_log("Router: No parameters extracted from route {$route['path']} for URI {$uri}");
                }
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = "App\\Middleware\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        try {
                            $middlewareInstance = new $middlewareClass();
                            $result = $middlewareInstance->handle();
                            if ($result === false) {
                                return;
                            }
                        } catch (\Throwable $e) {
                            $this->handleError($e);
                            return;
                        }
                    }
                }
                
                // Call handler with error handling
                try {
                    $this->callHandler($route['handler'], $params);
                } catch (\Throwable $e) {
                    $this->handleError($e);
                }
                return;
            }
        }
        
        // No route matched
        $this->handleNotFound();
    }
    
    /**
     * Get the current URI
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove base path
        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Ensure leading slash
        $uri = '/' . ltrim($uri, '/');
        
        // Remove trailing slash (except for root)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }
        
        return $uri;
    }
    
    /**
     * Call route handler
     */
    private function callHandler($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                
                // Use reflection to get method parameters and map route params correctly
                try {
                    $reflection = new \ReflectionMethod($controllerClass, $method);
                    $methodParams = $reflection->getParameters();
                    
                    // Map route parameters to method parameters by position
                    $args = [];
                    $paramIndex = 0;
                    foreach ($methodParams as $methodParam) {
                        if (isset($params[$paramIndex])) {
                            $args[] = $params[$paramIndex];
                            $paramIndex++;
                        } else {
                            // If parameter has default value, use it
                            if ($methodParam->isDefaultValueAvailable()) {
                                $args[] = $methodParam->getDefaultValue();
                            } else {
                                // For required parameters without defaults, pass null
                                // The controller method should handle this
                                $args[] = null;
                            }
                        }
                    }
                    
                    call_user_func_array([$controllerInstance, $method], $args);
                } catch (\ReflectionException $e) {
                    // Fallback: pass params as-is (assumes they're in correct order)
                    if (empty($params)) {
                        // If no params extracted, try to call without params
                        call_user_func([$controllerInstance, $method]);
                    } else {
                        call_user_func_array([$controllerInstance, $method], $params);
                    }
                } catch (\Throwable $e) {
                    // If controller method throws an error, handle it
                    if (defined('APP_ENV') && APP_ENV === 'development') {
                        error_log("Router: Error calling {$controllerClass}@{$method}: " . $e->getMessage());
                    }
                    throw $e;
                }
                return;
            }
        }
        
        $this->handleNotFound();
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        
        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found']);
        } else {
            include VIEWS_PATH . '/errors/404.php';
        }
    }
    
    /**
     * Check if request is API request
     */
    private function isApiRequest(): bool
    {
        $uri = $this->getUri();
        return strpos($uri, '/api/') === 0 || strpos($uri, '/admin/api/') === 0;
    }
    
    /**
     * Handle errors
     */
    private function handleError(\Throwable $e): void
    {
        if ($this->isApiRequest()) {
            // Clear any output
            if (ob_get_level() > 0) {
                ob_clean();
            }
            
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => APP_ENV === 'development' ? $e->getMessage() : 'An error occurred'
            ]);
            exit;
        }
        
        // For non-API requests, show error page
        http_response_code(500);
        if (APP_ENV === 'development') {
            echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
        } else {
            include VIEWS_PATH . '/errors/500.php';
        }
        exit;
    }
}

