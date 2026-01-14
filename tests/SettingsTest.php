<?php
/**
 * Settings Model Tests
 */

namespace Tests;

use App\Models\Setting;

class SettingsTest
{
    private Setting $settingModel;
    
    public function setUp(): void
    {
        $this->settingModel = new Setting();
    }
    
    public function testCanGetSetting(): bool
    {
        $value = $this->settingModel->get('institute_name');
        return TestRunner::assertNotNull($value, 'Should get institute name setting');
    }
    
    public function testGetDefaultValueForMissingSetting(): bool
    {
        $value = $this->settingModel->get('nonexistent_setting', 'default');
        return TestRunner::assertEquals('default', $value, 'Should return default for missing setting');
    }
    
    public function testCanSetSetting(): bool
    {
        $key = 'test_setting_' . time();
        $this->settingModel->set($key, 'test_value', 'string', 'test');
        
        // Clear cache to force fresh read
        $this->settingModel->clearCache();
        $value = $this->settingModel->get($key);
        
        // Clean up using raw query
        $db = \App\Core\Database::getInstance();
        $db->query("DELETE FROM settings WHERE `key` = ?", [$key]);
        
        return TestRunner::assertEquals('test_value', $value, 'Should set and get setting');
    }
    
    public function testIntegerTypeCasting(): bool
    {
        $value = $this->settingModel->get('fee_due_reminder_days');
        return TestRunner::assertTrue(is_int($value) || is_numeric($value), 'Should be integer or numeric');
    }
    
    public function testGetSettingsByGroup(): bool
    {
        $settings = $this->settingModel->getByGroup('general');
        return TestRunner::assertGreaterThan(0, count($settings), 'Should get settings by group');
    }
    
    public function testGetAllSettings(): bool
    {
        $settings = $this->settingModel->getAllSettings();
        return TestRunner::assertArrayHasKey('institute_name', $settings, 'Should have institute_name');
    }
    
    public function testGetInstituteInfo(): bool
    {
        $info = $this->settingModel->getInstituteInfo();
        
        $hasFields = isset($info['name']) && isset($info['email']) && isset($info['phone']);
        
        return TestRunner::assertTrue($hasFields, 'Should have institute info fields');
    }
    
    public function testUpdateExistingSetting(): bool
    {
        $originalValue = $this->settingModel->get('institute_short_name');
        
        $this->settingModel->set('institute_short_name', 'TEST_ITHM');
        $newValue = $this->settingModel->get('institute_short_name');
        
        // Restore
        $this->settingModel->set('institute_short_name', $originalValue);
        
        return TestRunner::assertEquals('TEST_ITHM', $newValue, 'Should update existing setting');
    }
}

