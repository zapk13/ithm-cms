<?php
/**
 * Fee Voucher & Payment Tests
 */

namespace Tests;

use App\Models\FeeVoucher;
use App\Models\FeePayment;
use App\Models\Admission;
use App\Models\User;
use App\Models\Course;
use App\Models\Campus;

class FeeVoucherTest
{
    private FeeVoucher $voucherModel;
    private FeePayment $paymentModel;
    private Admission $admissionModel;
    private User $userModel;
    private Course $courseModel;
    private Campus $campusModel;
    
    private ?int $testUserId = null;
    private ?int $testCourseId = null;
    private ?int $testAdmissionId = null;
    private ?int $testVoucherId = null;
    
    public function setUp(): void
    {
        $this->voucherModel = new FeeVoucher();
        $this->paymentModel = new FeePayment();
        $this->admissionModel = new Admission();
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->campusModel = new Campus();
        
        // Setup test data
        $this->testUserId = $this->userModel->createUser([
            'name' => 'Voucher Test Student',
            'email' => 'voucher_' . time() . '@test.com',
            'password' => 'test123',
            'role_id' => 4
        ]);
        
        $this->testCourseId = $this->courseModel->create([
            'name' => 'Voucher Test Course',
            'code' => 'VTC' . time(),
            'duration_months' => 12,
            'is_active' => 1
        ]);
        
        $campus = $this->campusModel->getMainCampus();
        $this->testAdmissionId = $this->admissionModel->createAdmission([
            'user_id' => $this->testUserId,
            'course_id' => $this->testCourseId,
            'campus_id' => $campus['id'],
            'shift' => 'morning',
            'personal_info' => ['full_name' => 'Test'],
            'guardian_info' => ['name' => 'Guardian'],
            'academic_info' => []
        ]);
    }
    
    public function tearDown(): void
    {
        // Delete in correct order to respect foreign keys
        try {
            if ($this->testVoucherId) {
                // Delete payments first
                $payments = $this->paymentModel->getByVoucher($this->testVoucherId);
                foreach ($payments as $p) {
                    $this->paymentModel->delete($p['id']);
                }
                $this->voucherModel->delete($this->testVoucherId);
                $this->testVoucherId = null;
            }
        } catch (\Exception $e) {}
        
        try {
            if ($this->testAdmissionId) {
                // Delete vouchers for this admission
                $vouchers = $this->voucherModel->where('admission_id', $this->testAdmissionId);
                foreach ($vouchers as $v) {
                    $this->voucherModel->delete($v['id']);
                }
                $this->admissionModel->delete($this->testAdmissionId);
                $this->testAdmissionId = null;
            }
        } catch (\Exception $e) {}
        
        try {
            if ($this->testCourseId) {
                $this->courseModel->delete($this->testCourseId);
                $this->testCourseId = null;
            }
        } catch (\Exception $e) {}
        
        try {
            if ($this->testUserId) {
                $this->userModel->delete($this->testUserId);
                $this->testUserId = null;
            }
        } catch (\Exception $e) {}
    }
    
    public function testVoucherNumberGeneration(): bool
    {
        $voucherNo = $this->voucherModel->generateVoucherNo();
        $hasFormat = strpos($voucherNo, 'V-') === 0;
        return TestRunner::assertTrue($hasFormat, 'Voucher number should start with V-');
    }
    
    public function testCanCreateVoucherForAdmission(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        return TestRunner::assertGreaterThan(0, $this->testVoucherId, 'Should create voucher');
    }
    
    public function testVoucherDefaultStatusIsUnpaid(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $voucher = $this->voucherModel->find($this->testVoucherId);
        
        return TestRunner::assertEquals('unpaid', $voucher['status'], 'Default status should be unpaid');
    }
    
    public function testGetVoucherWithDetails(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $details = $this->voucherModel->getWithDetails($this->testVoucherId);
        
        $hasDetails = isset($details['student_name']) && isset($details['campus_name']);
        
        return TestRunner::assertTrue($hasDetails, 'Should have student and campus details');
    }
    
    public function testGetVouchersByUser(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $vouchers = $this->voucherModel->getByUser($this->testUserId);
        
        return TestRunner::assertGreaterThan(0, count($vouchers), 'Should find user vouchers');
    }
    
    public function testVoucherStatusUpdate(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $this->voucherModel->updateStatus($this->testVoucherId, 'pending_verification');
        
        $voucher = $this->voucherModel->find($this->testVoucherId);
        
        return TestRunner::assertEquals('pending_verification', $voucher['status'], 'Status should be updated');
    }
    
    public function testCountVouchersByStatus(): bool
    {
        $counts = $this->voucherModel->countByStatus();
        
        $hasStatuses = isset($counts['unpaid']) && 
                       isset($counts['paid']) &&
                       isset($counts['pending_verification']);
        
        return TestRunner::assertTrue($hasStatuses, 'Should count all statuses');
    }
    
    public function testPaymentSubmission(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $paymentId = $this->paymentModel->submitPayment(
            $this->testVoucherId,
            25000,
            'TXN123456',
            'test/proof.jpg'
        );
        
        $success = $paymentId > 0;
        
        // Clean up
        if ($paymentId) {
            $this->paymentModel->delete($paymentId);
        }
        
        return TestRunner::assertTrue($success, 'Should submit payment');
    }
    
    public function testPaymentVerification(): bool
    {
        $dueDate = date('Y-m-d', strtotime('+14 days'));
        
        $this->testVoucherId = $this->voucherModel->createForAdmission(
            $this->testAdmissionId,
            25000,
            $dueDate
        );
        
        $paymentId = $this->paymentModel->submitPayment(
            $this->testVoucherId,
            25000,
            'TXN123456',
            'test/proof.jpg'
        );
        
        $admin = $this->userModel->findByEmail('admin@ithm.edu.pk');
        $verified = $this->paymentModel->verify($paymentId, $admin['id'], 'verified');
        
        // Check voucher status changed to paid
        $voucher = $this->voucherModel->find($this->testVoucherId);
        
        // Clean up
        $this->paymentModel->delete($paymentId);
        
        return TestRunner::assertEquals('paid', $voucher['status'], 'Voucher should be marked as paid');
    }
}

