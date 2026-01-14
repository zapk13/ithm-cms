<div x-data="admissionView()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?= BASE_URL ?>/admin/admissions" class="text-slate-500 hover:text-slate-700 mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Admissions
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Application: <?= htmlspecialchars($admission['application_no']) ?></h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>/pdf" 
               target="_blank"
               class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <?php
            $statusColors = [
                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                'approved' => 'bg-green-100 text-green-700 border-green-200',
                'rejected' => 'bg-red-100 text-red-700 border-red-200',
                'update_required' => 'bg-blue-100 text-blue-700 border-blue-200'
            ];
            $color = $statusColors[$admission['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="px-4 py-2 text-sm font-semibold rounded-full border <?= $color ?>">
                <i class="fas fa-circle text-xs mr-2"></i>
                <?= ucfirst(str_replace('_', ' ', $admission['status'])) ?>
            </span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-user mr-2 text-primary-600"></i>Personal Information</h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-500">Full Name</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['full_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Father Name</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['father_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">CNIC</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['cnic'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Date of Birth</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['date_of_birth'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Phone</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['phone'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Email</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['email'] ?? '-') ?></p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm text-slate-500">Address</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['personal_info']['address'] ?? '-') ?>, <?= htmlspecialchars($admission['personal_info']['city'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Guardian Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-users mr-2 text-primary-600"></i>Guardian Information</h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-500">Name</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['guardian_info']['name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Relation</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['guardian_info']['relation'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Phone</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['guardian_info']['phone'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Occupation</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['guardian_info']['occupation'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Academic Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-graduation-cap mr-2 text-primary-600"></i>Academic Information</h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-500">Last Qualification</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['last_qualification'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Institution</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['institution'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Board/University</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['board'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Passing Year</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['passing_year'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Marks</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['marks_obtained'] ?? '-') ?> / <?= htmlspecialchars($admission['academic_info']['total_marks'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Grade</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['academic_info']['grade'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Documents -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-file-alt mr-2 text-primary-600"></i>Documents</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($admission['documents'])): ?>
                    <p class="text-slate-500 text-center py-4">No documents uploaded</p>
                    <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($admission['documents'] as $doc): ?>
                        <div class="border border-slate-200 rounded-xl p-4 hover:border-primary-300 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-image text-slate-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate"><?= ucwords(str_replace('_', ' ', $doc['document_type'])) ?></p>
                                    <p class="text-xs text-slate-500"><?= $doc['status'] ?></p>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/storage/uploads/<?= $doc['file_path'] ?>" target="_blank" 
                               class="mt-3 block text-center text-sm text-primary-600 hover:underline">
                                View Document
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Application Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Application Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm text-slate-500">Course</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['course_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Campus</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['campus_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Shift</label>
                        <p class="font-medium text-slate-800"><?= ucfirst($admission['shift']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Submitted</label>
                        <p class="font-medium text-slate-800"><?= date('M d, Y H:i', strtotime($admission['submitted_at'])) ?></p>
                    </div>
                    <?php if ($admission['roll_number']): ?>
                    <div>
                        <label class="text-sm text-slate-500">Roll Number</label>
                        <p class="font-medium text-primary-600"><?= htmlspecialchars($admission['roll_number']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions -->
            <?php if ($admission['status'] === 'pending'): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Take Action</h2>
                </div>
                <div class="p-6 space-y-3">
                    <?php if (empty($feeVoucher)): ?>
                    <button @click="generateFeeChallan()" 
                            :disabled="generatingChallan"
                            class="w-full py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-invoice-dollar" x-show="!generatingChallan"></i>
                        <i class="fas fa-spinner fa-spin" x-show="generatingChallan"></i>
                        <span x-text="generatingChallan ? 'Generating...' : 'Generate Fee Challan'"></span>
                    </button>
                    <?php else: ?>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 text-green-700">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="font-medium text-sm">Fee Challan Generated</span>
                                </div>
                                <p class="text-xs text-green-600 mt-1">
                                    Voucher: <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $feeVoucher['id'] ?>/pdf" target="_blank" class="font-mono text-primary-600 hover:underline"><?= htmlspecialchars($feeVoucher['voucher_no'] ?? 'N/A') ?></a>
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    Amount: <span class="font-semibold">PKR <?= number_format((float)$feeVoucher['amount'], 2) ?></span>
                                </p>
                                <?php if (!empty($payment)): ?>
                                <p class="text-xs text-green-600 mt-1">
                                    Payment Status: <span class="font-medium"><?= ucfirst($payment['status'] ?? 'pending') ?></span>
                                </p>
                                <?php endif; ?>
                            </div>
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $feeVoucher['id'] ?>/pdf" 
                               target="_blank"
                               class="px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <button @click="updateStatus('approved')" 
                            class="w-full py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Approve Application
                    </button>
                    <?php if (!empty($feeVoucher) && (empty($payment) || $payment['status'] !== 'verified')): ?>
                    <p class="text-xs text-amber-600 text-center">
                        <i class="fas fa-info-circle"></i> Fee payment must be verified before approval
                    </p>
                    <?php endif; ?>
                    <button @click="showRejectModal = true" 
                            class="w-full py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Reject Application
                    </button>
                    <button @click="showUpdateModal = true" 
                            class="w-full py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i> Request Update
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($admission['status'] === 'approved' && !$admission['roll_number']): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Assign Roll Number</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Roll Number</label>
                        <input type="text" x-model="rollNumber" 
                               class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="Auto-generate or enter custom">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Batch</label>
                        <input type="text" x-model="batch" 
                               class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g., 2024">
                    </div>
                    <button @click="assignRollNumber()" 
                            class="w-full py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition-colors">
                        <i class="fas fa-id-card mr-2"></i>Assign Roll Number
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Admin Remarks -->
            <?php if ($admission['admin_remarks']): ?>
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6">
                <h3 class="font-semibold text-amber-800 mb-2"><i class="fas fa-comment mr-2"></i>Admin Remarks</h3>
                <p class="text-amber-700"><?= htmlspecialchars($admission['admin_remarks']) ?></p>
                <?php if ($admission['reviewer_name']): ?>
                <p class="text-sm text-amber-600 mt-2">— <?= htmlspecialchars($admission['reviewer_name']) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showRejectModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Reject Application</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason for rejection</label>
                <textarea x-model="remarks" rows="3" 
                          class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                          placeholder="Enter reason..."></textarea>
            </div>
            <div class="flex gap-3">
                <button @click="showRejectModal = false" class="flex-1 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="updateStatus('rejected')" class="flex-1 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </div>
    </div>
    
    <!-- Update Required Modal -->
    <div x-show="showUpdateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showUpdateModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Request Update</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">What needs to be updated?</label>
                <textarea x-model="remarks" rows="3" 
                          class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                          placeholder="Specify what the student needs to update..."></textarea>
            </div>
            <div class="flex gap-3">
                <button @click="showUpdateModal = false" class="flex-1 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="updateStatus('update_required')" class="flex-1 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Send Request</button>
            </div>
        </div>
    </div>
</div>

<script>
function admissionView() {
    return {
        showRejectModal: false,
        showUpdateModal: false,
        remarks: '',
        rollNumber: '',
        batch: '<?= date('Y') ?>',
        generatingChallan: false,
        
        async generateFeeChallan() {
            if (this.generatingChallan) return;
            
            this.generatingChallan = true;
            try {
                const result = await window.api.post('<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>/fee-challan', {});
                
                if (result.success) {
                    showToast(result.message || 'Fee challan generated successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error || 'Failed to generate fee challan', 'error');
                }
            } catch (e) {
                showToast('An error occurred while generating fee challan', 'error');
            } finally {
                this.generatingChallan = false;
            }
        },
        
        async updateStatus(status) {
            try {
                const result = await window.api.post('<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>/status', {
                    status: status,
                    remarks: this.remarks
                });
                
                if (result.success) {
                    showToast(result.message || 'Status updated successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error || 'Failed to update status', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            
            this.showRejectModal = false;
            this.showUpdateModal = false;
        },
        
        async assignRollNumber() {
            try {
                const result = await window.api.post('<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>/roll-number', {
                    roll_number: this.rollNumber,
                    batch: this.batch
                });
                
                if (result.success) {
                    showToast('Roll number assigned: ' + result.roll_number);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error || 'Failed to assign roll number', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
        }
    }
}
</script>

