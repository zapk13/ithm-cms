<?php
/**
 * Campus Management Tests
 */

namespace Tests;

use App\Models\Campus;

class CampusTest
{
    private Campus $campusModel;
    
    public function setUp(): void
    {
        $this->campusModel = new Campus();
    }
    
    public function testCanGetAllCampuses(): bool
    {
        $campuses = $this->campusModel->all();
        return TestRunner::assertGreaterThan(0, count($campuses), 'Should have at least one campus');
    }
    
    public function testMainCampusExists(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        return TestRunner::assertNotNull($mainCampus, 'Main campus should exist');
    }
    
    public function testMainCampusHasCorrectType(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        return TestRunner::assertEquals('main', $mainCampus['type'], 'Main campus type should be "main"');
    }
    
    public function testCanCreateCampus(): bool
    {
        $campusId = $this->campusModel->create([
            'name' => 'Test Sub Campus',
            'code' => 'TST' . time(),
            'type' => 'sub',
            'city' => 'Test City',
            'is_active' => 1
        ]);
        
        $exists = $campusId > 0;
        
        // Clean up
        if ($campusId) {
            $this->campusModel->delete($campusId);
        }
        
        return TestRunner::assertTrue($exists, 'Should be able to create campus');
    }
    
    public function testCampusCodeMustBeUnique(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        $exists = $this->campusModel->codeExists($mainCampus['code']);
        return TestRunner::assertTrue($exists, 'Duplicate campus code should be detected');
    }
    
    public function testGetActiveCampuses(): bool
    {
        $activeCampuses = $this->campusModel->getActive();
        
        $allActive = true;
        foreach ($activeCampuses as $campus) {
            if (!$campus['is_active']) {
                $allActive = false;
                break;
            }
        }
        
        return TestRunner::assertTrue($allActive, 'All returned campuses should be active');
    }
    
    public function testGetCampusWithStats(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        $campusWithStats = $this->campusModel->getWithStats($mainCampus['id']);
        
        $hasStats = isset($campusWithStats['student_count']) && 
                    isset($campusWithStats['course_count']) &&
                    isset($campusWithStats['pending_admissions']);
        
        return TestRunner::assertTrue($hasStats, 'Campus should have stats');
    }
    
    public function testGetCampusesForDropdown(): bool
    {
        $campuses = $this->campusModel->getForDropdown();
        
        $hasRequiredFields = true;
        foreach ($campuses as $campus) {
            if (!isset($campus['id']) || !isset($campus['name'])) {
                $hasRequiredFields = false;
                break;
            }
        }
        
        return TestRunner::assertTrue($hasRequiredFields, 'Dropdown should have id and name');
    }
}

