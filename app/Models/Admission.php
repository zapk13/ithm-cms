<?php
/**
 * Admission Model
 */

namespace App\Models;

use App\Core\Model;

class Admission extends Model
{
    protected string $table = 'admissions';
    
    protected array $fillable = [
        'user_id', 'course_id', 'campus_id', 'application_no', 'roll_number',
        'batch', 'shift', 'status', 'is_trashed', 'personal_info', 'guardian_info',
        'academic_info', 'admin_remarks', 'reviewed_by', 'reviewed_at'
    ];
    
    /**
     * Generate unique application number
     */
    public function generateApplicationNo(): string
    {
        $year = date('Y');
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE YEAR(created_at) = ?",
            [$year]
        );
        
        return 'APP-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create admission with application number
     */
    public function createAdmission(array $data): int
    {
        $data['application_no'] = $this->generateApplicationNo();
        
        // Encode JSON fields
        if (is_array($data['personal_info'])) {
            $data['personal_info'] = json_encode($data['personal_info']);
        }
        if (is_array($data['guardian_info'])) {
            $data['guardian_info'] = json_encode($data['guardian_info']);
        }
        if (is_array($data['academic_info'])) {
            $data['academic_info'] = json_encode($data['academic_info']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Get admission with full details
     */
    public function getWithDetails(int $id): ?array
    {
        $admission = $this->db->fetch(
            "SELECT a.*, 
                    u.name as student_name, u.email as student_email, u.phone as student_phone,
                    c.name as course_name, c.code as course_code, c.duration_months,
                    ca.name as campus_name, ca.type as campus_type, ca.address as campus_address,
                    ca.city as campus_city, ca.phone as campus_phone, ca.email as campus_email,
                    ca.logo as campus_logo,
                    r.name as reviewer_name
             FROM {$this->table} a
             INNER JOIN users u ON a.user_id = u.id
             INNER JOIN courses c ON a.course_id = c.id
             INNER JOIN campuses ca ON a.campus_id = ca.id
             LEFT JOIN users r ON a.reviewed_by = r.id
             WHERE a.id = ?",
            [$id]
        );
        
        if ($admission) {
            $admission['personal_info'] = json_decode($admission['personal_info'], true);
            $admission['guardian_info'] = json_decode($admission['guardian_info'], true);
            $admission['academic_info'] = json_decode($admission['academic_info'], true);
            
            // Get documents
            $admission['documents'] = $this->db->fetchAll(
                "SELECT * FROM admission_documents WHERE admission_id = ?",
                [$id]
            );
        }
        
        return $admission;
    }
    
    /**
     * Get admissions by user
     */
    public function getByUser(int $userId): array
    {
        $admissions = $this->db->fetchAll(
            "SELECT a.*, c.name as course_name, c.code as course_code,
                    ca.name as campus_name
             FROM {$this->table} a
             INNER JOIN courses c ON a.course_id = c.id
             INNER JOIN campuses ca ON a.campus_id = ca.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC",
            [$userId]
        );
        
        foreach ($admissions as &$admission) {
            $admission['personal_info'] = json_decode($admission['personal_info'], true);
        }
        
        return $admissions;
    }
    
    /**
     * Get admissions by campus
     */
    public function getByCampus(int $campusId, array $filters = []): array
    {
        $sql = "SELECT a.*, u.name as student_name, u.email as student_email,
                       c.name as course_name, c.code as course_code
                FROM {$this->table} a
                INNER JOIN users u ON a.user_id = u.id
                INNER JOIN courses c ON a.course_id = c.id
                WHERE a.campus_id = ?";
        $params = [$campusId];
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['course_id'])) {
            $sql .= " AND a.course_id = ?";
            $params[] = $filters['course_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR a.application_no LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get all admissions (for main campus admin)
     */
    public function getAllAdmissions(array $filters = []): array
    {
        $sql = "SELECT a.*, u.name as student_name, u.email as student_email,
                       c.name as course_name, c.code as course_code,
                       ca.name as campus_name
                FROM {$this->table} a
                INNER JOIN users u ON a.user_id = u.id
                INNER JOIN courses c ON a.course_id = c.id
                INNER JOIN campuses ca ON a.campus_id = ca.id
                WHERE a.is_trashed = 0";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['campus_id'])) {
            $sql .= " AND a.campus_id = ?";
            $params[] = $filters['campus_id'];
        }
        
        if (!empty($filters['course_id'])) {
            $sql .= " AND a.course_id = ?";
            $params[] = $filters['course_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR a.application_no LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update admission status
     */
    public function updateStatus(int $id, string $status, int $reviewerId, ?string $remarks = null): bool
    {
        return $this->db->update(
            $this->table,
            [
                'status' => $status,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'admin_remarks' => $remarks
            ],
            'id = ?',
            [$id]
        ) > 0;
    }
    
    /**
     * Assign roll number
     */
    public function assignRollNumber(int $id, string $rollNumber): bool
    {
        return $this->update($id, ['roll_number' => $rollNumber]);
    }
    
    /**
     * Generate roll number
     */
    public function generateRollNumber(int $admissionId): string
    {
        $admission = $this->find($admissionId);
        if (!$admission) return '';
        
        $campus = $this->db->fetch("SELECT code FROM campuses WHERE id = ?", [$admission['campus_id']]);
        $course = $this->db->fetch("SELECT code FROM courses WHERE id = ?", [$admission['course_id']]);
        
        $year = date('y');
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE campus_id = ? AND course_id = ? AND roll_number IS NOT NULL",
            [$admission['campus_id'], $admission['course_id']]
        );
        
        return strtoupper($campus['code']) . '-' . 
               strtoupper($course['code']) . '-' . 
               $year . '-' . 
               str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Count by status
     */
    public function countByStatus(?int $campusId = null): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if ($campusId) {
            $sql .= " WHERE campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " GROUP BY status";
        
        $results = $this->db->fetchAll($sql, $params);
        
        $counts = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'update_required' => 0,
            'total' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['status']] = (int) $row['count'];
            $counts['total'] += (int) $row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Check if user has pending admission for course
     */
    public function hasPendingForCourse(int $userId, int $courseId): bool
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE user_id = ? AND course_id = ? AND status IN ('pending', 'approved')",
            [$userId, $courseId]
        ) > 0;
    }
}

