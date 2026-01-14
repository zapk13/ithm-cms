<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Students</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['total_students'] ?? 0) ?></p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600"><i class="fas fa-arrow-up mr-1"></i>12%</span>
                <span class="text-slate-500 ml-2">from last month</span>
            </div>
        </div>
        
        <!-- Pending Admissions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">Pending Admissions</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['admissions']['pending'] ?? 0) ?></p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-amber-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/admin/admissions?status=pending" class="text-sm text-primary-600 hover:underline">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Approved Admissions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">Approved Admissions</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['admissions']['approved'] ?? 0) ?></p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/admin/admissions?status=approved" class="text-sm text-primary-600 hover:underline">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <!-- Pending Payments -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">Pending Verifications</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['pending_payments'] ?? 0) ?></p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/admin/pending-payments" class="text-sm text-primary-600 hover:underline">
                    Verify now <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Financials Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Financial Overview</h2>
            <p class="text-sm text-slate-500 mt-1">Fee collection and verification status</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Verified Fees -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-green-700 bg-green-200 px-2 py-1 rounded-full">Verified</span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Total Verified Fees</p>
                    <p class="text-2xl font-bold text-green-700">PKR <?= number_format($financialStats['total_verified'] ?? 0, 2) ?></p>
                    <p class="text-xs text-slate-500 mt-2">Fees successfully verified</p>
                </div>
                
                <!-- Pending Verification -->
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border border-amber-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-200 px-2 py-1 rounded-full">In Review</span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Pending Verification</p>
                    <p class="text-2xl font-bold text-amber-700">PKR <?= number_format($financialStats['total_pending_verification'] ?? 0, 2) ?></p>
                    <p class="text-xs text-slate-500 mt-2">Payment proofs under review</p>
                </div>
                
                <!-- Rejected Payments -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ban text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-red-700 bg-red-200 px-2 py-1 rounded-full">Rejected</span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Rejected Payments</p>
                    <p class="text-2xl font-bold text-red-700">PKR <?= number_format($financialStats['total_rejected'] ?? 0, 2) ?></p>
                    <p class="text-xs text-slate-500 mt-2">Need resubmission</p>
                </div>
                
                <!-- Total Pending Fees -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-purple-700 bg-purple-200 px-2 py-1 rounded-full">Pending</span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Total Pending Fees</p>
                    <p class="text-2xl font-bold text-purple-700">PKR <?= number_format($financialStats['total_pending'] ?? 0, 2) ?></p>
                    <p class="text-xs text-slate-500 mt-2">No payment submitted yet</p>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-500 font-medium mb-1">Verified Collected</p>
                        <p class="text-xl font-bold text-slate-800">
                            PKR <?= number_format($financialStats['total_verified'] ?? 0, 2) ?>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Cleared and verified</p>
                    </div>
                    <div class="text-center p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-500 font-medium mb-1">In Verification Queue</p>
                        <p class="text-xl font-bold text-slate-800">
                            PKR <?= number_format($financialStats['total_pending_verification'] ?? 0, 2) ?>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Awaiting review</p>
                    </div>
                    <div class="text-center p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-500 font-medium mb-1">Outstanding</p>
                        <p class="text-xl font-bold text-red-600">
                            <?php 
                            $outstanding = ($financialStats['total_pending'] ?? 0)
                                + ($financialStats['total_pending_verification'] ?? 0)
                                + ($financialStats['total_rejected'] ?? 0);
                            ?>
                            PKR <?= number_format($outstanding, 2) ?>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Pending + In review + Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Admissions -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Recent Admission Applications</h2>
                <a href="<?= BASE_URL ?>/admin/admissions" class="text-sm text-primary-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Applicant</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Course</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recentAdmissions)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                                <p>No pending admissions</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recentAdmissions as $admission): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['student_name']) ?></p>
                                    <p class="text-sm text-slate-500"><?= htmlspecialchars($admission['application_no'] ?? '') ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-600"><?= htmlspecialchars($admission['course_name']) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'update_required' => 'bg-blue-100 text-blue-700'
                                ];
                                $color = $statusColors[$admission['status']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-3 py-1 text-xs font-medium rounded-full <?= $color ?>">
                                    <?= ucfirst(str_replace('_', ' ', $admission['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>" 
                                   class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Review <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Fee Defaulters -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Fee Defaulters</h2>
                <button onclick="sendFeeReminders()" class="text-sm bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-200 transition-colors">
                    <i class="fas fa-bell mr-1"></i> Send Reminders
                </button>
            </div>
            <div class="p-6 space-y-4">
                <?php if (empty($defaulters)): ?>
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i>
                    <p>No fee defaulters!</p>
                </div>
                <?php else: ?>
                <?php foreach ($defaulters as $defaulter): ?>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                    <div>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($defaulter['name']) ?></p>
                        <p class="text-sm text-slate-500"><?= $defaulter['overdue_count'] ?> overdue voucher(s)</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-red-600">PKR <?= number_format($defaulter['total_due']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= BASE_URL ?>/admin/admissions?status=pending" 
               class="flex flex-col items-center p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
                <i class="fas fa-file-lines text-2xl text-amber-600 mb-2"></i>
                <span class="text-sm font-medium text-slate-700">Review Admissions</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/pending-payments" 
               class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <i class="fas fa-credit-card text-2xl text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-slate-700">Verify Payments</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/certificates" 
               class="flex flex-col items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <i class="fas fa-certificate text-2xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-slate-700">Upload Certificates</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/fee-vouchers" 
               class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <i class="fas fa-receipt text-2xl text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-slate-700">View Vouchers</span>
            </a>
        </div>
    </div>
</div>

<script>
async function sendFeeReminders() {
    if (!confirm('Send fee reminders to all defaulters?')) return;
    
    try {
        const result = await window.api.post('<?= BASE_URL ?>/admin/fee-reminders');
        showToast(result.message || 'Reminders sent!', result.success ? 'success' : 'error');
    } catch (e) {
        showToast('Failed to send reminders', 'error');
    }
}
</script>

