<?php
/**
 * Campus Model
 */

namespace App\Models;

use App\Core\Model;

class Campus extends Model
{
    protected string $table = 'campuses';
    
    protected array $fillable = [
        'name', 'type', 'code', 'address', 'city',
        'phone', 'email', 'focal_person', 'is_active',
        'bank_account_name', 'bank_account_number', 'bank_name', 'bank_branch', 'iban',
        'contact_person_name', 'contact_person_phone', 'contact_person_email', 'logo'
    ];
    
    /**
     * Get all active campuses
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1);
    }
    
    /**
     * Get main campus
     */
    public function getMainCampus(): ?array
    {
        return $this->firstWhere('type', 'main');
    }
    
    /**
     * Get sub campuses
     */
    public function getSubCampuses(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE type = 'sub' AND is_active = 1 ORDER BY name"
        );
    }
    
    /**
     * Get campus with stats
     */
    public function getWithStats(int $id): ?array
    {
        $campus = $this->find($id);
        if (!$campus) return null;
        
        // Get student count
        $campus['student_count'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE campus_id = ? AND role_id = ?",
            [$id, ROLE_STUDENT]
        );
        
        // Get course count
        $campus['course_count'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM campus_courses WHERE campus_id = ? AND is_active = 1",
            [$id]
        );
        
        // Get pending admissions
        $campus['pending_admissions'] = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM admissions WHERE campus_id = ? AND status = 'pending'",
            [$id]
        );
        
        return $campus;
    }
    
    /**
     * Get all campuses with stats
     */
    public function getAllWithStats(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*,
                (SELECT COUNT(*) FROM users WHERE campus_id = c.id AND role_id = ?) as student_count,
                (SELECT COUNT(*) FROM campus_courses WHERE campus_id = c.id AND is_active = 1) as course_count,
                (SELECT COUNT(*) FROM admissions WHERE campus_id = c.id AND status = 'pending') as pending_admissions
             FROM {$this->table} c
             ORDER BY c.type DESC, c.name",
            [ROLE_STUDENT]
        );
    }
    
    /**
     * Get campus courses
     */
    public function getCourses(int $campusId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, cc.available_seats, cc.is_active as campus_active
             FROM courses c
             INNER JOIN campus_courses cc ON c.id = cc.course_id
             WHERE cc.campus_id = ? AND cc.is_active = 1
             ORDER BY c.name",
            [$campusId]
        );
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
            "SELECT id, name, type FROM {$this->table} WHERE is_active = 1 ORDER BY type DESC, name"
        );
    }
}

