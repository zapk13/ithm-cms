<?php
/**
 * Fee Structure Model
 */

namespace App\Models;

use App\Core\Model;

class FeeStructure extends Model
{
    protected string $table = 'fee_structures';
    
    protected array $fillable = [
        'course_id', 'campus_id', 'shift', 'admission_fee', 'tuition_fee',
        'semester_fee', 'monthly_fee', 'exam_fee', 'other_charges',
        'currency', 'is_active'
    ];
    
    /**
     * Get fee structure for course, campus and shift
     */
    public function getForCourseAndCampus(int $courseId, int $campusId, string $shift = 'morning'): ?array
    {
        return $this->db->fetch(
            "SELECT fs.*, c.name as course_name, ca.name as campus_name
             FROM {$this->table} fs
             INNER JOIN courses c ON fs.course_id = c.id
             INNER JOIN campuses ca ON fs.campus_id = ca.id
             WHERE fs.course_id = ? AND fs.campus_id = ? AND fs.shift = ?",
            [$courseId, $campusId, $shift]
        );
    }
    
    /**
     * Get all fee structures with course and campus details
     */
    public function getAllWithDetails(): array
    {
        return $this->db->fetchAll(
            "SELECT fs.*, c.name as course_name, c.code as course_code, 
                    ca.name as campus_name, ca.type as campus_type
             FROM {$this->table} fs
             INNER JOIN courses c ON fs.course_id = c.id
             INNER JOIN campuses ca ON fs.campus_id = ca.id
             WHERE fs.is_active = 1
             ORDER BY ca.type DESC, ca.name, c.name, fs.shift"
        );
    }
    
    /**
     * Get fee structures by campus
     */
    public function getByCampus(int $campusId, ?string $shift = null): array
    {
        $sql = "SELECT fs.*, c.name as course_name, c.code as course_code
             FROM {$this->table} fs
             INNER JOIN courses c ON fs.course_id = c.id
             WHERE fs.campus_id = ? AND fs.is_active = 1";
        $params = [$campusId];
        
        if ($shift) {
            $sql .= " AND fs.shift = ?";
            $params[] = $shift;
        }
        
        $sql .= " ORDER BY c.name, fs.shift";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Create or update fee structure
     */
    public function createOrUpdate(array $data): int
    {
        $shift = $data['shift'] ?? 'morning';
        $existing = $this->getForCourseAndCampus($data['course_id'], $data['campus_id'], $shift);
        
        if ($existing) {
            $this->update($existing['id'], $data);
            return $existing['id'];
        }
        
        // Ensure shift is set
        if (!isset($data['shift'])) {
            $data['shift'] = 'morning';
        }
        
        return $this->create($data);
    }
    
    /**
     * Calculate total admission fee
     */
    public function calculateAdmissionFee(int $courseId, int $campusId, string $shift = 'morning'): float
    {
        $structure = $this->getForCourseAndCampus($courseId, $campusId, $shift);
        
        if (!$structure) return 0;
        
        return (float) $structure['admission_fee'] + (float) $structure['tuition_fee'];
    }
    
    /**
     * Get total fee breakdown
     */
    public function getFeeBreakdown(int $courseId, int $campusId, string $shift = 'morning'): array
    {
        $structure = $this->getForCourseAndCampus($courseId, $campusId, $shift);
        
        if (!$structure) {
            return [
                'admission_fee' => 0,
                'tuition_fee' => 0,
                'semester_fee' => 0,
                'monthly_fee' => 0,
                'exam_fee' => 0,
                'other_charges' => 0,
                'total' => 0,
                'currency' => 'PKR'
            ];
        }
        
        $total = (float) $structure['admission_fee'] + 
                 (float) $structure['tuition_fee'] + 
                 (float) $structure['semester_fee'] +
                 (float) $structure['monthly_fee'] +
                 (float) $structure['exam_fee'] +
                 (float) $structure['other_charges'];
        
        return [
            'admission_fee' => (float) $structure['admission_fee'],
            'tuition_fee' => (float) $structure['tuition_fee'],
            'semester_fee' => (float) $structure['semester_fee'],
            'monthly_fee' => (float) $structure['monthly_fee'],
            'exam_fee' => (float) $structure['exam_fee'],
            'other_charges' => (float) $structure['other_charges'],
            'total' => $total,
            'currency' => $structure['currency'] ?? 'PKR',
            'shift' => $structure['shift'] ?? 'morning'
        ];
    }
}

