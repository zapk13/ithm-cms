<?php
/**
 * Admission Document Model
 */

namespace App\Models;

use App\Core\Model;

class AdmissionDocument extends Model
{
    protected string $table = 'admission_documents';
    
    protected array $fillable = [
        'admission_id', 'document_type', 'file_path', 'original_name',
        'status', 'remarks', 'verified_by', 'verified_at'
    ];
    
    /**
     * Get documents by admission
     */
    public function getByAdmission(int $admissionId): array
    {
        return $this->db->fetchAll(
            "SELECT ad.*, u.name as verifier_name
             FROM {$this->table} ad
             LEFT JOIN users u ON ad.verified_by = u.id
             WHERE ad.admission_id = ?
             ORDER BY ad.created_at",
            [$admissionId]
        );
    }
    
    /**
     * Upload document
     */
    public function uploadDocument(int $admissionId, string $type, string $filePath, string $originalName): int
    {
        // Check if document type already exists for this admission
        $existing = $this->db->fetch(
            "SELECT id FROM {$this->table} WHERE admission_id = ? AND document_type = ?",
            [$admissionId, $type]
        );
        
        if ($existing) {
            // Update existing document
            $this->update($existing['id'], [
                'file_path' => $filePath,
                'original_name' => $originalName,
                'status' => 'pending'
            ]);
            return $existing['id'];
        }
        
        return $this->create([
            'admission_id' => $admissionId,
            'document_type' => $type,
            'file_path' => $filePath,
            'original_name' => $originalName,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Verify document
     */
    public function verify(int $id, int $verifierId, string $status = 'verified', ?string $remarks = null): bool
    {
        return $this->db->update(
            $this->table,
            [
                'status' => $status,
                'verified_by' => $verifierId,
                'verified_at' => date('Y-m-d H:i:s'),
                'remarks' => $remarks
            ],
            'id = ?',
            [$id]
        ) > 0;
    }
    
    /**
     * Check if all required documents are uploaded
     */
    public function hasRequiredDocuments(int $admissionId): bool
    {
        $required = ['cnic_front', 'photo'];
        
        $uploaded = $this->db->fetchAll(
            "SELECT document_type FROM {$this->table} WHERE admission_id = ?",
            [$admissionId]
        );
        
        $uploadedTypes = array_column($uploaded, 'document_type');
        
        foreach ($required as $type) {
            if (!in_array($type, $uploadedTypes)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get pending documents count for admission
     */
    public function getPendingCount(int $admissionId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE admission_id = ? AND status = 'pending'",
            [$admissionId]
        );
    }
}

