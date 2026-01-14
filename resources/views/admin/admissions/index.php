<div x-data="admissionsPage()" class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                       placeholder="Name or App No." class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Status</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="update_required" <?= ($filters['status'] ?? '') === 'update_required' ? 'selected' : '' ?>>Update Required</option>
                </select>
            </div>
            <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Campus</label>
                <select name="campus_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Campuses</option>
                    <?php foreach ($campuses as $campus): ?>
                    <option value="<?= $campus['id'] ?>" <?= ($filters['campus_id'] ?? '') == $campus['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($campus['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Course</label>
                <select name="course_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($filters['course_id'] ?? '') == $course['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Admissions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-800">Admission Applications</h2>
            <a href="<?= BASE_URL ?>/admin/admissions/new" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-plus mr-2"></i>Submit New Admission
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Application</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Campus</th>
                        <?php endif; ?>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($admissions)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p>No admissions found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($admissions as $admission): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-primary-600"><?= htmlspecialchars($admission['application_no'] ?? 'N/A') ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['student_name']) ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($admission['student_email']) ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-700"><?= htmlspecialchars($admission['course_name']) ?></p>
                            <p class="text-xs text-slate-500"><?= htmlspecialchars($admission['course_code']) ?></p>
                        </td>
                        <?php if ($user['role_slug'] !== 'sub_campus_admin'): ?>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($admission['campus_name'] ?? '-') ?></td>
                        <?php endif; ?>
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
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <?= date('M d, Y', strtotime($admission['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="<?= BASE_URL ?>/admin/admissions/<?= $admission['id'] ?>" 
                               class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                <i class="fas fa-eye mr-1.5"></i> View
                            </a>
                            <?php if (in_array($admission['status'], ['pending','rejected','update_required'], true)): ?>
                            <button onclick="trashAdmission(<?= (int)$admission['id'] ?>)"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm">
                                <i class="fas fa-trash mr-1.5"></i> Trash
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
async function trashAdmission(id) {
    if (!confirm('Move this admission application to trash?')) {
        return;
    }
    try {
        const result = await window.api.post(`<?= BASE_URL ?>/admin/admissions/${id}/trash`, {});
        if (result.success) {
            showToast(result.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(result.error || 'Failed to trash admission', 'error');
        }
    } catch (e) {
        showToast('An error occurred', 'error');
    }
}
</script>

<script>
function admissionsPage() {
    return {
        // Add any Alpine.js functionality here
    }
}
</script>

