<div x-data="paymentsPage()" class="space-y-6">
    <!-- Unpaid Vouchers Section -->
    <?php if (!empty($unpaidVouchers)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Unpaid Fee Vouchers</h2>
            <p class="text-sm text-slate-500 mt-1">Vouchers awaiting payment submission</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Voucher No</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Due Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($unpaidVouchers as $voucher): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $voucher['id'] ?>/pdf" target="_blank" class="font-mono text-sm text-primary-600 hover:underline">
                                <?= htmlspecialchars($voucher['voucher_no']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($voucher['student_name'] ?? 'N/A') ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($voucher['student_email'] ?? '') ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">PKR <?= number_format((float)$voucher['amount'], 2) ?></p>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <?= date('M d, Y', strtotime($voucher['due_date'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $voucher['id'] ?>/pdf" target="_blank"
                               class="px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 text-sm">
                                <i class="fas fa-file-pdf mr-1"></i> View PDF
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Submitted Payments Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Pending Payment Verifications</h2>
            <p class="text-sm text-slate-500 mt-1">Review and verify student payment submissions</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Voucher</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Transaction ID</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Submitted</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($submittedPayments)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-check-circle text-4xl mb-3 text-green-500"></i>
                            <p>No pending verifications</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($submittedPayments as $payment): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $payment['voucher_id'] ?? '' ?>/pdf" target="_blank" class="font-mono text-sm text-primary-600 hover:underline">
                                <?= htmlspecialchars($payment['voucher_no']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($payment['student_name']) ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($payment['student_email']) ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">PKR <?= number_format($payment['amount_paid'], 2) ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-sm bg-slate-100 px-2 py-1 rounded"><?= htmlspecialchars($payment['transaction_id']) ?></code>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="<?= BASE_URL ?>/storage/uploads/<?= $payment['proof_file'] ?>" target="_blank"
                                   class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                    <i class="fas fa-eye mr-1"></i> Proof
                                </a>
                                <button @click="verifyPayment(<?= $payment['id'] ?>, 'verified')" 
                                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm">
                                    <i class="fas fa-check mr-1"></i> Verify
                                </button>
                                <button @click="showReject(<?= $payment['id'] ?>)" 
                                        class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm">
                                    <i class="fas fa-times mr-1"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="rejectModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Reject Payment</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason for rejection</label>
                <textarea x-model="rejectReason" rows="3" 
                          class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500"
                          placeholder="e.g., Invalid transaction ID, proof unclear..."></textarea>
            </div>
            <div class="flex gap-3">
                <button @click="rejectModal = false" class="flex-1 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="verifyPayment(rejectId, 'rejected')" class="flex-1 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </div>
    </div>
</div>

<script>
function paymentsPage() {
    return {
        rejectModal: false,
        rejectId: null,
        rejectReason: '',
        
        showReject(id) {
            this.rejectId = id;
            this.rejectReason = '';
            this.rejectModal = true;
        },
        
        async verifyPayment(id, status) {
            try {
                const result = await window.api.post(`<?= BASE_URL ?>/admin/payments/${id}/verify`, {
                    status: status,
                    remarks: this.rejectReason
                });
                
                if (result.success) {
                    showToast(result.message || `Payment ${status} successfully`);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error || 'Failed to update payment', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            
            this.rejectModal = false;
        }
    }
}
</script>

