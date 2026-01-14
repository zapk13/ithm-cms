<?php
/**
 * User Model
 */

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name', 'email', 'password', 'phone', 'cnic',
        'role_id', 'campus_id', 'profile_image', 'is_active', 'password_needs_reset'
    ];
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT u.*, r.name as role_name, r.slug as role_slug 
             FROM {$this->table} u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE u.email = ?",
            [$email]
        );
    }
    
    /**
     * Create user with hashed password
     */
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->create($data);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Update password
     */
    public function updatePassword(int $id, string $password): bool
    {
        return $this->db->update(
            $this->table,
            ['password' => password_hash($password, PASSWORD_BCRYPT)],
            'id = ?',
            [$id]
        ) > 0;
    }
    
    /**
     * Get user with role and campus info
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT u.*, r.name as role_name, r.slug as role_slug, c.name as campus_name 
             FROM {$this->table} u 
             LEFT JOIN roles r ON u.role_id = r.id 
             LEFT JOIN campuses c ON u.campus_id = c.id 
             WHERE u.id = ?",
            [$id]
        );
    }
    
    /**
     * Get all users with role and campus
     */
    public function getAllWithDetails(array $filters = []): array
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug, c.name as campus_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.id 
                LEFT JOIN campuses c ON u.campus_id = c.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['role_id'])) {
            $sql .= " AND u.role_id = ?";
            $params[] = $filters['role_id'];
        }
        
        if (!empty($filters['campus_id'])) {
            $sql .= " AND u.campus_id = ?";
            $params[] = $filters['campus_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get students
     */
    public function getStudents(array $filters = []): array
    {
        $filters['role_id'] = ROLE_STUDENT;
        return $this->getAllWithDetails($filters);
    }
    
    /**
     * Get students by campus
     */
    public function getStudentsByCampus(int $campusId): array
    {
        return $this->getAllWithDetails(['role_id' => ROLE_STUDENT, 'campus_id' => $campusId]);
    }
    
    /**
     * Count students
     */
    public function countStudents(?int $campusId = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role_id = ?";
        $params = [ROLE_STUDENT];
        
        if ($campusId) {
            $sql .= " AND campus_id = ?";
            $params[] = $campusId;
        }
        
        return (int) $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin(int $id): void
    {
        $this->db->update(
            $this->table,
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$id]
        );
    }
    
    /**
     * Generate password reset token
     * @param int $id User ID
     * @param int $hours Token expiry in hours (default: 1 hour)
     */
    public function createResetToken(int $id, int $hours = 1): string
    {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));
        
        $this->db->update(
            $this->table,
            [
                'reset_token' => $token,
                'reset_token_expiry' => $expiry
            ],
            'id = ?',
            [$id]
        );
        
        return $token;
    }
    
    /**
     * Find by reset token
     */
    public function findByResetToken(string $token): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} 
             WHERE reset_token = ? AND reset_token_expiry > NOW()",
            [$token]
        );
    }
    
    /**
     * Clear reset token
     */
    public function clearResetToken(int $id): void
    {
        $this->db->update(
            $this->table,
            ['reset_token' => null, 'reset_token_expiry' => null],
            'id = ?',
            [$id]
        );
    }
    
    /**
     * Clear password_needs_reset flag
     */
    public function clearPasswordNeedsReset(int $id): void
    {
        $this->db->update(
            $this->table,
            ['password_needs_reset' => 0],
            'id = ?',
            [$id]
        );
    }
}

