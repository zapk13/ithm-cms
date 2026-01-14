<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Exams Management</h1>
            <p class="text-slate-500">Overview of terms, schedules, and registrations</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="text-primary-600 hover:underline text-sm">Back to dashboard</a>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Terms</p>
            <p class="text-3xl font-bold text-slate-800 mt-1"><?= number_format($stats['terms'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Scheduled</p>
            <p class="text-3xl font-bold text-amber-600 mt-1"><?= number_format($stats['scheduled'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Completed</p>
            <p class="text-3xl font-bold text-green-600 mt-1"><?= number_format($stats['completed'] ?? 0) ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">Registrations</p>
            <p class="text-3xl font-bold text-blue-600 mt-1"><?= number_format($stats['registrations'] ?? 0) ?></p>
        </div>
    </div>
    
    <!-- Quick create -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Create Exam Term</h2>
                <p class="text-sm text-slate-500">Define term/semester window</p>
            </div>
            <form class="p-6 space-y-4" method="POST" action="<?= BASE_URL ?>/admin/exams/terms">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Name</label>
                        <input name="name" required class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Spring 2025">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Code</label>
                        <input name="code" required class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="SP25">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Start Date</label>
                        <input type="date" name="start_date" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">End Date</label>
                        <input type="date" name="end_date" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Save Term</button>
            </form>
        </div>
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Schedule Exam</h2>
                <p class="text-sm text-slate-500">Link course to term and set slot</p>
            </div>
            <form class="p-6 space-y-4" method="POST" action="<?= BASE_URL ?>/admin/exams">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Term</label>
                        <select name="exam_term_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                            <option value="">Select term</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?= $term['id'] ?>"><?= htmlspecialchars($term['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Course</label>
                        <select name="course_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                            <option value="">Select course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Title</label>
                    <input name="title" required class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Midterm Exam">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Type</label>
                        <select name="exam_type" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                            <option value="midterm">Midterm</option>
                            <option value="final">Final</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                            <option value="practical">Practical</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Date</label>
                        <input type="date" name="exam_date" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Venue</label>
                        <input name="venue" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Hall A">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Start Time</label>
                        <input type="time" name="start_time" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">End Time</label>
                        <input type="time" name="end_time" required class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Total Marks</label>
                            <input type="number" step="0.01" name="total_marks" class="w-full rounded-lg border border-slate-200 px-3 py-2" value="100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Weightage (%)</label>
                            <input type="number" step="0.01" name="weightage" class="w-full rounded-lg border border-slate-200 px-3 py-2" value="0">
                        </div>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Schedule Exam</button>
            </form>
        </div>
    </div>
    
    <!-- Recent exams -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Recent Exams</h2>
            <span class="text-sm text-slate-500">Showing latest <?= count($recentExams ?? []) ?> entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentExams)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-slate-500">No exams yet</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentExams as $exam): ?>
                    <tr>
                        <td class="px-6 py-3">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($exam['title']) ?></p>
                            <p class="text-xs text-slate-500">Course #<?= htmlspecialchars($exam['course_id']) ?></p>
                        </td>
                        <td class="px-6 py-3 text-slate-600"><?= ucfirst($exam['exam_type']) ?></td>
                        <td class="px-6 py-3 text-slate-600"><?= htmlspecialchars($exam['exam_date']) ?></td>
                        <td class="px-6 py-3">
                            <?php
                            $colors = [
                                'scheduled' => 'bg-amber-100 text-amber-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700'
                            ];
                            $color = $colors[$exam['status']] ?? 'bg-slate-100 text-slate-700';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= ucfirst($exam['status']) ?>
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

