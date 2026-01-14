<?php
/**
 * Email Service
 * Handles sending emails via SMTP
 */

namespace App\Services;

use App\Models\Setting;

class EmailService
{
    private Setting $settingModel;
    private string $type; // 'internal' or 'external'
    
    public function __construct(string $type = 'external')
    {
        $this->settingModel = new Setting();
        $this->type = $type;
    }
    
    /**
     * Send email using SMTP
     */
    public function send(string $to, string $subject, string $body, string $toName = ''): bool
    {
        $prefix = 'smtp_' . $this->type . '_';
        
        // Check if external should use internal settings
        if ($this->type === 'external' && $this->settingModel->get('smtp_external_same_as_internal', '0') === '1') {
            $prefix = 'smtp_internal_';
        }
        
        $host = $this->settingModel->get($prefix . 'host');
        $port = (int)$this->settingModel->get($prefix . 'port', 587);
        $username = $this->settingModel->get($prefix . 'username');
        $password = $this->settingModel->get($prefix . 'password');
        $encryption = $this->settingModel->get($prefix . 'encryption', 'tls');
        $fromEmail = $this->settingModel->get($prefix . 'from_email');
        $fromName = $this->settingModel->get($prefix . 'from_name', 'ITHM CMS');
        
        if (empty($host) || empty($username) || empty($fromEmail)) {
            error_log("EmailService: SMTP settings not configured for type: {$this->type}");
            return false;
        }
        
        try {
            // Use PHP's mail() function with SMTP headers
            // For production, consider using PHPMailer library
            $headers = [
                'From: ' . $this->formatEmailAddress($fromEmail, $fromName),
                'Reply-To: ' . $fromEmail,
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'X-Mailer: PHP/' . phpversion()
            ];
            
            // For SMTP, we'll use a simple approach
            // In production, install PHPMailer: composer require phpmailer/phpmailer
            $success = @mail(
                $toName ? $this->formatEmailAddress($to, $toName) : $to,
                $subject,
                $body,
                implode("\r\n", $headers)
            );
            
            if (!$success) {
                error_log("EmailService: Failed to send email to {$to}");
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("EmailService Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format email address with name
     */
    private function formatEmailAddress(string $email, string $name): string
    {
        return $name ? "{$name} <{$email}>" : $email;
    }
    
    /**
     * Send welcome email with password reset link
     */
    public function sendWelcomeEmail(string $to, string $toName, string $resetLink, string $applicationNo = ''): bool
    {
        $instituteName = $this->settingModel->get('institute_name', 'ITHM');
        
        $subject = "Welcome to {$instituteName} - Set Your Password";
        
        $body = $this->getWelcomeEmailTemplate($toName, $resetLink, $applicationNo, $instituteName);
        
        return $this->send($to, $subject, $body, $toName);
    }
    
    /**
     * Get welcome email template
     */
    private function getWelcomeEmailTemplate(string $name, string $resetLink, string $applicationNo, string $instituteName): string
    {
        $appUrl = BASE_URL;
        
        $appNoHtml = '';
        if (!empty($applicationNo)) {
            $appNoHtml = <<<HTML
        <div style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0;">
                <strong>Application Number:</strong> <span style="font-size: 18px; color: #2196F3;">{$applicationNo}</span>
            </p>
        </div>
HTML;
        }
        
        $template = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {$instituteName}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0;">Welcome to {$instituteName}!</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px;">Dear <strong>{$name}</strong>,</p>
        
        <p>Your admission application has been successfully submitted to {$instituteName}.</p>
        
        {$appNoHtml}
        
        <p>To access your student portal and manage your account, please set your password by clicking the button below:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{$resetLink}" 
               style="background: #667eea; color: #fff; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Set Your Password
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px;">Or copy and paste this link into your browser:</p>
        <p style="color: #667eea; word-break: break-all; font-size: 12px; background: #f0f0f0; padding: 10px; border-radius: 5px;">{$resetLink}</p>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0; color: #856404;">
                <strong>Important:</strong> This link will expire in 24 hours. If you don't set your password within this time, please contact the administration office.
            </p>
        </div>
        
        <p>After setting your password, you can log in to your student portal to:</p>
        <ul>
            <li>View your admission status</li>
            <li>Check fee vouchers and payments</li>
            <li>Download certificates</li>
            <li>Update your profile</li>
            <li>View notifications</li>
        </ul>
        
        <p>If you have any questions or need assistance, please contact our support team.</p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #666; font-size: 12px; margin: 0;">
            This is an automated email. Please do not reply to this message.<br>
            &copy; {$instituteName} - All rights reserved.
        </p>
    </div>
</body>
</html>
HTML;
        
        return $template;
    }
}

