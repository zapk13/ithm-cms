<?php
/**
 * File Upload Helper Class
 */

namespace App\Helpers;

class FileUploadHelper
{
    private array $errors = [];
    
    /**
     * Upload a file
     */
    public function upload(array $file, string $destination, array $options = []): ?string
    {
        $this->errors = [];
        
        // Default options
        $maxSize = $options['max_size'] ?? MAX_UPLOAD_SIZE;
        $allowedTypes = $options['allowed_types'] ?? ALLOWED_IMAGE_TYPES;
        $prefix = $options['prefix'] ?? '';
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($file['error']);
            return null;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $maxMb = $maxSize / (1024 * 1024);
            $this->errors[] = "File size exceeds maximum allowed ({$maxMb}MB).";
            return null;
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $this->errors[] = "File type not allowed.";
            return null;
        }
        
        // Create destination directory if not exists
        $fullDestination = UPLOADS_PATH . '/' . $destination;
        if (!is_dir($fullDestination)) {
            mkdir($fullDestination, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . uniqid() . '_' . time() . '.' . $extension;
        $filepath = $fullDestination . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->errors[] = "Failed to upload file.";
            return null;
        }
        
        // Return relative path for database storage
        return $destination . '/' . $filename;
    }
    
    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $files, string $destination, array $options = []): array
    {
        $uploaded = [];
        
        // Reorganize files array if needed
        if (isset($files['name']) && is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $result = $this->upload($file, $destination, $options);
                if ($result) {
                    $uploaded[] = $result;
                }
            }
        }
        
        return $uploaded;
    }
    
    /**
     * Delete a file
     */
    public function delete(string $filepath): bool
    {
        $fullPath = UPLOADS_PATH . '/' . $filepath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
    
    /**
     * Get upload errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get first error
     */
    public function error(): ?string
    {
        return $this->errors[0] ?? null;
    }
    
    /**
     * Get PHP upload error message
     */
    private function getUploadError(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in the form.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error.';
    }
    
    /**
     * Get file URL
     */
    public static function url(string $filepath): string
    {
        return BASE_URL . '/storage/uploads/' . $filepath;
    }
    
    /**
     * Check if file exists
     */
    public static function exists(string $filepath): bool
    {
        return file_exists(UPLOADS_PATH . '/' . $filepath);
    }
    
    /**
     * Get file size in human readable format
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

