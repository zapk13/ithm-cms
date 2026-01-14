<?php
/**
 * Authentication System Tests
 */

namespace Tests;

use App\Models\User;

class AuthenticationTest
{
    private User $userModel;
    
    public function setUp(): void
    {
        $this->userModel = new User();
    }
    
    public function testUserModelCanFindByEmail(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        return TestRunner::assertNotNull($user, 'User findByEmail should return user');
    }
    
    public function testPasswordHashIsValid(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        if (!$user) return 'Admin user not found';
        
        $isValid = $this->userModel->verifyPassword('Admin@123', $user['password']);
        return TestRunner::assertTrue($isValid, 'Password verification should pass for Admin@123');
    }
    
    public function testInvalidPasswordFails(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        if (!$user) return 'Admin user not found';
        
        $isValid = $this->userModel->verifyPassword('wrongpassword', $user['password']);
        return TestRunner::assertFalse($isValid, 'Invalid password should fail verification');
    }
    
    public function testUserHasRoleInfo(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        return TestRunner::assertArrayHasKey('role_slug', $user, 'User should have role_slug');
    }
    
    public function testAdminUserHasSystemAdminRole(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        return TestRunner::assertEquals('system_admin', $user['role_slug'], 'Admin should have system_admin role');
    }
    
    public function testUserCreationWithHashedPassword(): bool
    {
        // Create test user
        $testEmail = 'test_' . time() . '@test.com';
        $userId = $this->userModel->createUser([
            'name' => 'Test User',
            'email' => $testEmail,
            'password' => 'TestPass123',
            'role_id' => 4 // Student
        ]);
        
        if (!$userId) return 'Failed to create test user';
        
        // Verify password is hashed
        $user = $this->userModel->find($userId);
        $isHashed = strpos($user['password'], '$2y$') === 0;
        
        // Clean up
        $this->userModel->delete($userId);
        
        return TestRunner::assertTrue($isHashed, 'Password should be bcrypt hashed');
    }
    
    public function testResetTokenGeneration(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        if (!$user) return 'Admin user not found';
        
        $token = $this->userModel->createResetToken($user['id']);
        $isValid = strlen($token) === 64; // 32 bytes = 64 hex chars
        
        // Clear the token
        $this->userModel->clearResetToken($user['id']);
        
        return TestRunner::assertTrue($isValid, 'Reset token should be 64 characters');
    }
    
    public function testGetUserWithDetails(): bool
    {
        $user = $this->userModel->findByEmail('admin@ithm.edu.pk');
        $details = $this->userModel->getWithDetails($user['id']);
        
        $hasRoleName = isset($details['role_name']);
        return TestRunner::assertTrue($hasRoleName, 'User details should include role_name');
    }
}

