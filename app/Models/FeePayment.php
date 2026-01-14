<?php
/**
 * Fee Payment Model
 */

namespace App\Models;

use App\Core\Model;

class FeePayment extends Model
{
    protected string $table = 'fee_payments';
    
    protected array $fillable = [
        'voucher_id', 'amount_paid', 'transaction_id', 'payment_method',
        'proof_file', 'status', 'remarks', 'verified_by', 'verified_at'
    ];
    
    /**
     * Submit payment
     */
    public function submitPayment(int $voucherId, float $amount, string $transactionId, string $proofFile): int
    {
        $paymentId = $this->create([
            'voucher_id' => $voucherId,
            'amount_paid' => $amount,
            'transaction_id' => $transactionId,
            'proof_file' => $proofFile,
            'status' => 'pending'
        ]);
        
        // Update voucher status
        $this->db->update('fee_vouchers', ['status' => 'pending_verification'], 'id = ?', [$voucherId]);
        
        return $paymentId;
    }
    
    /**
     * Get payment with voucher details
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT fp.*, v.voucher_no, v.amount as voucher_amount, v.due_date, v.fee_type,
                    u.name as student_name, u.email as student_email,
                    ver.name as verifier_name
             FROM {$this->table} fp
             INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
             INNER JOIN users u ON v.user_id = u.id
             LEFT JOIN users ver ON fp.verified_by = ver.id
             WHERE fp.id = ?",
            [$id]
        );
    }
    
    /**
     * Get payments by voucher
     */
    public function getByVoucher(int $voucherId): array
    {
        return $this->db->fetchAll(
            "SELECT fp.*, u.name as verifier_name
             FROM {$this->table} fp
             LEFT JOIN users u ON fp.verified_by = u.id
             WHERE fp.voucher_id = ?
             ORDER BY fp.created_at DESC",
            [$voucherId]
        );
    }
    
    /**
     * Verify payment
     */
    public function verify(int $id, int $verifierId, string $status = 'verified', ?string $remarks = null): bool
    {
        $payment = $this->find($id);
        if (!$payment) return false;
        
        $updated = $this->db->update(
            $this->table,
            [
                'status' => $status,
                'verified_by' => $verifierId,
                'verified_at' => date('Y-m-d H:i:s'),
                'remarks' => $remarks
            ],
            'id = ?',
            [$id]
        );
        
        if ($updated && $status === 'verified') {
            // Update voucher status to paid
            $this->db->update('fee_vouchers', ['status' => 'paid'], 'id = ?', [$payment['voucher_id']]);
        } elseif ($updated && $status === 'rejected') {
            // Update voucher status back to unpaid
            $this->db->update('fee_vouchers', ['status' => 'unpaid'], 'id = ?', [$payment['voucher_id']]);
        }
        
        return $updated > 0;
    }
    
    /**
     * Get pending payments
     */
    public function getPending(?int $campusId = null): array
    {
        $sql = "SELECT fp.*, v.voucher_no, v.amount as voucher_amount, v.due_date,
                       u.name as student_name, u.email as student_email,
                       c.name as campus_name
                FROM {$this->table} fp
                INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
                INNER JOIN users u ON v.user_id = u.id
                INNER JOIN campuses c ON v.campus_id = c.id
                WHERE fp.status = 'pending'";
        $params = [];
        
        if ($campusId) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $campusId;
        }
        
        $sql .= " ORDER BY fp.payment_date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count pending payments
     */
    public function countPending(?int $campusId = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} fp
                INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
                WHERE fp.status = 'pending'";
        $params = [];
        
        if ($campusId) {
            $sql .= " AND v.campus_id = ?";
            $params[] = $campusId;
        }
        
        return (int) $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Get financial statistics
     */
    public function getFinancialStats(?int $campusId = null): array
    {
        $campusFilter = $campusId ? " AND v.campus_id = ?" : "";
        $params = $campusId ? [$campusId] : [];
        
        // Total verified fees (sum of verified payments)
        $totalVerified = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(fp.amount_paid), 0) 
             FROM {$this->table} fp
             INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
             WHERE fp.status = 'verified'" . $campusFilter,
            $params
        );
        
        // Payments currently under verification
        $totalPendingVerification = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(fp.amount_paid), 0) 
             FROM {$this->table} fp
             INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
             WHERE fp.status = 'pending'" . $campusFilter,
            $params
        );
        
        // Payments that were rejected during verification
        $totalRejected = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(fp.amount_paid), 0) 
             FROM {$this->table} fp
             INNER JOIN fee_vouchers v ON fp.voucher_id = v.id
             WHERE fp.status = 'rejected'" . $campusFilter,
            $params
        );
        
        return [
            'total_verified' => (float) $totalVerified,
            'total_pending_verification' => (float) $totalPendingVerification,
            'total_rejected' => (float) $totalRejected
        ];
    }
}

