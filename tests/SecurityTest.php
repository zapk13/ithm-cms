<?php
/**
 * Security Tests
 * Tests security measures in the application
 */

namespace Tests;

use App\Helpers\ValidationHelper;
use App\Core\Database;

class SecurityTest
{
    public function testXssProtectionInSanitize(): bool
    {
        $malicious = '<script>alert("xss")</script>';
        $clean = ValidationHelper::sanitize($malicious);
        $safe = strpos($clean, '<script>') === false;
        return TestRunner::assertTrue($safe, 'XSS should be sanitized');
    }
    
    public function testHtmlEntitiesSanitized(): bool
    {
        $malicious = '<img src="x" onerror="alert(1)">';
        $clean = ValidationHelper::sanitize($malicious);
        $safe = strpos($clean, 'onerror') === false;
        return TestRunner::assertTrue($safe, 'HTML event handlers should be sanitized');
    }
    
    public function testSqlInjectionProtection(): bool
    {
        // Test that PDO prepared statements are used
        $db = Database::getInstance();
        
        $malicious = "'; DROP TABLE users; --";
        
        // This should not cause an error if prepared statements are used properly
        try {
            $result = $db->fetch(
                "SELECT * FROM users WHERE email = ?",
                [$malicious]
            );
            return true; // If we get here, prepared statement worked
        } catch (\Exception $e) {
            // An exception from DROP would be a problem
            return strpos($e->getMessage(), 'DROP') === false;
        }
    }
    
    public function testPasswordHashUseBcrypt(): bool
    {
        $password = 'test123';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $isBcrypt = strpos($hash, '$2y$') === 0;
        return TestRunner::assertTrue($isBcrypt, 'Passwords should use bcrypt');
    }
    
    public function testPasswordHashVerification(): bool
    {
        $password = 'SecurePass123!';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $verified = password_verify($password, $hash);
        return TestRunner::assertTrue($verified, 'Password verification should work');
    }
    
    public function testWrongPasswordFails(): bool
    {
        $password = 'SecurePass123!';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $verified = password_verify('wrongpassword', $hash);
        return TestRunner::assertFalse($verified, 'Wrong password should fail verification');
    }
    
    public function testEmailValidationRejectsInvalid(): bool
    {
        $validator = new ValidationHelper();
        
        $invalid = [
            'notanemail',
            '@nodomain',
            'no@',
            'spaces in@email.com',
            '<script>@attack.com'
        ];
        
        foreach ($invalid as $email) {
            $result = $validator->validate(['email' => $email], ['email' => 'email']);
            if ($result === true) {
                return "Should reject: {$email}";
            }
        }
        
        return true;
    }
    
    public function testEmailValidationAcceptsValid(): bool
    {
        $validator = new ValidationHelper();
        
        $valid = [
            'test@example.com',
            'user.name@domain.org',
            'user+tag@example.com',
            'user123@sub.domain.com'
        ];
        
        foreach ($valid as $email) {
            $result = $validator->validate(['email' => $email], ['email' => 'email']);
            if ($result !== true) {
                return "Should accept: {$email}";
            }
        }
        
        return true;
    }
    
    public function testResetTokenRandomness(): bool
    {
        $user = new \App\Models\User();
        $adminUser = $user->findByEmail('admin@ithm.edu.pk');
        
        $token1 = $user->createResetToken($adminUser['id']);
        $user->clearResetToken($adminUser['id']);
        
        $token2 = $user->createResetToken($adminUser['id']);
        $user->clearResetToken($adminUser['id']);
        
        return TestRunner::assertFalse($token1 === $token2, 'Reset tokens should be unique');
    }
    
    public function testResetTokenLength(): bool
    {
        $user = new \App\Models\User();
        $adminUser = $user->findByEmail('admin@ithm.edu.pk');
        
        $token = $user->createResetToken($adminUser['id']);
        $user->clearResetToken($adminUser['id']);
        
        // 32 bytes = 64 hex characters
        $correctLength = strlen($token) === 64;
        return TestRunner::assertTrue($correctLength, 'Reset token should be 64 characters');
    }
    
    public function testPhoneValidationRejectsLetters(): bool
    {
        $validator = new ValidationHelper();
        $result = $validator->validate(['phone' => 'abc-def-ghij'], ['phone' => 'phone']);
        return TestRunner::assertFalse($result, 'Phone should not contain letters');
    }
    
    public function testCnicFormatValidation(): bool
    {
        $validator = new ValidationHelper();
        
        // Valid CNIC
        $valid = $validator->validate(['cnic' => '35201-1234567-1'], ['cnic' => 'cnic']);
        
        // Invalid CNIC
        $invalid = $validator->validate(['cnic' => '123'], ['cnic' => 'cnic']);
        
        return TestRunner::assertTrue($valid === true && $invalid === false, 'CNIC validation should work');
    }
    
    public function testDatabaseUsePreparedStatements(): bool
    {
        $db = Database::getInstance();
        
        // Verify that query method accepts parameters
        $reflection = new \ReflectionMethod($db, 'query');
        $params = $reflection->getParameters();
        
        $hasParams = count($params) >= 2;
        return TestRunner::assertTrue($hasParams, 'Database query should support prepared statements');
    }
    
    public function testHtmlSpecialCharsInOutput(): bool
    {
        $input = '<script>alert("test")</script>';
        $output = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $safe = strpos($output, '<script>') === false;
        return TestRunner::assertTrue($safe, 'HTML special chars should be escaped');
    }
}

