<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Class Attendance</h1>
            <p class="text-slate-500">Session coverage and student participation</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="text-primary-600 hover:underline text-sm">Back to dashboard</a>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Sessions</p>
            <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['sessions'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Completed</p>
            <p class="text-3xl font-bold text-green-600 mt-1"><?= number_format($stats['completed'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Scheduled</p>
            <p class="text-3xl font-bold text-amber-600 mt-1"><?= number_format($stats['scheduled'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Records</p>
            <p class="text-3xl font-bold text-blue-600 mt-1"><?= number_format($stats['records'] ?? 0) ?></p>
        </div>
    </div>
    
    <!-- Quick create -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Create Attendance Session</h2>
            <p class="text-sm text-slate-500">Add a class meeting to track attendance</p>
        </div>
        <form class="p-6 space-y-4" method="POST" action="<?= BASE_URL ?>/admin/attendance/sessions">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Course</label>
                    <select name="course_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Date</label>
                    <input type="date" name="session_date" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Type</label>
                    <select name="session_type" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="lecture">Lecture</option>
                        <option value="lab">Lab</option>
                        <option value="tutorial">Tutorial</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Start Time</label>
                    <input type="time" name="start_time" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">End Time</label>
                    <input type="time" name="end_time" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Topic</label>
                    <input name="topic" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Chapter 3">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Instructor (optional)</label>
                <input name="instructor_id" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="User ID">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Create Session</button>
        </form>
    </div>
    
    <!-- Recent sessions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Recent Sessions</h2>
            <span class="text-sm text-slate-500">Showing latest <?= count($recentSessions ?? []) ?> entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentSessions)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-slate-500">No sessions yet</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentSessions as $session): ?>
                    <tr>
                        <td class="px-6 py-3 text-slate-700">Course #<?= htmlspecialchars($session['course_id']) ?></td>
                        <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($session['session_date']) ?></td>
                        <td class="px-6 py-3 text-slate-700"><?= ucfirst($session['session_type']) ?></td>
                        <td class="px-6 py-3">
                            <?php
                            $colors = [
                                'scheduled' => 'bg-amber-100 text-amber-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700'
                            ];
                            $color = $colors[$session['status']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= ucfirst($session['status']) ?>
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

