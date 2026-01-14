<?php
/**
 * Certificate Model
 */

namespace App\Models;

use App\Core\Model;

class Certificate extends Model
{
    protected string $table = 'certificates';
    
    protected array $fillable = [
        'user_id', 'admission_id', 'course_id', 'certificate_type', 'file_path', 'issued_by'
    ];
    
    /**
     * Upload certificate
     */
    public function uploadCertificate(int $userId, int $admissionId, int $courseId, string $filePath, int $issuedBy, string $type = 'completion'): int
    {
        return $this->create([
            'user_id' => $userId,
            'admission_id' => $admissionId,
            'course_id' => $courseId,
            'certificate_type' => $type,
            'file_path' => $filePath,
            'issued_by' => $issuedBy
        ]);
    }
    
    /**
     * Get certificates by user
     */
    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, co.name as course_name, co.code as course_code,
                    u.name as issued_by_name
             FROM {$this->table} c
             INNER JOIN courses co ON c.course_id = co.id
             LEFT JOIN users u ON c.issued_by = u.id
             WHERE c.user_id = ?
             ORDER BY c.issued_at DESC",
            [$userId]
        );
    }
    
    /**
     * Get certificate with details
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT c.*, 
                    u.name as student_name, u.email as student_email,
                    co.name as course_name, co.code as course_code,
                    a.roll_number, a.batch,
                    ca.name as campus_name,
                    iu.name as issued_by_name
             FROM {$this->table} c
             INNER JOIN users u ON c.user_id = u.id
             INNER JOIN courses co ON c.course_id = co.id
             INNER JOIN admissions a ON c.admission_id = a.id
             INNER JOIN campuses ca ON a.campus_id = ca.id
             LEFT JOIN users iu ON c.issued_by = iu.id
             WHERE c.id = ?",
            [$id]
        );
    }
    
    /**
     * Get certificates by campus
     */
    public function getByCampus(int $campusId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.name as student_name, co.name as course_name,
                    a.roll_number
             FROM {$this->table} c
             INNER JOIN users u ON c.user_id = u.id
             INNER JOIN courses co ON c.course_id = co.id
             INNER JOIN admissions a ON c.admission_id = a.id
             WHERE a.campus_id = ?
             ORDER BY c.issued_at DESC",
            [$campusId]
        );
    }
    
    /**
     * Check if certificate exists for admission
     */
    public function existsForAdmission(int $admissionId): bool
    {
        return $this->count('admission_id = ?', [$admissionId]) > 0;
    }
}

