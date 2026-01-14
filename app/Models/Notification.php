<?php
/**
 * Notification Model
 */

namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected string $table = 'notifications';
    
    protected array $fillable = [
        'user_id', 'title', 'message', 'type', 'reference_type', 'reference_id', 'is_read'
    ];
    
    /**
     * Create notification
     */
    public function notify(int $userId, string $title, string $message, string $type = 'system', ?string $refType = null, ?int $refId = null): int
    {
        return $this->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'is_read' => 0
        ]);
    }
    
    /**
     * Get notifications by user
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$userId, $limit]
        );
    }
    
    /**
     * Get unread notifications by user
     */
    public function getUnreadByUser(int $userId): array
    {
        return $this->where('user_id', $userId);
    }
    
    /**
     * Count unread notifications
     */
    public function countUnread(int $userId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
    }
    
    /**
     * Mark as read
     */
    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]);
    }
    
    /**
     * Mark all as read for user
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->db->update(
            $this->table,
            ['is_read' => 1],
            'user_id = ? AND is_read = 0',
            [$userId]
        );
    }
    
    /**
     * Send admission status notification
     */
    public function notifyAdmissionStatus(int $userId, int $admissionId, string $status, string $applicationNo): void
    {
        $titles = [
            'pending' => 'Application Received',
            'approved' => 'Application Approved',
            'rejected' => 'Application Rejected',
            'update_required' => 'Application Update Required'
        ];
        
        $messages = [
            'pending' => "Your admission application ({$applicationNo}) has been received and is under review.",
            'approved' => "Congratulations! Your admission application ({$applicationNo}) has been approved. Please pay the fee voucher to complete your enrollment.",
            'rejected' => "We regret to inform you that your admission application ({$applicationNo}) has been rejected. Please contact the administration for more details.",
            'update_required' => "Your admission application ({$applicationNo}) requires some updates. Please login and update your application."
        ];
        
        $this->notify(
            $userId,
            $titles[$status] ?? 'Admission Update',
            $messages[$status] ?? 'Your admission status has been updated.',
            'admission',
            'admission',
            $admissionId
        );
    }
    
    /**
     * Send fee voucher notification
     */
    public function notifyFeeVoucher(int $userId, int $voucherId, string $voucherNo, float $amount, string $dueDate): void
    {
        $this->notify(
            $userId,
            'New Fee Voucher Generated',
            "A new fee voucher ({$voucherNo}) of PKR " . number_format($amount, 2) . " has been generated. Due date: {$dueDate}. Please download and pay before the due date.",
            'fee',
            'fee_voucher',
            $voucherId
        );
    }
    
    /**
     * Send payment verification notification
     */
    public function notifyPaymentStatus(int $userId, int $voucherId, string $voucherNo, string $status): void
    {
        $titles = [
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Rejected'
        ];
        
        $messages = [
            'verified' => "Your payment for voucher ({$voucherNo}) has been verified successfully. Your enrollment is now complete.",
            'rejected' => "Your payment for voucher ({$voucherNo}) has been rejected. Please contact the administration or submit a new payment proof."
        ];
        
        $this->notify(
            $userId,
            $titles[$status] ?? 'Payment Update',
            $messages[$status] ?? 'Your payment status has been updated.',
            'fee',
            'fee_voucher',
            $voucherId
        );
    }
    
    /**
     * Send fee reminder
     */
    public function sendFeeReminder(int $userId, string $voucherNo, float $amount, string $dueDate): void
    {
        $this->notify(
            $userId,
            'Fee Payment Reminder',
            "This is a reminder that your fee voucher ({$voucherNo}) of PKR " . number_format($amount, 2) . " is due on {$dueDate}. Please make the payment to avoid late fees.",
            'manual',
            null,
            null
        );
    }
    
    /**
     * Send certificate notification
     */
    public function notifyCertificate(int $userId, int $certificateId, string $courseName): void
    {
        $this->notify(
            $userId,
            'Certificate Available',
            "Your certificate for {$courseName} is now available for download. Please login to download your certificate and collect the printed copy from the campus.",
            'certificate',
            'certificate',
            $certificateId
        );
    }
    
    /**
     * Bulk send notifications
     */
    public function bulkNotify(array $userIds, string $title, string $message, string $type = 'manual'): int
    {
        $count = 0;
        foreach ($userIds as $userId) {
            $this->notify($userId, $title, $message, $type);
            $count++;
        }
        return $count;
    }
}

