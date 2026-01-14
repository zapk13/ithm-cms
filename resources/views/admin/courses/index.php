<div x-data="coursesPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Course Management</h1>
            <p class="text-slate-500 mt-1">Manage courses and campus assignments</p>
        </div>
        <button @click="showModal = true; editMode = false; resetForm()"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
            <i class="fas fa-plus mr-2"></i>Add Course
        </button>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Code</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Duration</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Seats</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-book-open text-4xl mb-3 opacity-50"></i>
                            <p>No courses found. Add your first course!</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($course['name']) ?></p>
                            <p class="text-sm text-slate-500 truncate max-w-xs"><?= htmlspecialchars($course['description'] ?? '') ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <code class="px-2 py-1 bg-slate-100 rounded text-sm"><?= htmlspecialchars($course['code']) ?></code>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= $course['duration_months'] ?> months</td>
                        <td class="px-6 py-4 text-slate-600"><?= $course['total_seats'] ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?= $course['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <button @click="editCourse(<?= htmlspecialchars(json_encode($course)) ?>)"
                                    class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button @click="confirmDeleteCourse(<?= (int)$course['id'] ?>)"
                                    class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold text-slate-800 mb-6" x-text="editMode ? 'Edit Course' : 'Add New Course'"></h3>
            
            <form @submit.prevent="saveCourse()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Course Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Course Code <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.code" required :disabled="editMode"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 disabled:bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea x-model="form.description" rows="2"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Duration (months)</label>
                            <input type="number" x-model="form.duration_months" min="1" max="60"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Total Seats</label>
                            <input type="number" x-model="form.total_seats" min="1"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div x-show="editMode">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" x-model="form.is_active"
                                   class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-slate-700">Active</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showModal = false" 
                            class="flex-1 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" :disabled="saving"
                            class="flex-1 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!saving" x-text="editMode ? 'Update' : 'Create'"></span>
                        <span x-show="saving"><i class="fas fa-spinner fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function coursesPage() {
    return {
        showModal: false,
        editMode: false,
        saving: false,
        deleting: false,
        form: { id: null, name: '', code: '', description: '', duration_months: 12, total_seats: 50, is_active: true },
        
        resetForm() {
            this.form = { id: null, name: '', code: '', description: '', duration_months: 12, total_seats: 50, is_active: true };
        },
        
        editCourse(course) {
            this.form = { ...course, is_active: !!course.is_active };
            this.editMode = true;
            this.showModal = true;
        },
        
        async saveCourse() {
            this.saving = true;
            const url = this.editMode ? `<?= BASE_URL ?>/admin/courses/${this.form.id}` : '<?= BASE_URL ?>/admin/courses';
            
            try {
                const result = await window.api.post(url, this.form);
                if (result.success) {
                    showToast(result.message);
                    this.showModal = false;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.error, 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            this.saving = false;
        },

        async confirmDeleteCourse(id) {
            if (!confirm('Are you sure you want to delete this course? This is only allowed when no admissions exist.')) {
                return;
            }
            this.deleting = true;
            try {
                const result = await window.api.post(`<?= BASE_URL ?>/admin/courses/${id}/delete`, {});
                if (result.success) {
                    showToast(result.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(result.error || 'Failed to delete course', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            this.deleting = false;
        }
    }
}
</script>

