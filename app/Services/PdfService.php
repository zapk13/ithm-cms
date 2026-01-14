<?php
/**
 * PDF Service
 * Handles PDF generation using TCPDF or browser print-to-PDF
 */

namespace App\Services;

// For production, install TCPDF via Composer: composer require tecnickcom/tcpdf
// Or use dompdf: composer require dompdf/dompdf

class PdfService
{
    /**
     * Generate PDF using TCPDF or fallback to browser print-to-PDF
     */
    public function generate(string $html, string $title = 'Document', string $filename = 'document.pdf'): void
    {
        // Clear any previous output
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Check if TCPDF is available
        if (class_exists('TCPDF')) {
            $this->generateWithTcpdf($html, $title, $filename);
        } else {
            // Fallback: Use browser print-to-PDF
            $this->generateWithBrowserPrint($html, $title, $filename);
        }
    }
    
    /**
     * Generate PDF using TCPDF
     */
    private function generateWithTcpdf(string $html, string $title, string $filename): void
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('ITHM CMS');
        $pdf->SetAuthor('ITHM CMS');
        $pdf->SetTitle($title);
        $pdf->SetSubject($title);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('dejavusans', '', 10);
        
        // Print HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        $pdf->Output($filename, 'D'); // 'D' = download, 'I' = inline
        exit;
    }
    
    /**
     * Generate PDF using browser print-to-PDF
     * Opens print dialog - user can save as PDF
     */
    private function generateWithBrowserPrint(string $html, string $title, string $filename): void
    {
        // Output HTML with print styles
        header('Content-Type: text/html; charset=UTF-8');
        
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1a1a1a;
            background: white;
            padding: 20px;
            max-width: 210mm;
            margin: 0 auto;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            text-align: center;
            margin: 0 0 15px 0;
            font-size: 22px;
            font-weight: bold;
            color: #1e40af;
            letter-spacing: 0.5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td, table th {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #374151;
            font-size: 10px;
        }
        table td {
            color: #1f2937;
        }
        h2 {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 600;
            color: #1e40af;
        }
        .print-instructions {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .print-instructions h3 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        .print-instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        .print-instructions li {
            margin: 5px 0;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-height: 80px;
            max-width: 200px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }
        .bank-info-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .bank-info-box h3 {
            margin: 0 0 12px 0;
            color: #0369a1;
            font-size: 13px;
            font-weight: 600;
        }
        .bank-info-box table {
            margin: 0;
            background: white;
        }
        .bank-info-box table th {
            background: #e0f2fe;
            width: 35%;
        }
    </style>
    <script>
        // Auto-trigger print dialog after a short delay
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</head>
<body>
    <div class="no-print print-instructions">
        <h3><i class="fas fa-info-circle"></i> How to Save as PDF:</h3>
        <ol>
            <li>Click the "Print" button below or press Ctrl+P (Cmd+P on Mac)</li>
            <li>In the print dialog, select "Save as PDF" as the destination</li>
            <li>Click "Save" to download the PDF</li>
        </ol>
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-top: 10px;">
            <i class="fas fa-print"></i> Print / Save as PDF
        </button>
    </div>
    
    ' . $html . '
    
</body>
</html>';
        exit;
    }
    
    /**
     * Get campus logo HTML
     */
    private function getCampusLogoHtml(?string $logoPath): string
    {
        if (!$logoPath) {
            return '';
        }
        
        $fullPath = defined('UPLOADS_PATH') ? UPLOADS_PATH . '/' . $logoPath : __DIR__ . '/../../storage/uploads/' . $logoPath;
        
        if (!file_exists($fullPath)) {
            return '';
        }
        
        $imageData = base64_encode(file_get_contents($fullPath));
        $imageType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeType = 'image/jpeg';
        if ($imageType === 'png') $mimeType = 'image/png';
        elseif ($imageType === 'gif') $mimeType = 'image/gif';
        elseif ($imageType === 'webp') $mimeType = 'image/webp';
        
        return '<div class="logo-container">
            <img src="data:' . $mimeType . ';base64,' . $imageData . '" alt="Campus Logo" />
        </div>';
    }
    
    /**
     * Generate admission application PDF
     */
    public function generateAdmissionPdf(array $admission, array $documents = []): void
    {
        $personalInfo = is_string($admission['personal_info']) 
            ? json_decode($admission['personal_info'], true) 
            : $admission['personal_info'];
        $guardianInfo = is_string($admission['guardian_info']) 
            ? json_decode($admission['guardian_info'], true) 
            : $admission['guardian_info'];
        $academicInfo = is_string($admission['academic_info']) 
            ? json_decode($admission['academic_info'], true) 
            : $admission['academic_info'];
        
        // Get student photo
        $photoPath = null;
        foreach ($documents as $doc) {
            if ($doc['document_type'] === 'photo') {
                $photoPath = $doc['file_path'];
                break;
            }
        }
        
        $html = $this->getAdmissionPdfHtml($admission, $personalInfo, $guardianInfo, $academicInfo, $photoPath);
        
        $filename = 'Admission_Application_' . $admission['application_no'] . '.pdf';
        $this->generate($html, 'Admission Application', $filename);
    }
    
    /**
     * Generate fee voucher PDF
     */
    public function generateFeeVoucherPdf(array $voucher): void
    {
        $html = $this->getFeeVoucherPdfHtml($voucher);
        
        $filename = 'Fee_Voucher_' . $voucher['voucher_no'] . '.pdf';
        $this->generate($html, 'Fee Voucher', $filename);
    }
    
    /**
     * Get admission PDF HTML
     */
    private function getAdmissionPdfHtml(array $admission, array $personalInfo, array $guardianInfo, array $academicInfo, ?string $photoPath): string
    {
        $logoHtml = $this->getCampusLogoHtml($admission['campus_logo'] ?? null);
        
        $photoHtml = '';
        if ($photoPath) {
            $fullPath = defined('UPLOADS_PATH') ? UPLOADS_PATH . '/' . $photoPath : __DIR__ . '/../../storage/uploads/' . $photoPath;
            
            if (file_exists($fullPath)) {
                $imageData = base64_encode(file_get_contents($fullPath));
                $imageType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mimeType = 'image/jpeg';
                if ($imageType === 'png') $mimeType = 'image/png';
                elseif ($imageType === 'gif') $mimeType = 'image/gif';
                elseif ($imageType === 'webp') $mimeType = 'image/webp';
                
                $photoHtml = '<div style="position: absolute; top: 20px; right: 20px; width: 110px; text-align: center; z-index: 10;">
                    <div style="border: 3px solid #1e40af; padding: 3px; background: #fff; margin-bottom: 4px;">
                        <img src="data:' . $mimeType . ';base64,' . $imageData . '" alt="Student Photo" style="width: 100%; height: auto; max-height: 130px; min-height: 110px; object-fit: cover; display: block;" />
                    </div>
                    <p style="margin: 0; font-size: 8px; font-weight: bold; text-align: center; color: #374151;">PASSPORT SIZE<br/>PHOTO</p>
                </div>';
            }
        }
        
        $status = ucfirst(str_replace('_', ' ', $admission['status'] ?? 'pending'));
        $statusColors = [
            'pending' => ['#fef3c7', '#d97706', '#92400e'],
            'approved' => ['#d1fae5', '#059669', '#065f46'],
            'rejected' => ['#fee2e2', '#dc2626', '#991b1b'],
            'update_required' => ['#dbeafe', '#2563eb', '#1e40af']
        ];
        $statusConfig = $statusColors[strtolower($admission['status'] ?? 'pending')] ?? ['#f3f4f6', '#6b7280', '#374151'];
        
        return '
        <div style="position: relative; margin-bottom: 30px;">
            ' . $logoHtml . '
            ' . ($photoHtml ? $photoHtml : '') . '
            <div class="header" style="' . ($photoPath ? 'padding-right: 130px;' : '') . '">
                <h1>ADMISSION APPLICATION FORM</h1>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-top: 10px;">
                    <div>
                        <p style="margin: 4px 0; font-size: 11px;"><strong>Application No:</strong> <span style="color: #2563eb; font-weight: 600;">' . htmlspecialchars($admission['application_no'] ?? 'N/A') . '</span></p>
                        <p style="margin: 4px 0; font-size: 11px;"><strong>Date:</strong> ' . date('d M Y', strtotime($admission['created_at'])) . '</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 10px; color: #6b7280;">Status:</p>
                        <span class="status-badge" style="background: ' . $statusConfig[0] . '; color: ' . $statusConfig[2] . '; border: 1px solid ' . $statusConfig[1] . ';">
                            ' . htmlspecialchars($status) . '
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <h2>Personal Information</h2>
        <table>
            <tr><th width="28%">Full Name</th><td><strong>' . htmlspecialchars($personalInfo['full_name'] ?? '') . '</strong></td></tr>
            <tr><th>Father\'s Name</th><td>' . htmlspecialchars($personalInfo['father_name'] ?? '') . '</td></tr>
            <tr><th>Date of Birth</th><td>' . htmlspecialchars($personalInfo['date_of_birth'] ?? '') . '</td></tr>
            <tr><th>Gender</th><td>' . htmlspecialchars($personalInfo['gender'] ?? '') . '</td></tr>
            <tr><th>CNIC</th><td>' . htmlspecialchars($personalInfo['cnic'] ?? '') . '</td></tr>
            <tr><th>Phone</th><td>' . htmlspecialchars($personalInfo['phone'] ?? '') . '</td></tr>
            <tr><th>Email</th><td>' . htmlspecialchars($personalInfo['email'] ?? '') . '</td></tr>
            <tr><th>Address</th><td>' . htmlspecialchars($personalInfo['address'] ?? '') . '</td></tr>
            <tr><th>City</th><td>' . htmlspecialchars($personalInfo['city'] ?? '') . '</td></tr>
            <tr><th>Religion</th><td>' . htmlspecialchars($personalInfo['religion'] ?? '') . '</td></tr>
            <tr><th>Nationality</th><td>' . htmlspecialchars($personalInfo['nationality'] ?? 'Pakistani') . '</td></tr>
        </table>
        
        <h2>Guardian Information</h2>
        <table>
            <tr><th width="28%">Name</th><td>' . htmlspecialchars($guardianInfo['name'] ?? '') . '</td></tr>
            <tr><th>Relation</th><td>' . htmlspecialchars($guardianInfo['relation'] ?? '') . '</td></tr>
            <tr><th>CNIC</th><td>' . htmlspecialchars($guardianInfo['cnic'] ?? '') . '</td></tr>
            <tr><th>Phone</th><td>' . htmlspecialchars($guardianInfo['phone'] ?? '') . '</td></tr>
            <tr><th>Occupation</th><td>' . htmlspecialchars($guardianInfo['occupation'] ?? '') . '</td></tr>
            <tr><th>Address</th><td>' . htmlspecialchars($guardianInfo['address'] ?? '') . '</td></tr>
        </table>
        
        <h2>Academic Information</h2>
        <table>
            <tr><th width="28%">Last Qualification</th><td>' . htmlspecialchars($academicInfo['last_qualification'] ?? '') . '</td></tr>
            <tr><th>Institution</th><td>' . htmlspecialchars($academicInfo['institution'] ?? '') . '</td></tr>
            <tr><th>Board</th><td>' . htmlspecialchars($academicInfo['board'] ?? '') . '</td></tr>
            <tr><th>Passing Year</th><td>' . htmlspecialchars($academicInfo['passing_year'] ?? '') . '</td></tr>
            <tr><th>Marks Obtained</th><td>' . htmlspecialchars($academicInfo['marks_obtained'] ?? '') . '</td></tr>
            <tr><th>Total Marks</th><td>' . htmlspecialchars($academicInfo['total_marks'] ?? '') . '</td></tr>
            <tr><th>Grade</th><td>' . htmlspecialchars($academicInfo['grade'] ?? '') . '</td></tr>
        </table>
        
        <h2>Course Information</h2>
        <table>
            <tr><th width="28%">Course</th><td><strong>' . htmlspecialchars($admission['course_name'] ?? '') . '</strong> (' . htmlspecialchars($admission['course_code'] ?? '') . ')</td></tr>
            <tr><th>Campus</th><td>' . htmlspecialchars($admission['campus_name'] ?? '') . '</td></tr>
            <tr><th>Shift</th><td>' . htmlspecialchars(ucfirst($admission['shift'] ?? 'morning')) . '</td></tr>
            <tr><th>Application Status</th><td><span class="status-badge" style="background: ' . $statusConfig[0] . '; color: ' . $statusConfig[2] . '; border: 1px solid ' . $statusConfig[1] . ';">' . htmlspecialchars($status) . '</span></td></tr>
            ' . (!empty($admission['roll_number']) ? '<tr><th>Roll Number</th><td><strong style="font-size: 13px; color: #2563eb;">' . htmlspecialchars($admission['roll_number']) . '</strong></td></tr>' : '') . '
            ' . (!empty($admission['reviewed_at']) ? '<tr><th>Reviewed On</th><td>' . date('d M Y, h:i A', strtotime($admission['reviewed_at'])) . '</td></tr>' : '') . '
            ' . (!empty($admission['reviewer_name']) ? '<tr><th>Reviewed By</th><td>' . htmlspecialchars($admission['reviewer_name']) . '</td></tr>' : '') . '
            ' . (!empty($admission['admin_remarks']) ? '<tr><th>Remarks</th><td style="font-style: italic; color: #6b7280;">' . htmlspecialchars($admission['admin_remarks']) . '</td></tr>' : '') . '
        </table>
        
        <div class="footer">
            <p><strong>' . htmlspecialchars($admission['campus_name'] ?? '') . '</strong></p>
            <p>' . htmlspecialchars($admission['campus_address'] ?? '') . (!empty($admission['campus_city']) ? ', ' . htmlspecialchars($admission['campus_city']) : '') . '</p>
            ' . (!empty($admission['campus_phone']) ? '<p>Phone: ' . htmlspecialchars($admission['campus_phone']) . '</p>' : '') . '
            ' . (!empty($admission['campus_email']) ? '<p>Email: ' . htmlspecialchars($admission['campus_email']) . '</p>' : '') . '
            <p style="margin-top: 10px; color: #9ca3af;">Generated on ' . date('d M Y, h:i A') . ' | System Generated Document</p>
        </div>';
    }
    
    /**
     * Get fee voucher PDF HTML - Professional Challan Format
     */
    private function getFeeVoucherPdfHtml(array $voucher): string
    {
        $logoHtml = $this->getCampusLogoHtml($voucher['campus_logo'] ?? null);
        
        $statusColors = [
            'unpaid' => ['#fef3c7', '#d97706', '#92400e'],
            'pending_verification' => ['#dbeafe', '#2563eb', '#1e40af'],
            'paid' => ['#d1fae5', '#059669', '#065f46'],
            'overdue' => ['#fee2e2', '#dc2626', '#991b1b'],
            'cancelled' => ['#f3f4f6', '#6b7280', '#374151']
        ];
        
        $statusConfig = $statusColors[$voucher['status']] ?? ['#f3f4f6', '#6b7280', '#374151'];
        $statusText = ucfirst(str_replace('_', ' ', $voucher['status']));
        
        // Bank account information
        $bankInfoHtml = '';
        if (!empty($voucher['bank_account_number'])) {
            $bankInfoHtml = '<div class="bank-info-box">
                <h3>💰 Bank Account Details for Payment</h3>
                <table>
                    ' . (!empty($voucher['bank_account_name']) ? '<tr><th>Account Holder Name</th><td><strong>' . htmlspecialchars($voucher['bank_account_name']) . '</strong></td></tr>' : '') . '
                    ' . (!empty($voucher['bank_account_number']) ? '<tr><th>Account Number</th><td><strong style="font-size: 12px; letter-spacing: 1px;">' . htmlspecialchars($voucher['bank_account_number']) . '</strong></td></tr>' : '') . '
                    ' . (!empty($voucher['bank_name']) ? '<tr><th>Bank Name</th><td>' . htmlspecialchars($voucher['bank_name']) . '</td></tr>' : '') . '
                    ' . (!empty($voucher['bank_branch']) ? '<tr><th>Branch</th><td>' . htmlspecialchars($voucher['bank_branch']) . '</td></tr>' : '') . '
                    ' . (!empty($voucher['iban']) ? '<tr><th>IBAN</th><td><strong style="font-size: 11px; letter-spacing: 0.5px;">' . htmlspecialchars($voucher['iban']) . '</strong></td></tr>' : '') . '
                </table>
            </div>';
        }
        
        return '
        <div style="margin-bottom: 25px;">
            ' . $logoHtml . '
            <div class="header">
                <h1>FEE CHALLAN / VOUCHER</h1>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-top: 12px;">
                    <div>
                        <p style="margin: 5px 0; font-size: 11px;"><strong>Voucher No:</strong> <span style="color: #2563eb; font-weight: 600; font-size: 12px;">' . htmlspecialchars($voucher['voucher_no']) . '</span></p>
                        <p style="margin: 5px 0; font-size: 11px;"><strong>Issue Date:</strong> ' . date('d M Y', strtotime($voucher['created_at'])) . '</p>
                        <p style="margin: 5px 0; font-size: 11px;"><strong>Due Date:</strong> <span style="color: ' . $statusConfig[2] . '; font-weight: 600;">' . date('d M Y', strtotime($voucher['due_date'])) . '</span></p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 10px; color: #6b7280;">Payment Status:</p>
                        <span class="status-badge" style="background: ' . $statusConfig[0] . '; color: ' . $statusConfig[2] . '; border: 1px solid ' . $statusConfig[1] . '; margin-top: 5px;">
                            ' . htmlspecialchars($statusText) . '
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <h2>Student Information</h2>
        <table>
            <tr><th width="30%">Student Name</th><td><strong style="font-size: 12px;">' . htmlspecialchars($voucher['student_name'] ?? '') . '</strong></td></tr>
            ' . (!empty($voucher['application_no']) ? '<tr><th>Application No</th><td>' . htmlspecialchars($voucher['application_no']) . '</td></tr>' : '') . '
            ' . (!empty($voucher['roll_number']) ? '<tr><th>Roll Number</th><td><strong style="color: #2563eb; font-size: 12px;">' . htmlspecialchars($voucher['roll_number']) . '</strong></td></tr>' : '') . '
            <tr><th>Course</th><td><strong>' . htmlspecialchars($voucher['course_name'] ?? '') . '</strong> (' . htmlspecialchars($voucher['course_code'] ?? '') . ')</td></tr>
            <tr><th>Campus</th><td>' . htmlspecialchars($voucher['campus_name'] ?? '') . '</td></tr>
        </table>
        
        <h2>Fee Details</h2>
        <table>
            <tr><th width="30%">Fee Type</th><td style="text-transform: capitalize; font-weight: 500;">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $voucher['fee_type'] ?? 'admission'))) . '</td></tr>
            ' . (!empty($voucher['admission_shift']) ? '<tr><th>Shift</th><td><span style="padding: 4px 10px; background: ' . (($voucher['admission_shift'] === 'morning') ? '#fef3c7' : '#e0e7ff') . '; color: ' . (($voucher['admission_shift'] === 'morning') ? '#92400e' : '#3730a3') . '; border-radius: 4px; font-weight: 500; font-size: 11px;">' . ucfirst($voucher['admission_shift']) . '</span></td></tr>' : '') . '
        </table>
        
        ' . (!empty($voucher['fee_breakdown']) && is_array($voucher['fee_breakdown']) ? '
        <h2>Fee Breakdown</h2>
        <table>
            ' . ((float)($voucher['fee_breakdown']['admission_fee'] ?? 0) > 0 ? '<tr><th width="30%">Admission Fee</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['admission_fee'] ?? 0), 2) . '</td></tr>' : '') . '
            ' . ((float)($voucher['fee_breakdown']['tuition_fee'] ?? 0) > 0 ? '<tr><th>Tuition Fee</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['tuition_fee'] ?? 0), 2) . '</td></tr>' : '') . '
            ' . ((float)($voucher['fee_breakdown']['semester_fee'] ?? 0) > 0 ? '<tr><th>Semester Fee</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['semester_fee'] ?? 0), 2) . '</td></tr>' : '') . '
            ' . ((float)($voucher['fee_breakdown']['monthly_fee'] ?? 0) > 0 ? '<tr><th>Monthly Fee</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['monthly_fee'] ?? 0), 2) . '</td></tr>' : '') . '
            ' . ((float)($voucher['fee_breakdown']['exam_fee'] ?? 0) > 0 ? '<tr><th>Exam Fee</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['exam_fee'] ?? 0), 2) . '</td></tr>' : '') . '
            ' . ((float)($voucher['fee_breakdown']['other_charges'] ?? 0) > 0 ? '<tr><th>Other Charges</th><td style="text-align: right;">PKR ' . number_format((float)($voucher['fee_breakdown']['other_charges'] ?? 0), 2) . '</td></tr>' : '') . '
            <tr style="background: #fef2f2; border-top: 2px solid #dc2626;">
                <th style="background: #fee2e2; font-size: 12px; font-weight: 600;">Total Amount Payable</th>
                <td style="font-size: 20px; font-weight: bold; color: #dc2626; text-align: right; padding: 15px 10px;">
                    PKR ' . number_format((float)$voucher['amount'], 2) . '
                </td>
            </tr>
        </table>
        ' : '
        <h2>Fee Amount</h2>
        <table>
            <tr style="background: #fef2f2;">
                <th style="background: #fee2e2; font-size: 11px;">Amount Payable</th>
                <td style="font-size: 20px; font-weight: bold; color: #dc2626; text-align: right; padding: 15px 10px;">
                    PKR ' . number_format((float)$voucher['amount'], 2) . '
                </td>
            </tr>
        </table>
        ') . '
        
        <h2>Payment Information</h2>
        <table>
            <tr><th width="30%">Due Date</th><td style="color: ' . $statusConfig[2] . '; font-weight: 600;">' . date('d M Y', strtotime($voucher['due_date'])) . '</td></tr>
            <tr><th>Status</th><td><span class="status-badge" style="background: ' . $statusConfig[0] . '; color: ' . $statusConfig[2] . '; border: 1px solid ' . $statusConfig[1] . ';">' . htmlspecialchars($statusText) . '</span></td></tr>
        </table>
        
        ' . $bankInfoHtml . '
        
        <div style="margin-top: 25px; padding: 15px; background: #f9fafb; border-left: 4px solid #2563eb; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0; font-size: 12px; color: #1e40af; font-weight: 600;">📋 Payment Instructions:</h3>
            <ol style="margin: 0; padding-left: 20px; font-size: 10px; line-height: 1.8;">
                <li>Please pay the above amount before the due date to avoid late fees.</li>
                <li>Use the bank account details provided above for online transfer or deposit at bank.</li>
                <li>Keep this challan for your records and submit payment proof through student portal.</li>
                <li>For any queries, contact: ' . (!empty($voucher['contact_person_name']) ? htmlspecialchars($voucher['contact_person_name']) : 'Campus Administration') . 
                   (!empty($voucher['contact_person_phone']) ? ' (' . htmlspecialchars($voucher['contact_person_phone']) . ')' : '') . '</li>
            </ol>
        </div>
        
        <div class="footer">
            <p><strong>' . htmlspecialchars($voucher['campus_name'] ?? '') . '</strong></p>
            <p>' . htmlspecialchars($voucher['campus_address'] ?? '') . (!empty($voucher['campus_city']) ? ', ' . htmlspecialchars($voucher['campus_city']) : '') . '</p>
            ' . (!empty($voucher['campus_phone']) ? '<p>Phone: ' . htmlspecialchars($voucher['campus_phone']) . '</p>' : '') . '
            ' . (!empty($voucher['campus_email']) ? '<p>Email: ' . htmlspecialchars($voucher['campus_email']) . '</p>' : '') . '
            <p style="margin-top: 10px; color: #9ca3af;">Generated on ' . date('d M Y, h:i A') . ' | System Generated Document</p>
        </div>';
    }
}
