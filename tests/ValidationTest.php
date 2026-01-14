<?php
/**
 * Validation Helper Tests
 */

namespace Tests;

use App\Helpers\ValidationHelper;

class ValidationTest
{
    private ValidationHelper $validator;
    
    public function setUp(): void
    {
        $this->validator = new ValidationHelper();
    }
    
    public function testRequiredValidation(): bool
    {
        $result = $this->validator->validate(['name' => ''], ['name' => 'required']);
        return TestRunner::assertFalse($result, 'Empty field should fail required');
    }
    
    public function testRequiredValidationPasses(): bool
    {
        $result = $this->validator->validate(['name' => 'John'], ['name' => 'required']);
        return TestRunner::assertTrue($result, 'Non-empty field should pass required');
    }
    
    public function testEmailValidation(): bool
    {
        $result = $this->validator->validate(['email' => 'invalid'], ['email' => 'email']);
        return TestRunner::assertFalse($result, 'Invalid email should fail');
    }
    
    public function testEmailValidationPasses(): bool
    {
        $result = $this->validator->validate(['email' => 'test@example.com'], ['email' => 'email']);
        return TestRunner::assertTrue($result, 'Valid email should pass');
    }
    
    public function testMinLengthValidation(): bool
    {
        $result = $this->validator->validate(['pass' => '123'], ['pass' => 'min:6']);
        return TestRunner::assertFalse($result, 'Short string should fail min length');
    }
    
    public function testMinLengthValidationPasses(): bool
    {
        $result = $this->validator->validate(['pass' => '123456'], ['pass' => 'min:6']);
        return TestRunner::assertTrue($result, 'String meeting min length should pass');
    }
    
    public function testMaxLengthValidation(): bool
    {
        $result = $this->validator->validate(['code' => 'TOOLONGCODE'], ['code' => 'max:5']);
        return TestRunner::assertFalse($result, 'Long string should fail max length');
    }
    
    public function testMaxLengthValidationPasses(): bool
    {
        $result = $this->validator->validate(['code' => 'ABC'], ['code' => 'max:5']);
        return TestRunner::assertTrue($result, 'String within max length should pass');
    }
    
    public function testNumericValidation(): bool
    {
        $result = $this->validator->validate(['amount' => 'abc'], ['amount' => 'numeric']);
        return TestRunner::assertFalse($result, 'Non-numeric should fail');
    }
    
    public function testNumericValidationPasses(): bool
    {
        $result = $this->validator->validate(['amount' => '123.45'], ['amount' => 'numeric']);
        return TestRunner::assertTrue($result, 'Numeric value should pass');
    }
    
    public function testConfirmedValidation(): bool
    {
        $data = ['password' => '123456', 'password_confirmation' => '654321'];
        $result = $this->validator->validate($data, ['password' => 'confirmed']);
        return TestRunner::assertFalse($result, 'Non-matching confirmation should fail');
    }
    
    public function testConfirmedValidationPasses(): bool
    {
        $data = ['password' => '123456', 'password_confirmation' => '123456'];
        $result = $this->validator->validate($data, ['password' => 'confirmed']);
        return TestRunner::assertTrue($result, 'Matching confirmation should pass');
    }
    
    public function testInValidation(): bool
    {
        $result = $this->validator->validate(['status' => 'invalid'], ['status' => 'in:pending,approved,rejected']);
        return TestRunner::assertFalse($result, 'Value not in list should fail');
    }
    
    public function testInValidationPasses(): bool
    {
        $result = $this->validator->validate(['status' => 'approved'], ['status' => 'in:pending,approved,rejected']);
        return TestRunner::assertTrue($result, 'Value in list should pass');
    }
    
    public function testPhoneValidation(): bool
    {
        $result = $this->validator->validate(['phone' => 'abc'], ['phone' => 'phone']);
        return TestRunner::assertFalse($result, 'Invalid phone should fail');
    }
    
    public function testPhoneValidationPasses(): bool
    {
        $result = $this->validator->validate(['phone' => '+92-300-1234567'], ['phone' => 'phone']);
        return TestRunner::assertTrue($result, 'Valid phone should pass');
    }
    
    public function testCnicValidation(): bool
    {
        $result = $this->validator->validate(['cnic' => '1234'], ['cnic' => 'cnic']);
        return TestRunner::assertFalse($result, 'Invalid CNIC should fail');
    }
    
    public function testCnicValidationPasses(): bool
    {
        $result = $this->validator->validate(['cnic' => '35201-1234567-1'], ['cnic' => 'cnic']);
        return TestRunner::assertTrue($result, 'Valid CNIC should pass');
    }
    
    public function testMultipleRulesValidation(): bool
    {
        $result = $this->validator->validate(
            ['email' => 'test@example.com'],
            ['email' => 'required|email']
        );
        return TestRunner::assertTrue($result, 'Multiple rules should all pass');
    }
    
    public function testGetErrors(): bool
    {
        $this->validator->validate(['name' => ''], ['name' => 'required']);
        $errors = $this->validator->errors();
        return TestRunner::assertArrayHasKey('name', $errors, 'Should have error for name field');
    }
    
    public function testSanitizeInput(): bool
    {
        $dirty = '<script>alert("xss")</script>';
        $clean = ValidationHelper::sanitize($dirty);
        $hasNoScript = strpos($clean, '<script>') === false;
        return TestRunner::assertTrue($hasNoScript, 'Should sanitize HTML');
    }
}

