<?php
/**
 * Course Management Tests
 */

namespace Tests;

use App\Models\Course;
use App\Models\Campus;

class CourseTest
{
    private Course $courseModel;
    private Campus $campusModel;
    
    public function setUp(): void
    {
        $this->courseModel = new Course();
        $this->campusModel = new Campus();
    }
    
    public function testCanCreateCourse(): bool
    {
        $courseId = $this->courseModel->create([
            'name' => 'Test Course',
            'code' => 'TC' . time(),
            'description' => 'Test course description',
            'duration_months' => 12,
            'total_seats' => 30,
            'is_active' => 1
        ]);
        
        $exists = $courseId > 0;
        
        // Clean up
        if ($courseId) {
            $this->courseModel->delete($courseId);
        }
        
        return TestRunner::assertTrue($exists, 'Should be able to create course');
    }
    
    public function testCourseCodeMustBeUnique(): bool
    {
        // Create a course first
        $code = 'UNIQUE' . time();
        $courseId = $this->courseModel->create([
            'name' => 'Unique Test',
            'code' => $code,
            'duration_months' => 6,
            'is_active' => 1
        ]);
        
        $exists = $this->courseModel->codeExists($code);
        
        // Clean up
        $this->courseModel->delete($courseId);
        
        return TestRunner::assertTrue($exists, 'Duplicate course code should be detected');
    }
    
    public function testCanAssignCourseToCapus(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        
        // Create test course
        $courseId = $this->courseModel->create([
            'name' => 'Assignment Test',
            'code' => 'AT' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
        
        // Assign to campus
        $assigned = $this->courseModel->assignToCampus($courseId, $mainCampus['id'], 50);
        
        // Clean up
        $this->courseModel->removeFromCampus($courseId, $mainCampus['id']);
        $this->courseModel->delete($courseId);
        
        return TestRunner::assertTrue($assigned, 'Should be able to assign course to campus');
    }
    
    public function testGetCoursesByCampus(): bool
    {
        $mainCampus = $this->campusModel->getMainCampus();
        
        // Create and assign a course
        $courseId = $this->courseModel->create([
            'name' => 'Campus Course Test',
            'code' => 'CCT' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
        
        $this->courseModel->assignToCampus($courseId, $mainCampus['id'], 30);
        
        $courses = $this->courseModel->getByCampus($mainCampus['id']);
        $found = false;
        foreach ($courses as $course) {
            if ($course['id'] == $courseId) {
                $found = true;
                break;
            }
        }
        
        // Clean up
        $this->courseModel->removeFromCampus($courseId, $mainCampus['id']);
        $this->courseModel->delete($courseId);
        
        return TestRunner::assertTrue($found, 'Course should appear in campus courses');
    }
    
    public function testGetActiveCourses(): bool
    {
        $courses = $this->courseModel->getActive();
        
        $allActive = true;
        foreach ($courses as $course) {
            if (!$course['is_active']) {
                $allActive = false;
                break;
            }
        }
        
        return TestRunner::assertTrue($allActive, 'All courses should be active');
    }
    
    public function testGetCourseForDropdown(): bool
    {
        // Create a course first
        $courseId = $this->courseModel->create([
            'name' => 'Dropdown Test',
            'code' => 'DT' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
        
        $courses = $this->courseModel->getForDropdown();
        $hasRequired = true;
        
        foreach ($courses as $course) {
            if (!isset($course['id']) || !isset($course['name']) || !isset($course['code'])) {
                $hasRequired = false;
                break;
            }
        }
        
        // Clean up
        $this->courseModel->delete($courseId);
        
        return TestRunner::assertTrue($hasRequired, 'Dropdown should have id, name, and code');
    }
}

