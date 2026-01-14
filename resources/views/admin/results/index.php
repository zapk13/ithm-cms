<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Results Management</h1>
            <p class="text-slate-500">Track publishing status and outcomes</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="text-primary-600 hover:underline text-sm">Back to dashboard</a>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Total Results</p>
            <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['total_results'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Published</p>
            <p class="text-3xl font-bold text-green-600 mt-1"><?= number_format($stats['published'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">In Progress</p>
            <p class="text-3xl font-bold text-amber-600 mt-1"><?= number_format($stats['in_progress'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Failed</p>
            <p class="text-3xl font-bold text-red-600 mt-1"><?= number_format($stats['failed'] ?? 0) ?></p>
        </div>
    </div>
    
    <!-- Recent results -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Recent Course Results</h2>
            <span class="text-sm text-slate-500">Showing latest <?= count($recentResults ?? []) ?> entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Grade</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentResults)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-slate-500">No results yet</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentResults as $result): ?>
                    <tr>
                        <td class="px-6 py-3 text-slate-700">Course #<?= htmlspecialchars($result['course_id']) ?></td>
                        <td class="px-6 py-3 text-slate-700">Student #<?= htmlspecialchars($result['user_id']) ?></td>
                        <td class="px-6 py-3 font-semibold text-slate-800"><?= htmlspecialchars($result['grade'] ?? '-') ?></td>
                        <td class="px-6 py-3">
                            <?php
                            $colors = [
                                'passed' => 'bg-green-100 text-green-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'in_progress' => 'bg-amber-100 text-amber-700',
                                'incomplete' => 'bg-slate-100 text-slate-700'
                            ];
                            $color = $colors[$result['status']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= ucfirst(str_replace('_', ' ', $result['status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

