<div x-data="feesPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Fee Vouchers</h1>
            <p class="text-slate-500 mt-1">View and pay your fee vouchers</p>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Voucher No</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Due Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($vouchers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
                            <p>No fee vouchers yet</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($vouchers as $voucher): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-primary-600 font-medium"><?= htmlspecialchars($voucher['voucher_no']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= ucfirst($voucher['fee_type']) ?></td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800">PKR <?= number_format($voucher['amount'], 2) ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= date('M d, Y', strtotime($voucher['due_date'])) ?></td>
                        <td class="px-6 py-4">
                            <?php
                            $statusConfig = [
                                'unpaid' => ['bg-amber-100 text-amber-700', 'Unpaid'],
                                'pending_verification' => ['bg-blue-100 text-blue-700', 'Pending Verification'],
                                'paid' => ['bg-green-100 text-green-700', 'Paid'],
                                'overdue' => ['bg-red-100 text-red-700', 'Overdue']
                            ];
                            $config = $statusConfig[$voucher['status']] ?? ['bg-gray-100 text-gray-700', $voucher['status']];
                            ?>
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?= $config[0] ?>">
                                <?= $config[1] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="<?= BASE_URL ?>/student/fees/<?= $voucher['id'] ?>" 
                                   class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <?php if (in_array($voucher['status'], ['unpaid', 'overdue'])): ?>
                                <button @click="openPaymentModal(<?= $voucher['id'] ?>, '<?= $voucher['voucher_no'] ?>', <?= $voucher['amount'] ?>)" 
                                        class="px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                    <i class="fas fa-credit-card mr-1"></i>Pay
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div x-show="paymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="paymentModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <button @click="paymentModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <h3 class="text-xl font-semibold text-slate-800 mb-2">Upload Payment Proof</h3>
            <p class="text-slate-500 text-sm mb-6">Voucher: <span class="font-mono" x-text="selectedVoucherNo"></span></p>
            
            <div class="p-4 bg-primary-50 rounded-xl mb-6">
                <p class="text-sm text-primary-700">Amount to Pay</p>
                <p class="text-2xl font-bold text-primary-800">PKR <span x-text="selectedAmount?.toLocaleString()"></span></p>
            </div>
            
            <form @submit.prevent="submitPayment()" enctype="multipart/form-data">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Transaction ID <span class="text-red-500">*</span></label>
                        <input type="text" x-model="transactionId" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="Enter bank transaction ID">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Receipt/Screenshot <span class="text-red-500">*</span></label>
                        <input type="file" @change="proofFile = $event.target.files[0]" accept="image/*,.pdf" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700">
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="paymentModal = false" 
                            class="flex-1 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" :disabled="submitting"
                            class="flex-1 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!submitting">Submit Proof</span>
                        <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function feesPage() {
    return {
        paymentModal: false,
        selectedVoucherId: null,
        selectedVoucherNo: '',
        selectedAmount: 0,
        transactionId: '',
        proofFile: null,
        submitting: false,
        
        openPaymentModal(id, voucherNo, amount) {
            this.selectedVoucherId = id;
            this.selectedVoucherNo = voucherNo;
            this.selectedAmount = amount;
            this.transactionId = '';
            this.proofFile = null;
            this.paymentModal = true;
        },
        
        async submitPayment() {
            if (!this.transactionId || !this.proofFile) {
                showToast('Please fill all required fields', 'error');
                return;
            }
            
            this.submitting = true;
            
            const formData = new FormData();
            formData.append('_token', window.csrfToken);
            formData.append('transaction_id', this.transactionId);
            formData.append('proof', this.proofFile);
            
            try {
                const response = await fetch(`<?= BASE_URL ?>/student/fees/${this.selectedVoucherId}/pay`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message || 'Payment proof submitted successfully');
                    this.paymentModal = false;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.error || 'Failed to submit payment', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            
            this.submitting = false;
        }
    }
}
</script>

