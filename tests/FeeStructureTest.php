<?php
/**
 * Fee Structure Tests
 */

namespace Tests;

use App\Models\FeeStructure;
use App\Models\Course;
use App\Models\Campus;

class FeeStructureTest
{
    private FeeStructure $feeModel;
    private Course $courseModel;
    private Campus $campusModel;
    private ?int $testCourseId = null;
    
    public function setUp(): void
    {
        $this->feeModel = new FeeStructure();
        $this->courseModel = new Course();
        $this->campusModel = new Campus();
        
        // Create test course
        $this->testCourseId = $this->courseModel->create([
            'name' => 'Fee Test Course',
            'code' => 'FTC' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
    }
    
    public function tearDown(): void
    {
        try {
            if ($this->testCourseId) {
                // Clean up fee structure first
                $campus = $this->campusModel->getMainCampus();
                $fee = $this->feeModel->getForCourseAndCampus($this->testCourseId, $campus['id']);
                if ($fee) {
                    $this->feeModel->delete($fee['id']);
                }
                $this->courseModel->delete($this->testCourseId);
                $this->testCourseId = null;
            }
        } catch (\Exception $e) {}
    }
    
    public function testCanCreateFeeStructure(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $feeId = $this->feeModel->create([
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'admission_fee' => 15000,
            'tuition_fee' => 25000,
            'semester_fee' => 20000,
            'monthly_fee' => 5000,
            'exam_fee' => 2000,
            'is_active' => 1
        ]);
        
        return TestRunner::assertGreaterThan(0, $feeId, 'Should create fee structure');
    }
    
    public function testCanGetFeeForCourseAndCampus(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        // Use createOrUpdate to avoid duplicate key error
        $this->feeModel->createOrUpdate([
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'admission_fee' => 15000,
            'tuition_fee' => 25000,
            'is_active' => 1
        ]);
        
        $fee = $this->feeModel->getForCourseAndCampus($this->testCourseId, $campus['id']);
        
        return TestRunner::assertNotNull($fee, 'Should find fee structure');
    }
    
    public function testFeeBreakdownCalculation(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        // Create fee structure
        $this->feeModel->createOrUpdate([
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'admission_fee' => 15000,
            'tuition_fee' => 25000,
            'other_charges' => 5000,
            'is_active' => 1
        ]);
        
        $breakdown = $this->feeModel->getFeeBreakdown($this->testCourseId, $campus['id']);
        
        $expectedTotal = 15000 + 25000 + 5000;
        return TestRunner::assertEquals($expectedTotal, $breakdown['total'], 'Total should be sum of fees');
    }
    
    public function testCreateOrUpdateFeeStructure(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        // Create
        $id1 = $this->feeModel->createOrUpdate([
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'admission_fee' => 10000,
            'is_active' => 1
        ]);
        
        // Update
        $id2 = $this->feeModel->createOrUpdate([
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'admission_fee' => 12000,
            'is_active' => 1
        ]);
        
        // Should return same ID (updated, not created new)
        return TestRunner::assertEquals($id1, $id2, 'Should update existing fee structure');
    }
    
    public function testGetAllFeeStructuresWithDetails(): bool
    {
        $structures = $this->feeModel->getAllWithDetails();
        
        $hasDetails = true;
        foreach ($structures as $fs) {
            if (!isset($fs['course_name']) || !isset($fs['campus_name'])) {
                $hasDetails = false;
                break;
            }
        }
        
        return TestRunner::assertTrue($hasDetails, 'Fee structures should have course and campus names');
    }
}

