<?php
/**
 * Admission Management Tests
 */

namespace Tests;

use App\Models\Admission;
use App\Models\User;
use App\Models\Course;
use App\Models\Campus;

class AdmissionTest
{
    private Admission $admissionModel;
    private User $userModel;
    private Course $courseModel;
    private Campus $campusModel;
    private ?int $testUserId = null;
    private ?int $testCourseId = null;
    private ?int $testAdmissionId = null;
    
    public function setUp(): void
    {
        $this->admissionModel = new Admission();
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->campusModel = new Campus();
        
        // Create test student
        $this->testUserId = $this->userModel->createUser([
            'name' => 'Test Student',
            'email' => 'student_' . time() . '@test.com',
            'password' => 'test123',
            'role_id' => 4
        ]);
        
        // Create test course
        $this->testCourseId = $this->courseModel->create([
            'name' => 'Admission Test Course',
            'code' => 'ADM' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
    }
    
    public function tearDown(): void
    {
        // Delete in correct order to respect foreign keys
        try {
            if ($this->testAdmissionId) {
                $this->admissionModel->delete($this->testAdmissionId);
                $this->testAdmissionId = null;
            }
        } catch (\Exception $e) {}
        
        try {
            if ($this->testCourseId) {
                $this->courseModel->delete($this->testCourseId);
                $this->testCourseId = null;
            }
        } catch (\Exception $e) {}
        
        try {
            if ($this->testUserId) {
                $this->userModel->delete($this->testUserId);
                $this->testUserId = null;
            }
        } catch (\Exception $e) {}
    }
    
    public function testApplicationNumberGeneration(): bool
    {
        $appNo = $this->admissionModel->generateApplicationNo();
        $hasCorrectFormat = strpos($appNo, 'APP-' . date('Y') . '-') === 0;
        return TestRunner::assertTrue($hasCorrectFormat, 'Application number should have correct format');
    }
    
    public function testCanCreateAdmission(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test Student', 'father_name' => 'Test Father'],
            'guardian_info' => ['name' => 'Guardian', 'phone' => '03001234567'],
            'academic_info' => ['last_qualification' => 'Matric']
        ]);
        
        return TestRunner::assertGreaterThan(0, $this->testAdmissionId, 'Should create admission');
    }
    
    public function testAdmissionHasApplicationNumber(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
        
        $admission = $this->admissionModel->find($this->testAdmissionId);
        
        return TestRunner::assertNotNull($admission['application_no'], 'Admission should have application number');
    }
    
    public function testAdmissionDefaultStatusIsPending(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
        
        $admission = $this->admissionModel->find($this->testAdmissionId);
        
        return TestRunner::assertEquals('pending', $admission['status'], 'Default status should be pending');
    }
    
    public function testCanUpdateAdmissionStatus(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        $admin = $this->userModel->findByEmail('admin@ithm.edu.pk');
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
        
        $updated = $this->admissionModel->updateStatus($this->testAdmissionId, 'approved', $admin['id'], 'Approved');
        
        $admission = $this->admissionModel->find($this->testAdmissionId);
        
        return TestRunner::assertEquals('approved', $admission['status'], 'Status should be updated to approved');
    }
    
    public function testGetAdmissionWithDetails(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test Student'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
        
        $details = $this->admissionModel->getWithDetails($this->testAdmissionId);
        
        $hasDetails = isset($details['student_name']) && 
                      isset($details['course_name']) && 
                      isset($details['campus_name']);
        
        return TestRunner::assertTrue($hasDetails, 'Should have student, course, and campus names');
    }
    
    public function testCountByStatus(): bool
    {
        $counts = $this->admissionModel->countByStatus();
        
        $hasAllStatuses = isset($counts['pending']) && 
                          isset($counts['approved']) && 
                          isset($counts['rejected']) &&
                          isset($counts['total']);
        
        return TestRunner::assertTrue($hasAllStatuses, 'Should count all statuses');
    }
    
    public function testRollNumberGeneration(): bool
    {
        $campus = $this->campusModel->getMainCampus();
        
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
        
        $rollNo = $this->admissionModel->generateRollNumber($this->testAdmissionId);
        
        $hasFormat = strlen($rollNo) > 5 && strpos($rollNo, '-') !== false;
        
        return TestRunner::assertTrue($hasFormat, 'Roll number should have proper format');
    }
}

