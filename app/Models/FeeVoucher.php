<?php
/**
 * Fee Voucher Model
 */

namespace App\Models;

use App\Core\Model;

class FeeVoucher extends Model
{
    protected string $table = 'fee_vouchers';
    
    protected array $fillable = [
        'voucher_no', 'user_id', 'admission_id', 'campus_id',
        'fee_type', 'amount', 'fee_breakdown', 'due_date', 'status'
    ];
    
    /**
     * Generate unique voucher number
     */
    public function generateVoucherNo(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE YEAR(created_at) = ? AND MONTH(created_at) = ?",
            [$year, $month]
        );
        
        return 'V-' . $year . $month . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Create voucher for admission
     */
    public function createForAdmission(int $admissionId, float $amount, string $dueDate, ?array $feeBreakdown = null): int
    {
        $admission = $this->db->fetch(
            "SELECT user_id, campus_id FROM admissions WHERE id = ?",
            [$admissionId]
        );
        
        if (!$admission) {
            throw new \Exception('Admission not found');
        }
        
        $data = [
            'voucher_no' => $this->generateVoucherNo(),
            'user_id' => $admission['user_id'],
            'admission_id' => $admissionId,
            'campus_id' => $admission['campus_id'],
            'fee_type' => 'admission',
            'amount' => $amount,
            'due_date' => $dueDate,
            'status' => 'unpaid'
        ];
        
        // Store fee breakdown as JSON if provided
        if ($feeBreakdown !== null) {
            $data['fee_breakdown'] = json_encode($feeBreakdown);
        }
        
        return $this->create($data);
    }
    
    /**
     * Create voucher for semester fee
     */
    public function createForSemester(int $admissionId, float $amount, string $dueDate): int
    {
        $admission = $this->db->fetch(
            "SELECT user_id, campus_id FROM admissions WHERE id = ?",
            [$admissionId]
        );
        
        if (!$admission) {
            throw new \Exception('Admission not found');
        }
        
        return $this->create([
            'voucher_no' => $this->generateVoucherNo(),
            'user_id' => $admission['user_id'],
            'admission_id' => $admissionId,
            'campus_id' => $admission['campus_id'],
            'fee_type' => 'semester',
            'amount' => $amount,
            'due_date' => $dueDate,
            'status' => 'unpaid'
        ]);
    }
    
    /**
     * Create voucher for monthly fee
     */
    public function createForMonthly(int $admissionId, float $amount, string $dueDate): int
    {
        $admission = $this->db->fetch(
            "SELECT user_id, campus_id FROM admissions WHERE id = ?",
            [$admissionId]
        );
        
        if (!$admission) {
            throw new \Exception('Admission not found');
        }
        
        return $this->create([
            'voucher_no' => $this->generateVoucherNo(),
            'user_id' => $admission['user_id'],
            'admission_id' => $admissionId,
            'campus_id' => $admission['campus_id'],
            'fee_type' => 'monthly',
            'amount' => $amount,
            'due_date' => $dueDate,
            'status' => 'unpaid'
        ]);
    }
    
    /**
     * Check if voucher already exists for admission and fee type
     */
    public function voucherExists(int $admissionId, string $feeType, string $dueDate): bool
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} 
             WHERE admission_id = ? AND fee_type = ? AND due_date = ? AND status != 'cancelled'",
            [$admissionId, $feeType, $dueDate]
        ) > 0;
    }
    
    /**
     * Get voucher with details
     */
    public function getWithDetails(int $id): ?array
    {
        $voucher = $this->db->fetch(
            "SELECT v.*, u.name as student_name, u.email as student_email, u.phone as student_phone,
                    c.name as campus_name, c.address as campus_address, c.city as campus_city,
                    c.phone as campus_phone, c.email as campus_email,
                    c.bank_account_name, c.bank_account_number, c.bank_name, c.bank_branch, c.iban,
                    c.contact_person_name, c.contact_person_phone, c.contact_person_email, c.logo as campus_logo,
                    a.application_no, a.roll_number, a.shift as admission_shift,
                    co.name as course_name, co.code as course_code
             FROM {$this->table} v
             INNER JOIN users u ON v.user_id = u.id
             INNER JOIN campuses c ON v.campus_id = c.id
             LEFT JOIN admissions a ON v.admission_id = a.id
             LEFT JOIN courses co ON a.course_id = co.id
             WHERE v.id = ?",
            [$id]
        );
        
        if ($voucher) {
            // Decode fee breakdown JSON if exists
            if (!empty($voucher['fee_breakdown'])) {
                $voucher['fee_breakdown'] = is_string($voucher['fee_breakdown']) 
                    ? json_decode($voucher['fee_breakdown'], true) 
                    : $voucher['fee_breakdown'];
            }
            
            // Get payment if exists
            $voucher['payment'] = $this->db->fetch(
                "SELECT * FROM fee_payments WHERE voucher_id = ? ORDER BY created_at DESC LIMIT 1",
                [$id]
            );
            
            // Get voucher_id for payment reference
            if (empty($voucher['voucher_id']) && isset($voucher['id'])) {
                $voucher['voucher_id'] = $voucher['id'];
            }
        }
        
        return $voucher;
    }
    
    /**
     * Get vouchers by user
     */
    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT v.*, c.name as campus_name,
                    (SELECT fp.status FROM fee_payments fp WHERE fp.voucher_id = v.id ORDER BY fp.created_at DESC LIMIT 1) as payment_status
             FROM {$this->table} v
             INNER JOIN campuses c ON v.campus_id = c.id
             WHERE v.user_id = ?
             ORDER BY v.created_at DESC",
            [$userId]
        );
    }
    
    /**
     * Get vouchers by campus
     */
    public function getByCampus(int $campusId, array $filters = []): array
    {
        $sql = "SELECT v.*, u.name as student_name, u.email as student_email,
                       a.application_no, a.roll_number
                FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                LEFT JOIN admissions a ON v.admission_id = a.id
                WHERE v.campus_id = ?";
        $params = [$campusId];
        
        if (!empty($filters['status'])) {
            $sql .= " AND v.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['fee_type'])) {
            $sql .= " AND v.fee_type = ?";
            $params[] = $filters['fee_type'];
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get all vouchers
     */
    public function getAllVouchers(array $filters = []): array
    {
        $sql = "SELECT v.*, u.name as student_name, u.email as student_email,
                       c.name as campus_name, a.application_no
                FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                INNER JOIN campuses c ON v.campus_id = c.id
                LEFT JOIN admissions a ON v.admission_id = a.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND v.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['campus_id'])) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $filters['campus_id'];
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get pending verification vouchers
     */
    public function getPendingVerification(?int $campusId = null): array
    {
        $sql = "SELECT v.*, u.name as student_name, u.email as student_email,
                       c.name as campus_name, a.application_no,
                       fp.transaction_id, fp.proof_file, fp.payment_date
                FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                INNER JOIN campuses c ON v.campus_id = c.id
                LEFT JOIN admissions a ON v.admission_id = a.id
                INNER JOIN fee_payments fp ON fp.voucher_id = v.id AND fp.status = 'pending'
                WHERE v.status = 'pending_verification'";
        $params = [];
        
        if ($campusId) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " ORDER BY fp.payment_date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update voucher status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Get overdue vouchers
     */
    public function getOverdue(?int $campusId = null): array
    {
        $sql = "SELECT v.*, u.name as student_name, u.email as student_email, u.phone as student_phone,
                       c.name as campus_name
                FROM {$this->table} v
                INNER JOIN users u ON v.user_id = u.id
                INNER JOIN campuses c ON v.campus_id = c.id
                WHERE v.status = 'unpaid' AND v.due_date < CURDATE()";
        $params = [];
        
        if ($campusId) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " ORDER BY v.due_date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total pending fees (unpaid/overdue vouchers with no payment submitted)
     */
    public function getTotalPendingFees(?int $campusId = null): float
    {
        $campusFilter = $campusId ? " AND v.campus_id = ?" : "";
        $params = $campusId ? [$campusId] : [];
        
        $sql = "SELECT COALESCE(SUM(v.amount), 0) 
                FROM {$this->table} v
                WHERE v.status IN ('unpaid', 'overdue')
                AND NOT EXISTS (
                    SELECT 1 FROM fee_payments fp 
                    WHERE fp.voucher_id = v.id 
                    AND fp.status IN ('pending', 'verified')
                )" . $campusFilter;
        
        return (float) $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Mark overdue vouchers
     */
    public function markOverdue(): int
    {
        return $this->db->query(
            "UPDATE {$this->table} SET status = 'overdue' WHERE status = 'unpaid' AND due_date < CURDATE()"
        )->rowCount();
    }
    
    /**
     * Get fee defaulters
     */
    public function getDefaulters(?int $campusId = null): array
    {
        $sql = "SELECT u.id, u.name, u.email, u.phone,
                       COUNT(v.id) as overdue_count,
                       SUM(v.amount) as total_due,
                       c.name as campus_name
                FROM users u
                INNER JOIN {$this->table} v ON v.user_id = u.id
                INNER JOIN campuses c ON v.campus_id = c.id
                WHERE v.status IN ('unpaid', 'overdue') AND v.due_date < CURDATE()";
        $params = [];
        
        if ($campusId) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " GROUP BY u.id, u.name, u.email, u.phone, c.name ORDER BY total_due DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count by status
     */
    public function countByStatus(?int $campusId = null): array
    {
        $sql = "SELECT status, COUNT(*) as count, SUM(amount) as total_amount 
                FROM {$this->table}";
        $params = [];
        
        if ($campusId) {
            $sql .= " WHERE campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " GROUP BY status";
        
        $results = $this->db->fetchAll($sql, $params);
        
        $counts = [
            'unpaid' => ['count' => 0, 'amount' => 0],
            'pending_verification' => ['count' => 0, 'amount' => 0],
            'paid' => ['count' => 0, 'amount' => 0],
            'overdue' => ['count' => 0, 'amount' => 0]
        ];
        
        foreach ($results as $row) {
            $counts[$row['status']] = [
                'count' => (int) $row['count'],
                'amount' => (float) $row['total_amount']
            ];
        }
        
        return $counts;
    }
}

