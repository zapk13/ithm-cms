<?php
/**
 * Course Model
 */

namespace App\Models;

use App\Core\Model;

class Course extends Model
{
    protected string $table = 'courses';
    
    protected array $fillable = [
        'name', 'code', 'description', 'duration_months', 'total_seats', 'is_active'
    ];
    
    /**
     * Get all active courses
     */
    public function getActive(): array
    {
        try {
            $courses = $this->db->fetchAll(
                "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name"
            );
            
            // Ensure all fields exist
            return array_map(function($course) {
                return [
                    'id' => (int)($course['id'] ?? 0),
                    'name' => $course['name'] ?? '',
                    'code' => $course['code'] ?? '',
                    'description' => $course['description'] ?? null,
                    'duration_months' => isset($course['duration_months']) ? (int)$course['duration_months'] : 0,
                    'total_seats' => isset($course['total_seats']) ? (int)$course['total_seats'] : 0,
                    'is_active' => isset($course['is_active']) ? (int)$course['is_active'] : 0
                ];
            }, $courses ?: []);
        } catch (\Exception $e) {
            error_log("Error in getActive: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all courses (including inactive)
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY name");
    }
    
    /**
     * Get course with campuses
     */
    public function getWithCampuses(int $id): ?array
    {
        $course = $this->find($id);
        if (!$course) return null;
        
        $course['campuses'] = $this->db->fetchAll(
            "SELECT c.*, cc.available_seats, cc.is_active as campus_active
             FROM campuses c
             INNER JOIN campus_courses cc ON c.id = cc.campus_id
             WHERE cc.course_id = ?
             ORDER BY c.type DESC, c.name",
            [$id]
        );
        
        return $course;
    }
    
    /**
     * Get courses by campus
     */
    public function getByCampus(int $campusId): array
    {
        try {
            $courses = $this->db->fetchAll(
                "SELECT c.*, cc.available_seats, fs.admission_fee, fs.semester_fee, fs.monthly_fee
                 FROM {$this->table} c
                 INNER JOIN campus_courses cc ON c.id = cc.course_id
                 LEFT JOIN fee_structures fs ON c.id = fs.course_id AND fs.campus_id = ?
                 WHERE cc.campus_id = ? AND cc.is_active = 1 AND c.is_active = 1
                 ORDER BY c.name",
                [$campusId, $campusId]
            );
            
            // Ensure all fields exist
            return array_map(function($course) {
                return [
                    'id' => (int)($course['id'] ?? 0),
                    'name' => $course['name'] ?? '',
                    'code' => $course['code'] ?? '',
                    'description' => $course['description'] ?? null,
                    'duration_months' => isset($course['duration_months']) ? (int)$course['duration_months'] : 0,
                    'total_seats' => isset($course['total_seats']) ? (int)$course['total_seats'] : 0,
                    'is_active' => isset($course['is_active']) ? (int)$course['is_active'] : 0,
                    'available_seats' => isset($course['available_seats']) ? (int)$course['available_seats'] : null,
                    'admission_fee' => isset($course['admission_fee']) ? (float)$course['admission_fee'] : null,
                    'semester_fee' => isset($course['semester_fee']) ? (float)$course['semester_fee'] : null,
                    'monthly_fee' => isset($course['monthly_fee']) ? (float)$course['monthly_fee'] : null
                ];
            }, $courses ?: []);
        } catch (\Exception $e) {
            error_log("Error in getByCampus: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Assign course to campus
     */
    public function assignToCampus(int $courseId, int $campusId, int $seats = 50): bool
    {
        // Check if already exists
        $exists = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM campus_courses WHERE course_id = ? AND campus_id = ?",
            [$courseId, $campusId]
        );
        
        if ($exists) {
            return $this->db->update(
                'campus_courses',
                ['is_active' => 1, 'available_seats' => $seats],
                'course_id = ? AND campus_id = ?',
                [$courseId, $campusId]
            ) > 0;
        }
        
        $this->db->insert('campus_courses', [
            'course_id' => $courseId,
            'campus_id' => $campusId,
            'available_seats' => $seats,
            'is_active' => 1
        ]);
        
        return true;
    }
    
    /**
     * Remove course from campus
     */
    public function removeFromCampus(int $courseId, int $campusId): bool
    {
        return $this->db->update(
            'campus_courses',
            ['is_active' => 0],
            'course_id = ? AND campus_id = ?',
            [$courseId, $campusId]
        ) > 0;
    }
    
    /**
     * Get available seats for course at campus
     */
    public function getAvailableSeats(int $courseId, int $campusId): int
    {
        $result = $this->db->fetch(
            "SELECT cc.available_seats,
                    (SELECT COUNT(*) FROM admissions 
                     WHERE course_id = ? AND campus_id = ? AND status = 'approved') as enrolled
             FROM campus_courses cc
             WHERE cc.course_id = ? AND cc.campus_id = ?",
            [$courseId, $campusId, $courseId, $campusId]
        );
        
        if (!$result) return 0;
        
        return max(0, $result['available_seats'] - $result['enrolled']);
    }
    
    /**
     * Check if code exists
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = ?";
        $params = [$code];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return $this->db->fetchColumn($sql, $params) > 0;
    }
    
    /**
     * Get for dropdown
     */
    public function getForDropdown(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, code FROM {$this->table} WHERE is_active = 1 ORDER BY name"
        );
    }
}

