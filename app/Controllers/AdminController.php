<?php
/**
 * Admin Controller
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
use App\Models\ExamTerm;
use App\Models\Exam;
use App\Models\ExamRegistration;
use App\Models\ExamMark;
use App\Models\CourseResult;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Setting;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\FileUploadHelper;
use App\Services\EmailService;
use App\Services\PdfService;

class AdminController extends Controller
{
    private User $userModel;
    private Campus $campusModel;
    private Course $courseModel;
    private Admission $admissionModel;
    private FeeStructure $feeStructureModel;
    private FeeVoucher $feeVoucherModel;
    private FeePayment $feePaymentModel;
    private Notification $notificationModel;
    private ExamTerm $examTermModel;
    private Exam $examModel;
    private ExamRegistration $examRegistrationModel;
    private ExamMark $examMarkModel;
    private CourseResult $courseResultModel;
    private AttendanceSession $attendanceSessionModel;
    private AttendanceRecord $attendanceRecordModel;
    private Setting $settingModel;
    private ValidationHelper $validator;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->campusModel = new Campus();
        $this->courseModel = new Course();
        $this->admissionModel = new Admission();
        $this->feeStructureModel = new FeeStructure();
        $this->feeVoucherModel = new FeeVoucher();
        $this->feePaymentModel = new FeePayment();
        $this->notificationModel = new Notification();
        $this->examTermModel = new ExamTerm();
        $this->examModel = new Exam();
        $this->examRegistrationModel = new ExamRegistration();
        $this->examMarkModel = new ExamMark();
        $this->courseResultModel = new CourseResult();
        $this->attendanceSessionModel = new AttendanceSession();
        $this->attendanceRecordModel = new AttendanceRecord();
        $this->settingModel = new Setting();
        $this->validator = new ValidationHelper();
    }
    
    /**
     * Dashboard
     */
    public function dashboard(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        // Get stats
        $stats = [
            'total_students' => $this->userModel->countStudents($campusId),
            'admissions' => $this->admissionModel->countByStatus($campusId),
            'fee_stats' => $this->feeVoucherModel->countByStatus($campusId),
            'pending_payments' => $this->feePaymentModel->countPending($campusId)
        ];
        
        // Get financial statistics
        $financialStats = $this->feePaymentModel->getFinancialStats($campusId);
        $financialStats['total_pending'] = $this->feeVoucherModel->getTotalPendingFees($campusId);
        
        // Get recent admissions
        $recentAdmissions = $campusId 
            ? $this->admissionModel->getByCampus($campusId, ['status' => 'pending'])
            : $this->admissionModel->getAllAdmissions(['status' => 'pending']);
        $recentAdmissions = array_slice($recentAdmissions, 0, 10);
        
        // Get fee defaulters
        $defaulters = $this->feeVoucherModel->getDefaulters($campusId);
        $defaulters = array_slice($defaulters, 0, 5);
        
        // Get campuses for dropdown
        $campuses = $this->campusModel->getForDropdown();
        
        $this->render('admin.dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats,
            'financialStats' => $financialStats,
            'recentAdmissions' => $recentAdmissions,
            'defaulters' => $defaulters,
            'campuses' => $campuses
        ], 'layouts.admin');
    }
    
    // ===================== CAMPUS MANAGEMENT =====================
    
    /**
     * List campuses
     */
    public function campuses(): void
    {
        $campuses = $this->campusModel->getAllWithStats();
        
        $this->render('admin.campuses.index', [
            'title' => 'Campus Management',
            'user' => $this->user(),
            'campuses' => $campuses
        ], 'layouts.admin');
    }

    /**
     * Delete campus (hard delete if no admissions or vouchers exist)
     */
    public function deleteCampus(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $campusId = (int) $id;
        $campus = $this->campusModel->find($campusId);
        if (!$campus) {
            $this->json(['error' => 'Campus not found'], 404);
        }

        // Prevent deleting main campus if desired business rule (optional)
        if (($campus['type'] ?? '') === 'main') {
            $this->json(['error' => 'Main campus cannot be deleted. You may inactivate it instead.'], 400);
        }

        // Check dependencies that use RESTRICT
        $hasAdmissions = $this->admissionModel->count('campus_id = ?', [$campusId]) > 0;
        $hasVouchers = $this->feeVoucherModel->count('campus_id = ?', [$campusId]) > 0;

        if ($hasAdmissions || $hasVouchers) {
            $this->json(['error' => 'Cannot delete campus while admissions or vouchers exist.'], 400);
        }

        $deleted = $this->campusModel->delete($campusId);
        if (!$deleted) {
            $this->json(['error' => 'Failed to delete campus'], 500);
        }

        $this->json(['success' => true, 'message' => 'Campus deleted successfully']);
    }
    
    /**
     * Show create campus form
     */
    public function createCampus(): void
    {
        $this->render('admin.campuses.create', [
            'title' => 'Add Campus',
            'user' => $this->user()
        ], 'layouts.admin');
    }
    
    /**
     * Store campus
     */
    public function storeCampus(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/campuses');
        }
        
        $rules = [
            'name' => 'required|min:3|max:150',
            'code' => 'required|max:20|unique:campuses,code',
            'type' => 'required|in:main,sub',
            'email' => 'email'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            SessionHelper::set('old_input', $_POST);
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('admin/campuses/create');
        }
        
        $this->campusModel->create([
            'name' => $this->input('name'),
            'code' => strtoupper($this->input('code')),
            'type' => $this->input('type'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'phone' => $this->input('phone'),
            'email' => $this->input('email'),
            'focal_person' => $this->input('focal_person'),
            'is_active' => 1
        ]);
        
        $this->flash('success', 'Campus created successfully.');
        $this->redirect('admin/campuses');
    }
    
    /**
     * Edit campus
     */
    public function editCampus(string $id): void
    {
        $campus = $this->campusModel->find((int)$id);
        if (!$campus) {
            $this->flash('error', 'Campus not found.');
            $this->redirect('admin/campuses');
        }
        
        $this->render('admin.campuses.edit', [
            'title' => 'Edit Campus',
            'user' => $this->user(),
            'campus' => $campus
        ], 'layouts.admin');
    }
    
    /**
     * Update campus
     */
    public function updateCampus(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/campuses');
        }
        
        $campus = $this->campusModel->find((int)$id);
        if (!$campus) {
            $this->flash('error', 'Campus not found.');
            $this->redirect('admin/campuses');
        }
        
        $rules = [
            'name' => 'required|min:3|max:150',
            'type' => 'required|in:main,sub',
            'email' => 'email'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('admin/campuses/' . $id . '/edit');
        }
        
        $updateData = [
            'name' => $this->input('name'),
            'type' => $this->input('type'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'phone' => $this->input('phone'),
            'email' => $this->input('email'),
            'focal_person' => $this->input('focal_person'),
            'is_active' => $this->normalizeBoolean($this->input('is_active', 0)),
            'bank_account_name' => $this->input('bank_account_name'),
            'bank_account_number' => $this->input('bank_account_number'),
            'bank_name' => $this->input('bank_name'),
            'bank_branch' => $this->input('bank_branch'),
            'iban' => $this->input('iban'),
            'contact_person_name' => $this->input('contact_person_name'),
            'contact_person_phone' => $this->input('contact_person_phone'),
            'contact_person_email' => $this->input('contact_person_email')
        ];
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploader = new FileUploadHelper();
            $logoPath = $uploader->upload($_FILES['logo'], 'campuses/logos', [
                'max_size' => 2 * 1024 * 1024, // 2MB
                'allowed_types' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
            ]);
            
            if ($logoPath) {
                // Delete old logo if exists
                if (!empty($campus['logo'])) {
                    $oldLogoPath = defined('UPLOADS_PATH') ? UPLOADS_PATH . '/' . $campus['logo'] : __DIR__ . '/../../storage/uploads/' . $campus['logo'];
                    if (file_exists($oldLogoPath)) {
                        @unlink($oldLogoPath);
                    }
                }
                $updateData['logo'] = $logoPath;
            }
        }
        
        $this->campusModel->update((int)$id, $updateData);
        
        $this->flash('success', 'Campus updated successfully.');
        $this->redirect('admin/campuses');
    }
    
    // ===================== COURSE MANAGEMENT =====================
    
    /**
     * List courses
     */
    public function courses(): void
    {
        $courses = $this->courseModel->all();
        $campuses = $this->campusModel->getForDropdown();
        
        $this->render('admin.courses.index', [
            'title' => 'Course Management',
            'user' => $this->user(),
            'courses' => $courses,
            'campuses' => $campuses
        ], 'layouts.admin');
    }
    
    /**
     * Store course
     */
    public function storeCourse(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $rules = [
            'name' => 'required|min:3|max:150',
            'code' => 'required|max:30|unique:courses,code',
            'duration_months' => 'required|integer'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->json(['error' => $this->validator->allErrors()[0]], 400);
        }
        
        $courseId = $this->courseModel->create([
            'name' => $this->input('name'),
            'code' => strtoupper($this->input('code')),
            'description' => $this->input('description'),
            'duration_months' => $this->input('duration_months'),
            'total_seats' => $this->input('total_seats', 50),
            'is_active' => 1
        ]);
        
        $this->json(['success' => true, 'id' => $courseId, 'message' => 'Course created successfully']);
    }
    
    /**
     * Update course
     */
    public function updateCourse(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $course = $this->courseModel->find((int)$id);
        if (!$course) {
            $this->json(['error' => 'Course not found'], 404);
        }
        
        $this->courseModel->update((int)$id, [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'duration_months' => $this->input('duration_months'),
            'total_seats' => $this->input('total_seats'),
            'is_active' => $this->normalizeBoolean($this->input('is_active', 0))
        ]);
        
        $this->json(['success' => true, 'message' => 'Course updated successfully']);
    }

    /**
     * Delete course (hard delete if no admissions exist)
     */
    public function deleteCourse(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $courseId = (int) $id;
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            $this->json(['error' => 'Course not found'], 404);
        }

        // Check for dependent admissions
        $hasAdmissions = $this->admissionModel->count('course_id = ?', [$courseId]) > 0;
        if ($hasAdmissions) {
            $this->json(['error' => 'Cannot delete course while admissions exist. Please archive it instead.'], 400);
        }

        // Safe to delete; related campus_courses and fee_structures will cascade
        $deleted = $this->courseModel->delete($courseId);
        if (!$deleted) {
            $this->json(['error' => 'Failed to delete course'], 500);
        }

        $this->json(['success' => true, 'message' => 'Course deleted successfully']);
    }
    
    // ===================== FEE STRUCTURE =====================
    
    /**
     * List fee structures
     */
    public function feeStructures(): void
    {
        // Always load campuses and courses for dropdowns
        $courses = $this->courseModel->getForDropdown();
        $campuses = $this->campusModel->getForDropdown();

        // Fee structures list may fail on older DB schema – guard it
        try {
            $feeStructures = $this->feeStructureModel->getAllWithDetails();
        } catch (\Throwable $e) {
            // If the underlying tables/columns are missing (older DB schema),
            // fail gracefully and show an empty state instead of a 500 error.
            $feeStructures = [];
        }
        
        $this->render('admin.fees.structures', [
            'title' => 'Fee Structures',
            'user' => $this->user(),
            'feeStructures' => $feeStructures,
            'courses' => $courses,
            'campuses' => $campuses
        ], 'layouts.admin');
    }

    /**
     * Delete fee structure (hard delete)
     */
    public function deleteFeeStructure(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $fsId = (int) $id;
        $existing = $this->feeStructureModel->find($fsId);
        if (!$existing) {
            $this->json(['error' => 'Fee structure not found'], 404);
        }

        $deleted = $this->feeStructureModel->delete($fsId);
        if (!$deleted) {
            $this->json(['error' => 'Failed to delete fee structure'], 500);
        }

        $this->json(['success' => true, 'message' => 'Fee structure deleted successfully']);
    }
    
    /**
     * Store/Update fee structure
     */
    public function storeFeeStructure(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $rules = [
            'course_id' => 'required|exists:courses,id',
            'campus_id' => 'required|exists:campuses,id',
            'shift' => 'required|in:morning,evening',
            'admission_fee' => 'required|numeric',
            'tuition_fee' => 'numeric',
            'semester_fee' => 'numeric',
            'monthly_fee' => 'numeric'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->json(['error' => $this->validator->allErrors()[0]], 400);
        }
        
        $data = [
            'course_id' => $this->input('course_id'),
            'campus_id' => $this->input('campus_id'),
            'shift' => $this->input('shift', 'morning'),
            'admission_fee' => $this->input('admission_fee', 0),
            'tuition_fee' => $this->input('tuition_fee', 0),
            'semester_fee' => $this->input('semester_fee', 0),
            'monthly_fee' => $this->input('monthly_fee', 0),
            'exam_fee' => $this->input('exam_fee', 0),
            'other_charges' => $this->input('other_charges', 0),
            'is_active' => 1
        ];
        
        // If ID is provided, try to update existing structure; otherwise create/update by unique key
        if ($this->input('id')) {
            $existing = $this->feeStructureModel->find((int)$this->input('id'));
            if ($existing) {
                $this->feeStructureModel->update((int)$this->input('id'), $data);
                $id = (int)$this->input('id');
            } else {
                // If the referenced record no longer exists (e.g. different DB),
                // fall back to createOrUpdate based on course/campus/shift.
                $id = $this->feeStructureModel->createOrUpdate($data);
            }
        } else {
            $id = $this->feeStructureModel->createOrUpdate($data);
        }
        
        $this->json(['success' => true, 'id' => $id, 'message' => 'Fee structure saved successfully']);
    }
    
    // ===================== ADMISSION MANAGEMENT =====================
    
    /**
     * List admissions
     */
    public function admissions(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        $filters = [
            'status' => $this->query('status'),
            'campus_id' => $this->query('campus_id') ?? $campusId,
            'course_id' => $this->query('course_id'),
            'search' => $this->query('search')
        ];
        
        $admissions = $campusId 
            ? $this->admissionModel->getByCampus($campusId, $filters)
            : $this->admissionModel->getAllAdmissions($filters);
        
        $campuses = $this->campusModel->getForDropdown();
        $courses = $this->courseModel->getForDropdown();
        
        $this->render('admin.admissions.index', [
            'title' => 'Admission Management',
            'user' => $user,
            'admissions' => $admissions,
            'campuses' => $campuses,
            'courses' => $courses,
            'filters' => $filters
        ], 'layouts.admin');
    }

    /**
     * Soft delete (trash) an admission
     */
    public function trashAdmission(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $admissionId = (int) $id;
        $admission = $this->admissionModel->find($admissionId);
        if (!$admission) {
            $this->json(['error' => 'Admission not found'], 404);
        }

        // Business rule: only allow trashing pending/rejected/update_required
        if (!in_array($admission['status'], ['pending', 'rejected', 'update_required'], true)) {
            $this->json(['error' => 'Only pending or rejected applications can be trashed.'], 400);
        }

        $updated = $this->admissionModel->update($admissionId, ['is_trashed' => 1]);
        if (!$updated) {
            $this->json(['error' => 'Failed to trash admission'], 500);
        }

        $this->json(['success' => true, 'message' => 'Admission moved to trash']);
    }
    
    /**
     * View admission details
     */
    public function viewAdmission(string $id): void
    {
        $admission = $this->admissionModel->getWithDetails((int)$id);
        
        if (!$admission) {
            $this->flash('error', 'Admission not found.');
            $this->redirect('admin/admissions');
        }
        
        // Check campus access for sub campus admin
        $user = $this->user();
        if ($user['role_slug'] === 'sub_campus_admin' && $admission['campus_id'] != $user['campus_id']) {
            $this->flash('error', 'Access denied.');
            $this->redirect('admin/admissions');
        }
        
        $documentModel = new AdmissionDocument();
        $documents = $documentModel->getByAdmission((int)$id);
        
        // Get fee voucher if exists
        $feeVoucher = $this->db->fetch(
            "SELECT v.*, 
                    (SELECT fp.status FROM fee_payments fp WHERE fp.voucher_id = v.id ORDER BY fp.created_at DESC LIMIT 1) as payment_status,
                    (SELECT fp.id FROM fee_payments fp WHERE fp.voucher_id = v.id ORDER BY fp.created_at DESC LIMIT 1) as payment_id
             FROM fee_vouchers v
             WHERE v.admission_id = ? AND v.fee_type = 'admission'
             ORDER BY v.created_at DESC LIMIT 1",
            [(int)$id]
        );
        
        // Get payment details if exists
        $payment = null;
        if ($feeVoucher && !empty($feeVoucher['payment_id'])) {
            $payment = $this->feePaymentModel->getWithDetails($feeVoucher['payment_id']);
        }
        
        $this->render('admin.admissions.view', [
            'title' => 'Admission Details - ' . $admission['application_no'],
            'user' => $user,
            'admission' => $admission,
            'documents' => $documents,
            'feeVoucher' => $feeVoucher,
            'payment' => $payment
        ], 'layouts.admin');
    }
    
    /**
     * Download admission application PDF
     */
    public function downloadAdmissionPdf(string $id): void
    {
        $admission = $this->admissionModel->getWithDetails((int)$id);
        
        if (!$admission) {
            $this->flash('error', 'Admission not found.');
            $this->redirect('admin/admissions');
        }
        
        // Check campus access
        $user = $this->user();
        if ($user['role_slug'] === 'sub_campus_admin' && $admission['campus_id'] != $user['campus_id']) {
            $this->flash('error', 'Access denied.');
            $this->redirect('admin/admissions');
        }
        
        $documentModel = new AdmissionDocument();
        $documents = $documentModel->getByAdmission((int)$id);
        
        $pdfService = new PdfService();
        $pdfService->generateAdmissionPdf($admission, $documents);
    }
    
    /**
     * Download fee voucher PDF
     */
    public function downloadFeeVoucherPdf(string $id): void
    {
        $voucher = $this->feeVoucherModel->getWithDetails((int)$id);
        
        if (!$voucher) {
            $this->flash('error', 'Fee voucher not found.');
            $this->redirect('admin/fee-vouchers');
        }
        
        // Check campus access
        $user = $this->user();
        if ($user['role_slug'] === 'sub_campus_admin' && $voucher['campus_id'] != $user['campus_id']) {
            $this->flash('error', 'Access denied.');
            $this->redirect('admin/fee-vouchers');
        }
        
        $pdfService = new PdfService();
        $pdfService->generateFeeVoucherPdf($voucher);
    }
    
    /**
     * Generate fee challan for admission
     */
    public function generateFeeChallan(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $admission = $this->admissionModel->find((int)$id);
        if (!$admission) {
            $this->json(['error' => 'Admission not found'], 404);
            return;
        }
        
        $user = $this->user();
        
        // Check campus access
        if ($user['role_slug'] === 'sub_campus_admin' && $admission['campus_id'] != $user['campus_id']) {
            $this->json(['error' => 'Access denied'], 403);
            return;
        }
        
        // Check if voucher already exists
        $existingVoucher = $this->db->fetch(
            "SELECT id FROM fee_vouchers WHERE admission_id = ? AND fee_type = 'admission' LIMIT 1",
            [(int)$id]
        );
        
        if ($existingVoucher) {
            $this->json(['error' => 'Fee challan already generated for this admission'], 400);
            return;
        }
        
        // Get fee structure with shift
        $shift = $admission['shift'] ?? 'morning';
        $feeStructure = $this->feeStructureModel->getForCourseAndCampus(
            $admission['course_id'],
            $admission['campus_id'],
            $shift
        );
        
        if (!$feeStructure) {
            $this->json(['error' => 'Fee structure not found for this course, campus, and shift'], 404);
            return;
        }
        
        // Get complete fee breakdown
        $feeBreakdown = $this->feeStructureModel->getFeeBreakdown(
            $admission['course_id'],
            $admission['campus_id'],
            $shift
        );
        
        // Calculate total admission fee (admission + tuition for admission challan)
        $totalFee = (float)$feeStructure['admission_fee'] + (float)$feeStructure['tuition_fee'];
        $dueDays = (int)$this->settingModel->get('admission_fee_due_days', 14);
        $dueDate = date('Y-m-d', strtotime("+{$dueDays} days"));
        
        // Generate voucher with fee breakdown stored
        $voucherId = $this->feeVoucherModel->createForAdmission((int)$id, $totalFee, $dueDate, $feeBreakdown);
        
        // Get voucher details
        $voucher = $this->feeVoucherModel->find($voucherId);
        
        // Send notification
        $this->notificationModel->notifyFeeVoucher(
            $admission['user_id'],
            $voucherId,
            $voucher['voucher_no'],
            $totalFee,
            $dueDate
        );
        
        $this->json([
            'success' => true,
            'message' => 'Fee challan generated successfully',
            'voucher' => [
                'id' => $voucherId,
                'voucher_no' => $voucher['voucher_no'],
                'amount' => $totalFee,
                'due_date' => $dueDate
            ]
        ]);
    }
    
    /**
     * Update admission status
     */
    public function updateAdmissionStatus(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $admission = $this->admissionModel->find((int)$id);
        if (!$admission) {
            $this->json(['error' => 'Admission not found'], 404);
        }
        
        $user = $this->user();
        $status = $this->input('status');
        $remarks = $this->input('remarks');
        
        // Check campus access
        if ($user['role_slug'] === 'sub_campus_admin' && $admission['campus_id'] != $user['campus_id']) {
            $this->json(['error' => 'Access denied'], 403);
        }
        
        // Check if trying to approve - require fee verification first
        if ($status === 'approved') {
            // Check if admission has a fee voucher
            $existingVoucher = $this->db->fetch(
                "SELECT v.id, v.status, 
                        (SELECT fp.status FROM fee_payments fp WHERE fp.voucher_id = v.id ORDER BY fp.created_at DESC LIMIT 1) as payment_status
                 FROM fee_vouchers v
                 WHERE v.admission_id = ? AND v.fee_type = 'admission'
                 ORDER BY v.created_at DESC LIMIT 1",
                [(int)$id]
            );
            
            if ($existingVoucher) {
                // Check if payment is verified
                if ($existingVoucher['payment_status'] !== 'verified') {
                    $this->json([
                        'error' => 'Cannot approve admission. Fee payment must be verified first.',
                        'message' => 'Please verify the fee payment before approving the admission.'
                    ], 400);
                    return;
                }
            } else {
                // No voucher generated yet
                $this->json([
                    'error' => 'Cannot approve admission. Fee challan must be generated first.',
                    'message' => 'Please generate the fee challan before approving the admission.'
                ], 400);
                return;
            }
        }
        
        $this->admissionModel->updateStatus((int)$id, $status, $user['id'], $remarks);
        
        // Send notification
        $this->notificationModel->notifyAdmissionStatus(
            $admission['user_id'],
            (int)$id,
            $status,
            $admission['application_no']
        );
        
        $this->json(['success' => true, 'message' => 'Admission status updated successfully']);
    }
    
    /**
     * Assign roll number
     */
    public function assignRollNumber(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $admission = $this->admissionModel->find((int)$id);
        if (!$admission || $admission['status'] !== 'approved') {
            $this->json(['error' => 'Invalid admission'], 400);
        }
        
        $rollNumber = $this->input('roll_number') ?: $this->admissionModel->generateRollNumber((int)$id);
        $batch = $this->input('batch', date('Y'));
        
        $this->admissionModel->update((int)$id, [
            'roll_number' => $rollNumber,
            'batch' => $batch
        ]);
        
        // Update user campus
        $this->userModel->update($admission['user_id'], [
            'campus_id' => $admission['campus_id']
        ]);
        
        $this->json(['success' => true, 'roll_number' => $rollNumber, 'message' => 'Roll number assigned successfully']);
    }
    
    // ===================== FEE MANAGEMENT =====================
    
    /**
     * List fee vouchers
     */
    public function feeVouchers(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        $filters = [
            'status' => $this->query('status'),
            'campus_id' => $this->query('campus_id') ?? $campusId
        ];
        
        $vouchers = $campusId
            ? $this->feeVoucherModel->getByCampus($campusId, $filters)
            : $this->feeVoucherModel->getAllVouchers($filters);
        
        $campuses = $this->campusModel->getForDropdown();
        
        $this->render('admin.fees.vouchers', [
            'title' => 'Fee Vouchers',
            'user' => $user,
            'vouchers' => $vouchers,
            'campuses' => $campuses,
            'filters' => $filters
        ], 'layouts.admin');
    }

    /**
     * Cancel (void) a fee voucher
     */
    public function cancelFeeVoucher(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $voucherId = (int) $id;
        $voucher = $this->feeVoucherModel->find($voucherId);
        if (!$voucher) {
            $this->json(['error' => 'Fee voucher not found'], 404);
        }

        // Do not cancel already paid vouchers
        if ($voucher['status'] === 'paid') {
            $this->json(['error' => 'Paid vouchers cannot be cancelled'], 400);
        }

        $updated = $this->feeVoucherModel->update($voucherId, ['status' => 'cancelled']);
        if (!$updated) {
            $this->json(['error' => 'Failed to cancel voucher'], 500);
        }

        $this->json(['success' => true, 'message' => 'Voucher cancelled successfully']);
    }
    
    /**
     * Pending payments verification
     * Shows both: unpaid vouchers (awaiting payment) and submitted payments (awaiting verification)
     */
    public function pendingPayments(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        // Get submitted payments awaiting verification
        $submittedPayments = $this->feePaymentModel->getPending($campusId);
        
        // Get unpaid vouchers (awaiting payment submission)
        if ($campusId) {
            $unpaidVouchers = $this->feeVoucherModel->getByCampus($campusId, ['status' => 'unpaid']);
        } else {
            $unpaidVouchers = $this->feeVoucherModel->getAllVouchers(['status' => 'unpaid']);
        }
        
        // Filter out vouchers that already have pending payments
        $voucherIdsWithPayments = array_column($submittedPayments, 'voucher_id');
        $unpaidVouchers = array_filter($unpaidVouchers, function($v) use ($voucherIdsWithPayments) {
            return !in_array($v['id'], $voucherIdsWithPayments);
        });
        
        $this->render('admin.fees.pending-payments', [
            'title' => 'Pending Payments',
            'user' => $user,
            'submittedPayments' => $submittedPayments,
            'unpaidVouchers' => array_values($unpaidVouchers)
        ], 'layouts.admin');
    }
    
    /**
     * Verify payment
     */
    public function verifyPayment(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $payment = $this->feePaymentModel->getWithDetails((int)$id);
        if (!$payment) {
            $this->json(['error' => 'Payment not found'], 404);
        }
        
        $user = $this->user();
        $status = $this->input('status');
        $remarks = $this->input('remarks');
        
        $this->feePaymentModel->verify((int)$id, $user['id'], $status, $remarks);
        
        // Get voucher for notification
        $voucher = $this->feeVoucherModel->find($payment['voucher_id']);
        
        // If verified and admission exists
        if ($status === 'verified' && $voucher['admission_id']) {
            $admission = $this->admissionModel->find($voucher['admission_id']);
            
            if ($admission) {
                // Generate roll number if not exists
                if (!$admission['roll_number']) {
                    $rollNumber = $this->admissionModel->generateRollNumber($voucher['admission_id']);
                    $this->admissionModel->assignRollNumber($voucher['admission_id'], $rollNumber);
                }
                
                // Auto-approve admission if fee is verified (optional - can be disabled)
                $autoApproveOnFeeVerification = $this->settingModel->get('auto_approve_on_fee_verification', '0');
                if ($autoApproveOnFeeVerification === '1' && $admission['status'] !== 'approved') {
                    $this->admissionModel->updateStatus(
                        $voucher['admission_id'],
                        'approved',
                        $user['id'],
                        'Auto-approved after fee verification'
                    );
                }
            }
        }
        
        // Send notification
        $this->notificationModel->notifyPaymentStatus(
            $voucher['user_id'],
            $payment['voucher_id'],
            $voucher['voucher_no'],
            $status
        );
        
        $this->json(['success' => true, 'message' => 'Payment ' . $status . ' successfully']);
    }
    
    /**
     * Send fee reminder
     */
    public function sendFeeReminder(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $user = $this->user();
        $campusId = $this->input('campus_id');
        
        // Sub campus admin can only send to their campus
        if ($user['role_slug'] === 'sub_campus_admin') {
            $campusId = $user['campus_id'];
        }
        
        $overdue = $this->feeVoucherModel->getOverdue($campusId);
        
        $count = 0;
        foreach ($overdue as $voucher) {
            $this->notificationModel->sendFeeReminder(
                $voucher['user_id'],
                $voucher['voucher_no'],
                $voucher['amount'],
                $voucher['due_date']
            );
            $count++;
        }
        
        $this->json(['success' => true, 'message' => "Fee reminders sent to {$count} students"]);
    }
    
    // ===================== USER MANAGEMENT =====================
    
    /**
     * List users
     */
    public function users(): void
    {
        $filters = [
            'role_id' => $this->query('role_id'),
            'campus_id' => $this->query('campus_id'),
            'search' => $this->query('search')
        ];
        
        $users = $this->userModel->getAllWithDetails($filters);
        $campuses = $this->campusModel->getForDropdown();
        
        $roles = $this->db->fetchAll("SELECT * FROM roles");
        
        $this->render('admin.users.index', [
            'title' => 'User Management',
            'user' => $this->user(),
            'users' => $users,
            'campuses' => $campuses,
            'roles' => $roles,
            'filters' => $filters
        ], 'layouts.admin');
    }
    
    /**
     * Create user
     */
    public function storeUser(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->json(['error' => $this->validator->allErrors()[0]], 400);
        }
        
        $campusId = $this->input('campus_id');
        $campusId = in_array($campusId, ['', null, 'null', 'undefined'], true) ? null : (int)$campusId;
        
        try {
            $userId = $this->userModel->createUser([
                'name' => $this->input('name'),
                'email' => $this->input('email'),
                'password' => $this->input('password'),
                'phone' => $this->input('phone'),
                'role_id' => $this->input('role_id'),
                'campus_id' => $campusId,
                'is_active' => 1
            ]);
            
            $this->json(['success' => true, 'id' => $userId, 'message' => 'User created successfully']);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Failed to create user', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Update user
     */
    public function updateUser(string $id): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $campusId = $this->input('campus_id');
        $campusId = in_array($campusId, ['', null, 'null', 'undefined'], true) ? null : (int)$campusId;
        
        $isActiveRaw = $this->input('is_active', 1);
        $isActive = in_array($isActiveRaw, [1, '1', true, 'true', 'on', 'yes'], true) ? 1 : 0;
        
        // Basic validation
        $rules = [
            'name' => 'required|min:3|max:150',
            'role_id' => 'required|numeric'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->json(['error' => $this->validator->allErrors()[0]], 400);
        }
        
        $userData = [
            'name' => $this->input('name'),
            'phone' => $this->input('phone'),
            'role_id' => $this->input('role_id'),
            'campus_id' => $campusId,
            'is_active' => $isActive
        ];
        
        // Update password if provided
        if ($this->input('password')) {
            $this->userModel->updatePassword((int)$id, $this->input('password'));
        }
        
        try {
            $this->userModel->update((int)$id, $userData);
            $this->json(['success' => true, 'message' => 'User updated successfully']);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
        }
    }
    
    // ===================== SETTINGS =====================
    
    /**
     * Settings page
     */
    public function settings(): void
    {
        $settings = $this->settingModel->getAllSettings();
        
        $this->render('admin.settings', [
            'title' => 'Settings',
            'user' => $this->user(),
            'settings' => $settings
        ], 'layouts.admin');
    }
    
    /**
     * Update settings
     */
    public function updateSettings(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/settings');
        }
        
        // General settings
        $settings = [
            'institute_name', 'institute_short_name', 'institute_email',
            'institute_phone', 'institute_address', 'fee_due_reminder_days',
            'admission_fee_due_days'
        ];
        
        foreach ($settings as $key) {
            if ($this->input($key) !== null) {
                $this->settingModel->set($key, $this->input($key));
            }
        }
        
        // SMTP Internal settings
        $smtpInternalSettings = [
            'smtp_internal_host', 'smtp_internal_port', 'smtp_internal_username',
            'smtp_internal_password', 'smtp_internal_from_email', 'smtp_internal_from_name',
            'smtp_internal_encryption'
        ];
        
        foreach ($smtpInternalSettings as $key) {
            if ($this->input($key) !== null) {
                $this->settingModel->set($key, $this->input($key), 'string', 'smtp');
            }
        }
        
        // SMTP External settings
        $this->settingModel->set('smtp_external_same_as_internal', $this->input('smtp_external_same_as_internal', '0'), 'string', 'smtp');
        
        $smtpExternalSettings = [
            'smtp_external_host', 'smtp_external_port', 'smtp_external_username',
            'smtp_external_password', 'smtp_external_from_email', 'smtp_external_from_name',
            'smtp_external_encryption'
        ];
        
        foreach ($smtpExternalSettings as $key) {
            if ($this->input($key) !== null) {
                $this->settingModel->set($key, $this->input($key), 'string', 'smtp');
            }
        }
        
        $this->flash('success', 'Settings updated successfully.');
        $this->redirect('admin/settings');
    }
    
    /**
     * Test SMTP connection
     */
    public function testSmtp(): void
    {
        $type = $this->input('type', 'internal');
        $prefix = 'smtp_' . $type . '_';
        
        $host = $this->settingModel->get($prefix . 'host');
        $port = $this->settingModel->get($prefix . 'port', 587);
        $username = $this->settingModel->get($prefix . 'username');
        $password = $this->settingModel->get($prefix . 'password');
        $encryption = $this->settingModel->get($prefix . 'encryption', 'tls');
        $fromEmail = $this->settingModel->get($prefix . 'from_email');
        
        if (empty($host) || empty($username)) {
            $this->json(['success' => false, 'error' => 'SMTP settings not configured']);
            return;
        }
        
        try {
            // Simple connection test
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            
            $conn = @stream_socket_client(
                ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port,
                $errno, $errstr, 10,
                STREAM_CLIENT_CONNECT,
                $context
            );
            
            if ($conn) {
                fclose($conn);
                $this->json(['success' => true, 'message' => 'Connection successful']);
            } else {
                $this->json(['success' => false, 'error' => "Connection failed: $errstr"]);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    // ===================== ADMIN ADMISSION SUBMISSION =====================
    
    /**
     * Show admin admission form
     */
    public function newAdmission(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        $campuses = $campusId 
            ? [$this->campusModel->find($campusId)]
            : $this->campusModel->getActive();
        
        $this->render('admin.admissions.new', [
            'title' => 'Submit New Admission',
            'user' => $user,
            'campuses' => $campuses,
            'documentTypes' => DOCUMENT_TYPES
        ], 'layouts.admin');
    }
    
    /**
     * Get courses for campus (AJAX) - Admin
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
     * Submit admin admission
     */
    public function submitAdmission(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/admissions/new');
        }
        
        $adminUser = $this->user();
        
        // First, create or find the student user
        $studentEmail = $this->input('email');
        $existingUser = $this->userModel->findByEmail($studentEmail);
        
        if ($existingUser) {
            $studentUserId = $existingUser['id'];
        } else {
            // Create new student user with temporary password
            // Password will be set by student via email link
            $tempPassword = bin2hex(random_bytes(16)); // Generate secure temp password
            $studentUserId = $this->userModel->createUser([
                'name' => $this->input('full_name'),
                'email' => $studentEmail,
                'password' => $tempPassword,
                'phone' => $this->input('phone'),
                'role_id' => 4, // Student role
                'is_active' => 1,
                'password_needs_reset' => 1 // Flag that password needs to be set
            ]);
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
            $this->redirect('admin/admissions/new');
        }
        
        // Prepare data
        $personalInfo = [
            'full_name' => $this->input('full_name'),
            'father_name' => $this->input('father_name'),
            'date_of_birth' => $this->input('date_of_birth'),
            'gender' => $this->input('gender'),
            'cnic' => $this->input('cnic'),
            'phone' => $this->input('phone'),
            'email' => $studentEmail,
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
            'user_id' => $studentUserId,
            'course_id' => (int)$this->input('course_id'),
            'campus_id' => (int)$this->input('campus_id'),
            'shift' => $this->input('shift', 'morning'),
            'personal_info' => $personalInfo,
            'guardian_info' => $guardianInfo,
            'academic_info' => $academicInfo
        ]);
        
        // Upload documents
        $documentModel = new AdmissionDocument();
        $uploader = new FileUploadHelper();
        $documentTypes = ['photo', 'cnic_front', 'cnic_back', 'matric_certificate', 'inter_certificate'];
        
        foreach ($documentTypes as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
                $filePath = $uploader->upload($_FILES[$type], 'documents/' . $admissionId, [
                    'prefix' => $type . '_'
                ]);
                
                if ($filePath) {
                    $documentModel->uploadDocument(
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
            $studentUserId,
            $admissionId,
            'pending',
            $admission['application_no']
        );
        
        // Send welcome email with password reset link if new user was created
        if (!$existingUser) {
            try {
                $emailService = new EmailService('external');
                $resetToken = $this->userModel->createResetToken($studentUserId);
                $resetLink = BASE_URL . '/reset-password/' . $resetToken;
                
                $emailSent = $emailService->sendWelcomeEmail(
                    $studentEmail,
                    $this->input('full_name'),
                    $resetLink,
                    $admission['application_no']
                );
                
                if ($emailSent) {
                    $this->flash('success', 'Admission application submitted successfully. Application No: ' . $admission['application_no'] . '. Welcome email sent to student.');
                } else {
                    $this->flash('success', 'Admission application submitted successfully. Application No: ' . $admission['application_no'] . '. Warning: Could not send welcome email. Please inform the student manually.');
                }
            } catch (\Exception $e) {
                error_log("Error sending welcome email: " . $e->getMessage());
                $this->flash('success', 'Admission application submitted successfully. Application No: ' . $admission['application_no'] . '. Warning: Could not send welcome email.');
            }
        } else {
            $this->flash('success', 'Admission application submitted successfully. Application No: ' . $admission['application_no']);
        }
        
        $this->redirect('admin/admissions');
    }
    
    // ===================== CERTIFICATES =====================
    
    /**
     * List certificates
     */
    public function certificates(): void
    {
        $user = $this->user();
        $campusId = $user['role_slug'] === 'sub_campus_admin' ? $user['campus_id'] : null;
        
        $certificateModel = new Certificate();
        $certificates = $campusId 
            ? $certificateModel->getByCampus($campusId)
            : $certificateModel->all();
        
        // Get enrolled students for uploading certificates
        $enrolledStudents = $this->db->fetchAll(
            "SELECT a.id as admission_id, a.roll_number, a.user_id,
                    u.name as student_name, c.name as course_name, c.id as course_id
             FROM admissions a
             INNER JOIN users u ON a.user_id = u.id
             INNER JOIN courses c ON a.course_id = c.id
             WHERE a.status = 'approved' AND a.roll_number IS NOT NULL" .
            ($campusId ? " AND a.campus_id = ?" : ""),
            $campusId ? [$campusId] : []
        );
        
        $this->render('admin.certificates.index', [
            'title' => 'Certificate Management',
            'user' => $user,
            'certificates' => $certificates,
            'enrolledStudents' => $enrolledStudents
        ], 'layouts.admin');
    }
    
    /**
     * Upload certificate
     */
    public function uploadCertificate(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $admissionId = $this->input('admission_id');
        $admission = $this->admissionModel->find((int)$admissionId);
        
        if (!$admission || $admission['status'] !== 'approved') {
            $this->json(['error' => 'Invalid admission'], 400);
        }
        
        // Upload file
        $uploader = new FileUploadHelper();
        $filePath = $uploader->upload($_FILES['certificate'], 'certificates', [
            'allowed_types' => ['application/pdf', 'image/jpeg', 'image/png']
        ]);
        
        if (!$filePath) {
            $this->json(['error' => $uploader->error() ?? 'Upload failed'], 400);
        }
        
        $certificateModel = new Certificate();
        $certId = $certificateModel->uploadCertificate(
            $admission['user_id'],
            (int)$admissionId,
            $admission['course_id'],
            $filePath,
            $this->user()['id']
        );
        
        // Get course name for notification
        $course = $this->courseModel->find($admission['course_id']);
        
        // Send notification
        $this->notificationModel->notifyCertificate(
            $admission['user_id'],
            $certId,
            $course['name']
        );
        
        $this->json(['success' => true, 'message' => 'Certificate uploaded successfully']);
    }

    // ===================== EXAMS & ATTENDANCE =====================
    
    /**
     * Exams dashboard
     */
    public function exams(): void
    {
        $user = $this->user();
        
        try {
            $stats = [
                'terms' => $this->examTermModel->count(),
                'scheduled' => $this->examModel->count("status = 'scheduled'"),
                'completed' => $this->examModel->count("status = 'completed'"),
                'registrations' => $this->examRegistrationModel->count()
            ];
            
            $recentExams = array_slice($this->examModel->all(), 0, 10);
            $terms = $this->examTermModel->all();
            $courses = $this->courseModel->getActive();
        } catch (\Throwable $e) {
            // Handle missing exams/terms tables on older databases
            $stats = [
                'terms' => 0,
                'scheduled' => 0,
                'completed' => 0,
                'registrations' => 0
            ];
            $recentExams = [];
            $terms = [];
            $courses = [];
        }
        
        $this->render('admin.exams.index', [
            'title' => 'Exams Management',
            'user' => $user,
            'stats' => $stats,
            'terms' => $terms,
            'courses' => $courses,
            'recentExams' => $recentExams
        ], 'layouts.admin');
    }
    
    /**
     * Store exam term
     */
    public function storeExamTerm(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/exams');
        }
        
        $rules = [
            'name' => 'required|max:150',
            'code' => 'required|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'in:draft,active,closed'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('admin/exams');
        }
        
        $this->examTermModel->create([
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'start_date' => $this->input('start_date'),
            'end_date' => $this->input('end_date'),
            'status' => $this->input('status', 'draft')
        ]);
        
        $this->flash('success', 'Exam term created.');
        $this->redirect('admin/exams');
    }
    
    /**
     * Store exam
     */
    public function storeExam(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/exams');
        }
        
        $rules = [
            'exam_term_id' => 'required|numeric',
            'course_id' => 'required|numeric',
            'title' => 'required|max:200',
            'exam_type' => 'required|in:midterm,final,quiz,assignment,practical,other',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_marks' => 'numeric',
            'weightage' => 'numeric'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('admin/exams');
        }
        
        $this->examModel->create([
            'exam_term_id' => (int)$this->input('exam_term_id'),
            'course_id' => (int)$this->input('course_id'),
            'title' => $this->input('title'),
            'exam_type' => $this->input('exam_type'),
            'exam_date' => $this->input('exam_date'),
            'start_time' => $this->input('start_time'),
            'end_time' => $this->input('end_time'),
            'venue' => $this->input('venue'),
            'total_marks' => $this->input('total_marks', 100),
            'weightage' => $this->input('weightage', 0),
            'status' => 'scheduled'
        ]);
        
        $this->flash('success', 'Exam scheduled.');
        $this->redirect('admin/exams');
    }
    
    /**
     * Results dashboard
     */
    public function results(): void
    {
        $user = $this->user();
        
        try {
            $stats = [
                'total_results' => $this->courseResultModel->count(),
                'published' => $this->courseResultModel->count("status IN ('passed','failed')"),
                'in_progress' => $this->courseResultModel->count("status = 'in_progress'"),
                'failed' => $this->courseResultModel->count("status = 'failed'")
            ];
            
            $recentResults = array_slice($this->courseResultModel->all(), 0, 10);
        } catch (\Throwable $e) {
            // Handle missing course_results table on older databases
            $stats = [
                'total_results' => 0,
                'published' => 0,
                'in_progress' => 0,
                'failed' => 0
            ];
            $recentResults = [];
        }
        
        $this->render('admin.results.index', [
            'title' => 'Results Management',
            'user' => $user,
            'stats' => $stats,
            'recentResults' => $recentResults
        ], 'layouts.admin');
    }
    
    /**
     * Attendance dashboard
     */
    public function attendance(): void
    {
        $user = $this->user();
        
        try {
            $stats = [
                'sessions' => $this->attendanceSessionModel->count(),
                'completed' => $this->attendanceSessionModel->count("status = 'completed'"),
                'scheduled' => $this->attendanceSessionModel->count("status = 'scheduled'"),
                'records' => $this->attendanceRecordModel->count()
            ];
            
            $recentSessions = array_slice($this->attendanceSessionModel->all(), 0, 10);
            $courses = $this->courseModel->getActive();
        } catch (\Throwable $e) {
            // Handle missing attendance tables on older databases
            $stats = [
                'sessions' => 0,
                'completed' => 0,
                'scheduled' => 0,
                'records' => 0
            ];
            $recentSessions = [];
            $courses = [];
        }
        
        $this->render('admin.attendance.index', [
            'title' => 'Class Attendance',
            'user' => $user,
            'stats' => $stats,
            'recentSessions' => $recentSessions,
            'courses' => $courses
        ], 'layouts.admin');
    }
    
    /**
     * Store attendance session
     */
    public function storeAttendanceSession(): void
    {
        if (!$this->validateCsrfToken()) {
            $this->flash('error', 'Invalid request.');
            $this->redirect('admin/attendance');
        }
        
        $rules = [
            'course_id' => 'required|numeric',
            'session_date' => 'required|date',
            'session_type' => 'required|in:lecture,lab,tutorial,exam',
            'start_time' => 'max:20',
            'end_time' => 'max:20'
        ];
        
        if (!$this->validator->validate($_POST, $rules)) {
            $this->flash('error', implode('<br>', $this->validator->allErrors()));
            $this->redirect('admin/attendance');
        }
        
        $this->attendanceSessionModel->create([
            'course_id' => (int)$this->input('course_id'),
            'instructor_id' => $this->input('instructor_id') ? (int)$this->input('instructor_id') : null,
            'session_date' => $this->input('session_date'),
            'start_time' => $this->input('start_time'),
            'end_time' => $this->input('end_time'),
            'session_type' => $this->input('session_type', 'lecture'),
            'topic' => $this->input('topic'),
            'status' => 'scheduled'
        ]);
        
        $this->flash('success', 'Attendance session created.');
        $this->redirect('admin/attendance');
    }
}

