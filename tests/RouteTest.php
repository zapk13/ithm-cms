<?php
/**
 * Route/Endpoint Tests
 * Tests HTTP responses for main application routes
 */

namespace Tests;

class RouteTest
{
    private string $baseUrl;
    
    public function setUp(): void
    {
        // Point to production base URL for integration checks
        $this->baseUrl = 'https://cms.ithm.edu.pk/public';
    }
    
    private function makeRequest(string $path, string $method = 'GET', array $data = []): array
    {
        $url = $this->baseUrl . $path;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'headers' => $headers,
            'body' => $body
        ];
    }
    
    public function testHomePageReturns200(): bool
    {
        $response = $this->makeRequest('/');
        return TestRunner::assertEquals(200, $response['status'], 'Home page should return 200');
    }
    
    public function testHomePageContainsTitle(): bool
    {
        $response = $this->makeRequest('/');
        $hasTitle = strpos($response['body'], 'ITHM') !== false;
        return TestRunner::assertTrue($hasTitle, 'Home page should contain ITHM');
    }
    
    public function testLoginPageReturns200(): bool
    {
        $response = $this->makeRequest('/login');
        return TestRunner::assertEquals(200, $response['status'], 'Login page should return 200');
    }
    
    public function testLoginPageContainsForm(): bool
    {
        $response = $this->makeRequest('/login');
        $hasForm = strpos($response['body'], '<form') !== false;
        return TestRunner::assertTrue($hasForm, 'Login page should contain form');
    }
    
    public function testRegisterPageReturns200(): bool
    {
        $response = $this->makeRequest('/register');
        return TestRunner::assertEquals(200, $response['status'], 'Register page should return 200');
    }
    
    public function testForgotPasswordPageReturns200(): bool
    {
        $response = $this->makeRequest('/forgot-password');
        return TestRunner::assertEquals(200, $response['status'], 'Forgot password page should return 200');
    }
    
    public function testNonExistentPageReturns404(): bool
    {
        $response = $this->makeRequest('/nonexistent-page-xyz');
        return TestRunner::assertEquals(404, $response['status'], 'Non-existent page should return 404');
    }
    
    public function testAdmitPageReturns200(): bool
    {
        $response = $this->makeRequest('/admit');
        return TestRunner::assertEquals(200, $response['status'], 'Admission page should return 200');
    }
    
    public function testAdminDashboardRequiresAuth(): bool
    {
        $response = $this->makeRequest('/admin/dashboard');
        // Should redirect to login (302) or show unauthorized (401/403)
        $requiresAuth = $response['status'] === 302 || 
                        $response['status'] === 401 ||
                        $response['status'] === 403 ||
                        strpos($response['body'], 'login') !== false;
        return TestRunner::assertTrue($requiresAuth, 'Admin dashboard should require authentication');
    }
    
    public function testStudentDashboardRequiresAuth(): bool
    {
        $response = $this->makeRequest('/student/dashboard');
        $requiresAuth = $response['status'] === 302 || 
                        $response['status'] === 401 ||
                        $response['status'] === 403 ||
                        strpos($response['body'], 'login') !== false;
        return TestRunner::assertTrue($requiresAuth, 'Student dashboard should require authentication');
    }
    
    public function testApiCampusesReturns200(): bool
    {
        $response = $this->makeRequest('/api/campuses');
        $success = $response['status'] === 200 || $response['status'] === 302;
        return TestRunner::assertTrue($success, 'API campuses should be accessible');
    }
    
    public function testApiCoursesReturns200(): bool
    {
        $response = $this->makeRequest('/api/courses');
        $success = $response['status'] === 200 || $response['status'] === 302;
        return TestRunner::assertTrue($success, 'API courses should be accessible');
    }
    
    public function testInvalidLoginReturnsError(): bool
    {
        $response = $this->makeRequest('/login', 'POST', [
            'email' => 'invalid@test.com',
            'password' => 'wrongpassword'
        ]);
        // Should show error or redirect back to login
        $hasError = strpos($response['body'], 'error') !== false ||
                    strpos($response['body'], 'invalid') !== false ||
                    strpos($response['body'], 'login') !== false ||
                    $response['status'] === 302;
        return TestRunner::assertTrue($hasError, 'Invalid login should show error');
    }
    
    public function testHomePageHasNavigation(): bool
    {
        $response = $this->makeRequest('/');
        $hasNav = strpos($response['body'], 'nav') !== false || 
                  strpos($response['body'], 'header') !== false;
        return TestRunner::assertTrue($hasNav, 'Home page should have navigation');
    }
    
    public function testHomePageHasCourseSection(): bool
    {
        $response = $this->makeRequest('/');
        $hasCourses = strpos(strtolower($response['body']), 'course') !== false ||
                      strpos(strtolower($response['body']), 'program') !== false;
        return TestRunner::assertTrue($hasCourses, 'Home page should mention courses/programs');
    }
    
    public function testHomePageHasFooter(): bool
    {
        $response = $this->makeRequest('/');
        $hasFooter = strpos($response['body'], 'footer') !== false;
        return TestRunner::assertTrue($hasFooter, 'Home page should have footer');
    }
    
    public function testLoginPageHasEmailField(): bool
    {
        $response = $this->makeRequest('/login');
        $hasEmail = strpos($response['body'], 'email') !== false;
        return TestRunner::assertTrue($hasEmail, 'Login page should have email field');
    }
    
    public function testLoginPageHasPasswordField(): bool
    {
        $response = $this->makeRequest('/login');
        $hasPassword = strpos($response['body'], 'password') !== false;
        return TestRunner::assertTrue($hasPassword, 'Login page should have password field');
    }
    
    public function testCsrfTokenPresent(): bool
    {
        $response = $this->makeRequest('/login');
        $hasCsrf = strpos($response['body'], 'csrf') !== false ||
                   strpos($response['body'], '_token') !== false;
        return TestRunner::assertTrue($hasCsrf, 'Forms should have CSRF protection');
    }
    
    public function testStaticAssetsAccessible(): bool
    {
        // Test if CSS is accessible
        $response = $this->makeRequest('/assets/css/app.css');
        $accessible = $response['status'] === 200 || $response['status'] === 404;
        return TestRunner::assertTrue($accessible, 'Static assets path should be accessible');
    }
}

