<div x-data="feeStructuresPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Fee Structures</h1>
            <p class="text-slate-500 mt-1">Configure fee structures for courses at each campus (separate for morning/evening shifts)</p>
        </div>
        <button @click="showModal = true; resetForm()"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
            <i class="fas fa-plus mr-2"></i>Add Fee Structure
        </button>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Campus</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Shift</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Admission Fee</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Tuition Fee</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Total</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($feeStructures)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-money-bill text-4xl mb-3 opacity-50"></i>
                            <p>No fee structures configured</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($feeStructures as $fs): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($fs['course_name']) ?></p>
                            <code class="text-xs text-slate-500"><?= htmlspecialchars($fs['course_code']) ?></code>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($fs['campus_name']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= ($fs['shift'] ?? 'morning') === 'morning' ? 'bg-yellow-100 text-yellow-700' : 'bg-indigo-100 text-indigo-700' ?>">
                                <?= ucfirst($fs['shift'] ?? 'morning') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium">PKR <?= number_format((float)$fs['admission_fee'], 2) ?></td>
                        <td class="px-6 py-4 font-medium">PKR <?= number_format((float)$fs['tuition_fee'], 2) ?></td>
                        <td class="px-6 py-4 font-semibold text-primary-600">
                            PKR <?= number_format((float)$fs['admission_fee'] + (float)$fs['tuition_fee'] + (float)$fs['semester_fee'] + (float)$fs['monthly_fee'] + (float)$fs['exam_fee'] + (float)$fs['other_charges'], 2) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button @click="editStructure(<?= htmlspecialchars(json_encode($fs)) ?>)"
                                        class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button @click="deleteStructure(<?= (int)$fs['id'] ?>)"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
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
    
    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold text-slate-800 mb-6">
                <span x-text="form.id ? 'Edit Fee Structure' : 'Configure Fee Structure'"></span>
            </h3>
            
            <form @submit.prevent="saveStructure()">
                <input type="hidden" x-model="form.id" name="id">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Campus <span class="text-red-500">*</span></label>
                            <select x-model="form.campus_id" required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Campus</option>
                                <?php foreach ($campuses as $campus): ?>
                                <option value="<?= $campus['id'] ?>"><?= htmlspecialchars($campus['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Course <span class="text-red-500">*</span></label>
                            <select x-model="form.course_id" required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Shift <span class="text-red-500">*</span></label>
                        <select x-model="form.shift" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">You can create separate fee structures for morning and evening shifts</p>
                    </div>
                    
                    <div class="border-t border-slate-200 pt-4">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">Fee Components</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Admission Fee (PKR) <span class="text-red-500">*</span></label>
                                <input type="number" x-model="form.admission_fee" min="0" step="0.01" required
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tuition Fee (PKR)</label>
                                <input type="number" x-model="form.tuition_fee" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Semester Fee (PKR)</label>
                                <input type="number" x-model="form.semester_fee" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Monthly Fee (PKR)</label>
                                <input type="number" x-model="form.monthly_fee" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Exam Fee (PKR)</label>
                                <input type="number" x-model="form.exam_fee" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Other Charges (PKR)</label>
                                <input type="number" x-model="form.other_charges" min="0" step="0.01"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-slate-50 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-700">Total Fee:</span>
                                <span class="text-lg font-bold text-primary-600" x-text="'PKR ' + formatCurrency(calculateTotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showModal = false" 
                            class="flex-1 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" :disabled="saving"
                            class="flex-1 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!saving">Save Fee Structure</span>
                        <span x-show="saving"><i class="fas fa-spinner fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function feeStructuresPage() {
    return {
        showModal: false,
        saving: false,
        form: {
            id: null,
            course_id: '', campus_id: '', shift: 'morning', admission_fee: 0, tuition_fee: 0,
            semester_fee: 0, monthly_fee: 0, exam_fee: 0, other_charges: 0
        },
        
        resetForm() {
            this.form = {
                id: null,
                course_id: '', campus_id: '', shift: 'morning', admission_fee: 0, tuition_fee: 0,
                semester_fee: 0, monthly_fee: 0, exam_fee: 0, other_charges: 0
            };
        },
        
        editStructure(fs) {
            this.form = { ...fs };
            this.showModal = true;
        },
        
        calculateTotal() {
            return parseFloat(this.form.admission_fee || 0) +
                   parseFloat(this.form.tuition_fee || 0) +
                   parseFloat(this.form.semester_fee || 0) +
                   parseFloat(this.form.monthly_fee || 0) +
                   parseFloat(this.form.exam_fee || 0) +
                   parseFloat(this.form.other_charges || 0);
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-PK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        },
        
        async saveStructure() {
            this.saving = true;
            try {
                const result = await window.api.post('<?= BASE_URL ?>/admin/fee-structures', this.form);
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

        async deleteStructure(id) {
            if (!confirm('Are you sure you want to delete this fee structure?')) {
                return;
            }
            this.saving = true;
            try {
                const result = await window.api.post(`<?= BASE_URL ?>/admin/fee-structures/${id}/delete`, {});
                if (result.success) {
                    showToast(result.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(result.error || 'Failed to delete fee structure', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            this.saving = false;
        }
    }
}
</script>
