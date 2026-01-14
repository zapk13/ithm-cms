<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Fee Vouchers</h1>
            <p class="text-slate-500 mt-1">View all generated fee vouchers</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="unpaid" <?= ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="pending_verification" <?= ($filters['status'] ?? '') === 'pending_verification' ? 'selected' : '' ?>>Pending Verification</option>
                    <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="overdue" <?= ($filters['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>
            <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Campus</label>
                <select name="campus_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="">All Campuses</option>
                    <?php foreach ($campuses as $campus): ?>
                    <option value="<?= $campus['id'] ?>" <?= ($filters['campus_id'] ?? '') == $campus['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($campus['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="px-6 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Voucher</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Due Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Campus</th>
                        <?php endif; ?>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($vouchers)): ?>
                    <tr>
                        <td colspan="<?= $user['role_slug'] !== 'sub_campus_admin' ? '7' : '6' ?>" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
                            <p>No vouchers found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($vouchers as $voucher): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $voucher['id'] ?>/pdf" target="_blank" class="font-mono text-sm text-primary-600 font-medium hover:underline">
                                <?= htmlspecialchars($voucher['voucher_no']) ?>
                            </a>
                            <?php if ($voucher['application_no']): ?>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($voucher['application_no']) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($voucher['student_name']) ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($voucher['student_email']) ?></p>
                        </td>
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
                        <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($voucher['campus_name'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td class="px-6 py-4 space-x-2">
                            <a href="<?= BASE_URL ?>/admin/fee-vouchers/<?= $voucher['id'] ?>/pdf" target="_blank"
                               class="px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 text-sm inline-flex items-center gap-1">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <?php if (!in_array($voucher['status'], ['paid','cancelled'], true)): ?>
                            <button onclick="cancelVoucher(<?= (int)$voucher['id'] ?>)"
                                    class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm inline-flex items-center gap-1">
                                <i class="fas fa-ban"></i> Void
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function cancelVoucher(id) {
    if (!confirm('Void this voucher? It will no longer appear in pending payments.')) {
        return;
    }
    try {
        const result = await window.api.post(`<?= BASE_URL ?>/admin/fee-vouchers/${id}/cancel`, {});
        if (result.success) {
            showToast(result.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(result.error || 'Failed to cancel voucher', 'error');
        }
    } catch (e) {
        showToast('An error occurred', 'error');
    }
}
</script>

