<?php
/**
 * Student Controller
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Campus;
use App\Models\Course;
use App\Models\Admission;
use App\Models\AdmissionDocument;
use App\Models\FeeStructure;
use App\Models\FeeVoucher;
use App\Models\FeePayment;
use App\Models\Notification;
use App\Models\Certificate;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\FileUploadHelper;
use App\Services\PdfService;

class StudentController extends Controller
{
    private User $userModel;
    private Campus $campusModel;
    private Course $courseModel;
    private Admission $admissionModel;
    private AdmissionDocument $documentModel;
    private FeeStructure $feeStructureModel;
    private FeeVoucher $feeVoucherModel;
    private FeePayment $feePaymentModel;
    private Notification $notificationModel;
    private Certificate $certificateModel;
    private ValidationHelper $validator;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->campusModel = new Campus();
        $this->courseModel = new Course();
        $this->admissionModel = new Admission();
        $this->documentModel = new AdmissionDocument();
        $this->feeStructureModel = new FeeStructure();
        $this->feeVoucherModel = new FeeVoucher();
        $this->feePaymentModel = new FeePayment();
        $this->notificationModel = new Notification();
        $this->certificateModel = new Certificate();
        $this->validator = new ValidationHelper();
    }
    
    /**
     * Student Dashboard
     */
    public function dashboard(): void
    {
        $user = $this->user();
        $userId = $user['id'];
        
        // Get applications
        $applications = $this->admissionModel->getByUser($userId);
        
        // Get fee vouchers
        $vouchers = $this->feeVoucherModel->getByUser($userId);
        
        // Get unread notifications
        $notifications = $this->notificationModel->getByUser($userId, 5);
        $unreadCount = $this->notificationModel->countUnread($userId);
        
        // Get certificates
        $certificates = $this->certificateModel->getByUser($userId);
        
        $this->render('student.dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'applications' => $applications,
            'vouchers' => $vouchers,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'certificates' => $certificates
        ], 'layouts.student');
    }
    
    // ===================== ADMISSION =====================
    
    /**
     * Show admission form
     */
    public function newAdmission(): void
    {
        $campuses = $this->campusModel->getActive();
        
        $this->render('student.admission.new', [
            'title' => 'New Admission Application',
            'user' => $this->user(),
            'campuses' => $campuses,
            'documentTypes' => DOCUMENT_TYPES
        ], 'layouts.student');
    }
    
    /**
     * Get courses for campus (AJAX)
     */
    public function getCourses(string $campusId = null): void
    {
        // Clear any previous output
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        try {
            // Handle case where parameter might not be passed correctly by Router
            if ($campusId === null || $campusId === '') {
                // Try to extract from URL
                $uri = $_SERVER['REQUEST_URI'] ?? '';
                if (preg_match('/\/campuses\/(\d+)\//', $uri, $matches)) {
                    $campusId = $matches[1];
                } elseif (isset($_GET['id'])) {
                    $campusId = $_GET['id'];
                } else {
                    $this->json(['error' => 'Campus ID is required'], 400);
                    return;
                }
            }
            
            $campusIdInt = (int)$campusId;
            
            if ($campusIdInt <= 0) {
                $this->json(['error' => 'Invalid campus ID: ' . $campusId], 400);
                return;
            }
            
            // Get courses
            $courses = $this->courseModel->getByCampus($campusIdInt);
            
            // If no courses assigned to campus, return all active courses
            if (empty($courses)) {
                $courses = $this->courseModel->getActive();
            }
            
            // Ensure we return a proper array format
            $result = [];
            foreach ($courses as $course) {
                if (!isset($course['id']) || !isset($course['name'])) {
                    continue; // Skip invalid entries
                }
                
                $result[] = [
                    'id' => (int)$course['id'],
                    'name' => $course['name'] ?? '',
                    'code' => $course['code'] ?? '',
                    'duration_months' => isset($course['duration_months']) ? (int)$course['duration_months'] : 0,
                    'available_seats' => isset($course['available_seats']) ? (int)$course['available_seats'] : null
                ];
            }
            
            $this->json($result);
        } catch (\PDOException $e) {
            $this->json([
                'error' => 'Database error',
                'message' => APP_ENV === 'development' ? $e->getMessage() : 'Failed to load courses'
            ], 500);
        } catch (\Throwable $e) {
            $this->json([
                'error' => 'Failed to load courses',
                'message' => APP_ENV === 'development' ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Please try again later'
            ], 500);
        }
    }
    
    /**
     * Get fee structure (AJAX)
     */
    public function getFeeStructure(): void
    {
        $courseId = $this->query('course_id');
        $campusId = $this->query('campus_id');
        
        if (!$courseId || !$campusId) {
            $this->json(['error' => 'Missing parameters'], 400);
        }
        
        $feeStructure = $this->feeStructureModel->getFeeBreakdown((int)$courseId, (int)$campusId);
        $this->json($feeStructure);
    }
    
    /**
     * Submit admission application
     */
    public function submitAdmission(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('student/admission/new');
        }
        
        $user = $this->user();
        
        // Check if already applied for this course
        $courseId = $this->input('course_id');
        if ($this->admissionModel->hasPendingForCourse($user['id'], (int)$courseId)) {
            $this->flash('error', 'You already have a pending or approved application for this course.');
            $this->redirect('student/admission/new');
        }
        
        // Validate basic fields
        $rules = [
            'campus_id' => 'required|exists:campuses,id',
            'course_id' => 'required|exists:courses,id',
            'full_name' => 'required|min:3',
            'father_name' => 'required|min:3',
            'cnic' => 'required',
            'phone' => 'required|phone',
            'guardian_name' => 'required',
            'guardian_phone' => 'required|phone'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            SessionHelper::set('old_input', $_POST);
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('student/admission/new');
        }
        
        // Prepare data
        $personalInfo = [
            'full_name' => $this->input('full_name'),
            'father_name' => $this->input('father_name'),
            'date_of_birth' => $this->input('date_of_birth'),
            'gender' => $this->input('gender'),
            'cnic' => $this->input('cnic'),
            'phone' => $this->input('phone'),
            'email' => $this->input('email', $user['email']),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'religion' => $this->input('religion'),
            'nationality' => $this->input('nationality', 'Pakistani')
        ];
        
        $guardianInfo = [
            'name' => $this->input('guardian_name'),
            'relation' => $this->input('guardian_relation'),
            'cnic' => $this->input('guardian_cnic'),
            'phone' => $this->input('guardian_phone'),
            'occupation' => $this->input('guardian_occupation'),
            'address' => $this->input('guardian_address')
        ];
        
        $academicInfo = [
            'last_qualification' => $this->input('last_qualification'),
            'institution' => $this->input('institution'),
            'board' => $this->input('board'),
            'passing_year' => $this->input('passing_year'),
            'marks_obtained' => $this->input('marks_obtained'),
            'total_marks' => $this->input('total_marks'),
            'grade' => $this->input('grade')
        ];
        
        // Create admission
        $admissionId = $this->admissionModel->createAdmission([
            'user_id' => $user['id'],
            'course_id' => (int)$courseId,
            'campus_id' => (int)$this->input('campus_id'),
            'shift' => $this->input('shift', 'morning'),
            'personal_info' => $personalInfo,
            'guardian_info' => $guardianInfo,
            'academic_info' => $academicInfo
        ]);
        
        // Upload documents
        $uploader = new FileUploadHelper();
        $documentTypes = ['photo', 'cnic_front', 'cnic_back', 'matric_certificate', 'inter_certificate'];
        
        foreach ($documentTypes as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
                $filePath = $uploader->upload($_FILES[$type], 'documents/' . $admissionId, [
                    'prefix' => $type . '_'
                ]);
                
                if ($filePath) {
                    $this->documentModel->uploadDocument(
                        $admissionId,
                        $type,
                        $filePath,
                        $_FILES[$type]['name']
                    );
                }
            }
        }
        
        // Send notification
        $admission = $this->admissionModel->find($admissionId);
        $this->notificationModel->notifyAdmissionStatus(
            $user['id'],
            $admissionId,
            'pending',
            $admission['application_no']
        );
        
        $this->flash('success', 'Your admission application has been submitted successfully. Application No: ' . $admission['application_no']);
        $this->redirect('student/dashboard');
    }
    
    /**
     * View application details
     */
    public function viewApplication(string $id): void
    {
        $user = $this->user();
        $admission = $this->admissionModel->getWithDetails((int)$id);
        
        if (!$admission || $admission['user_id'] != $user['id']) {
            $this->flash('error', 'Application not found.');
            $this->redirect('student/dashboard');
        }
        
        $this->render('student.admission.view', [
            'title' => 'Application - ' . $admission['application_no'],
            'user' => $user,
            'admission' => $admission
        ], 'layouts.student');
    }
    
    /**
     * Edit application (if update required)
     */
    public function editApplication(string $id): void
    {
        $user = $this->user();
        $admission = $this->admissionModel->getWithDetails((int)$id);
        
        if (!$admission || $admission['user_id'] != $user['id']) {
            $this->flash('error', 'Application not found.');
            $this->redirect('student/dashboard');
        }
        
        if ($admission['status'] !== 'update_required') {
            $this->flash('error', 'This application cannot be edited.');
            $this->redirect('student/applications/' . $id);
        }
        
        $campuses = $this->campusModel->getActive();
        $courses = $this->courseModel->getByCampus($admission['campus_id']);
        
        $this->render('student.admission.edit', [
            'title' => 'Edit Application - ' . $admission['application_no'],
            'user' => $user,
            'admission' => $admission,
            'campuses' => $campuses,
            'courses' => $courses,
            'documentTypes' => DOCUMENT_TYPES
        ], 'layouts.student');
    }
    
    /**
     * Update application
     */
    public function updateApplication(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->back();
        }
        
        $user = $this->user();
        $admission = $this->admissionModel->find((int)$id);
        
        if (!$admission || $admission['user_id'] != $user['id'] || $admission['status'] !== 'update_required') {
            $this->flash('error', 'Cannot update this application.');
            $this->redirect('student/dashboard');
        }
        
        $personalInfo = [
            'full_name' => $this->input('full_name'),
            'father_name' => $this->input('father_name'),
            'date_of_birth' => $this->input('date_of_birth'),
            'gender' => $this->input('gender'),
            'cnic' => $this->input('cnic'),
            'phone' => $this->input('phone'),
            'email' => $this->input('email'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'religion' => $this->input('religion'),
            'nationality' => $this->input('nationality', 'Pakistani')
        ];
        
        $guardianInfo = [
            'name' => $this->input('guardian_name'),
            'relation' => $this->input('guardian_relation'),
            'cnic' => $this->input('guardian_cnic'),
            'phone' => $this->input('guardian_phone'),
            'occupation' => $this->input('guardian_occupation'),
            'address' => $this->input('guardian_address')
        ];
        
        $academicInfo = [
            'last_qualification' => $this->input('last_qualification'),
            'institution' => $this->input('institution'),
            'board' => $this->input('board'),
            'passing_year' => $this->input('passing_year'),
            'marks_obtained' => $this->input('marks_obtained'),
            'total_marks' => $this->input('total_marks'),
            'grade' => $this->input('grade')
        ];
        
        $this->admissionModel->update((int)$id, [
            'personal_info' => json_encode($personalInfo),
            'guardian_info' => json_encode($guardianInfo),
            'academic_info' => json_encode($academicInfo),
            'status' => 'pending'
        ]);
        
        // Upload new documents
        $uploader = new FileUploadHelper();
        $documentTypes = ['photo', 'cnic_front', 'cnic_back', 'matric_certificate', 'inter_certificate'];
        
        foreach ($documentTypes as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
                $filePath = $uploader->upload($_FILES[$type], 'documents/' . $id, [
                    'prefix' => $type . '_'
                ]);
                
                if ($filePath) {
                    $this->documentModel->uploadDocument(
                        (int)$id,
                        $type,
                        $filePath,
                        $_FILES[$type]['name']
                    );
                }
            }
        }
        
        $this->flash('success', 'Application updated successfully and resubmitted for review.');
        $this->redirect('student/applications/' . $id);
    }
    
    // ===================== FEE MANAGEMENT =====================
    
    /**
     * View fee vouchers
     */
    public function feeVouchers(): void
    {
        $user = $this->user();
        $vouchers = $this->feeVoucherModel->getByUser($user['id']);
        
        $this->render('student.fees.index', [
            'title' => 'Fee Vouchers',
            'user' => $user,
            'vouchers' => $vouchers
        ], 'layouts.student');
    }
    
    /**
     * View voucher details
     */
    public function viewVoucher(string $id): void
    {
        $user = $this->user();
        $voucher = $this->feeVoucherModel->getWithDetails((int)$id);
        
        if (!$voucher || $voucher['user_id'] != $user['id']) {
            $this->flash('error', 'Voucher not found.');
            $this->redirect('student/fees');
        }
        
        $this->render('student.fees.view', [
            'title' => 'Fee Voucher - ' . $voucher['voucher_no'],
            'user' => $user,
            'voucher' => $voucher
        ], 'layouts.student');
    }
    
    /**
     * Download admission application PDF
     */
    public function downloadAdmissionPdf(string $id): void
    {
        $user = $this->user();
        $admission = $this->admissionModel->getWithDetails((int)$id);
        
        if (!$admission || $admission['user_id'] != $user['id']) {
            $this->flash('error', 'Application not found.');
            $this->redirect('student/dashboard');
        }
        
        $documents = $this->documentModel->getByAdmission((int)$id);
        
        $pdfService = new PdfService();
        $pdfService->generateAdmissionPdf($admission, $documents);
    }
    
    /**
     * Download fee voucher PDF
     */
    public function downloadFeeVoucherPdf(string $id): void
    {
        $user = $this->user();
        $voucher = $this->feeVoucherModel->getWithDetails((int)$id);
        
        if (!$voucher || $voucher['user_id'] != $user['id']) {
            $this->flash('error', 'Voucher not found.');
            $this->redirect('student/fees');
        }
        
        $pdfService = new PdfService();
        $pdfService->generateFeeVoucherPdf($voucher);
    }
    
    /**
     * Submit payment proof
     */
    public function submitPayment(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $user = $this->user();
        $voucher = $this->feeVoucherModel->find((int)$id);
        
        if (!$voucher || $voucher['user_id'] != $user['id']) {
            $this->json(['error' => 'Voucher not found'], 404);
        }
        
        if ($voucher['status'] === 'paid') {
            $this->json(['error' => 'This voucher has already been paid'], 400);
        }
        
        $transactionId = $this->input('transaction_id');
        if (empty($transactionId)) {
            $this->json(['error' => 'Transaction ID is required'], 400);
        }
        
        // Upload proof
        if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Payment proof file is required'], 400);
        }
        
        $uploader = new FileUploadHelper();
        $proofPath = $uploader->upload($_FILES['proof'], 'payments/' . $voucher['voucher_no'], [
            'prefix' => 'proof_'
        ]);
        
        if (!$proofPath) {
            $this->json(['error' => $uploader->error() ?? 'File upload failed'], 400);
        }
        
        $this->feePaymentModel->submitPayment(
            (int)$id,
            $voucher['amount'],
            $transactionId,
            $proofPath
        );
        
        $this->json(['success' => true, 'message' => 'Payment proof submitted successfully. Awaiting verification.']);
    }
    
    // ===================== NOTIFICATIONS =====================
    
    /**
     * View notifications
     */
    public function notifications(): void
    {
        $user = $this->user();
        $notifications = $this->notificationModel->getByUser($user['id'], 50);
        
        $this->render('student.notifications', [
            'title' => 'Notifications',
            'user' => $user,
            'notifications' => $notifications
        ], 'layouts.student');
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead(string $id): void
    {
        $this->notificationModel->markAsRead((int)$id);
        $this->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(): void
    {
        $user = $this->user();
        $this->notificationModel->markAllAsRead($user['id']);
        $this->json(['success' => true]);
    }
    
    // ===================== CERTIFICATES =====================
    
    /**
     * View certificates
     */
    public function certificates(): void
    {
        $user = $this->user();
        $certificates = $this->certificateModel->getByUser($user['id']);
        
        $this->render('student.certificates', [
            'title' => 'My Certificates',
            'user' => $user,
            'certificates' => $certificates
        ], 'layouts.student');
    }
    
    // ===================== PROFILE =====================
    
    /**
     * View profile
     */
    public function profile(): void
    {
        $user = $this->user();
        
        $this->render('student.profile', [
            'title' => 'My Profile',
            'user' => $user
        ], 'layouts.student');
    }
    
    /**
     * Update profile
     */
    public function updateProfile(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('student/profile');
        }
        
        $user = $this->user();
        
        $rules = [
            'name' => 'required|min:3|max:150',
            'phone' => 'phone'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('student/profile');
        }
        
        $updateData = [
            'name' => $this->input('name'),
            'phone' => $this->input('phone')
        ];
        
        // Handle profile image
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploadHelper();
            $imagePath = $uploader->upload($_FILES['profile_image'], 'profiles', [
                'prefix' => 'user_' . $user['id'] . '_',
                'allowed_types' => ALLOWED_IMAGE_TYPES
            ]);
            
            if ($imagePath) {
                $updateData['profile_image'] = $imagePath;
            }
        }
        
        $this->userModel->update($user['id'], $updateData);
        SessionHelper::set('user_name', $updateData['name']);
        
        $this->flash('success', 'Profile updated successfully.');
        $this->redirect('student/profile');
    }
    
    /**
     * Change password
     */
    public function changePassword(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $user = $this->user();
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        
        // Verify current password
        $fullUser = $this->userModel->find($user['id']);
        if (!$this->userModel->verifyPassword($currentPassword, $fullUser['password'])) {
            $this->json(['error' => 'Current password is incorrect'], 400);
        }
        
        // Validate new password
        $rules = ['new_password' => 'required|min:8|confirmed'];
        if (!$this->validator->validate($_POST, $rules)) {
            $this->json(['error' => $this->validator->allErrors()[0]], 400);
        }
        
        $this->userModel->updatePassword($user['id'], $newPassword);
        
        $this->json(['success' => true, 'message' => 'Password changed successfully']);
    }
}

