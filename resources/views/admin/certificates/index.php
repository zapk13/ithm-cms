<div x-data="certificatesPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Certificate Management</h1>
            <p class="text-slate-500 mt-1">Upload and manage student certificates</p>
        </div>
        <button @click="showModal = true"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
            <i class="fas fa-upload mr-2"></i>Upload Certificate
        </button>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Student</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Course</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Roll Number</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Issued Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($certificates)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-certificate text-4xl mb-3 opacity-50"></i>
                            <p>No certificates uploaded yet</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($certificates as $cert): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($cert['student_name']) ?></p>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($cert['course_name']) ?></td>
                        <td class="px-6 py-4">
                            <code class="px-2 py-1 bg-slate-100 rounded text-sm"><?= htmlspecialchars($cert['roll_number'] ?? 'N/A') ?></code>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= ucfirst($cert['certificate_type'] ?? 'completion') ?></td>
                        <td class="px-6 py-4 text-sm text-slate-500"><?= date('M d, Y', strtotime($cert['issued_at'])) ?></td>
                        <td class="px-6 py-4">
                            <a href="<?= BASE_URL ?>/storage/uploads/<?= $cert['file_path'] ?>" target="_blank"
                               class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Upload Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold text-slate-800 mb-6">Upload Certificate</h3>
            
            <form @submit.prevent="uploadCertificate()" enctype="multipart/form-data">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Student <span class="text-red-500">*</span></label>
                        <select x-model="admissionId" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="">Select enrolled student</option>
                            <?php foreach ($enrolledStudents as $student): ?>
                            <option value="<?= $student['admission_id'] ?>">
                                <?= htmlspecialchars($student['student_name']) ?> - <?= htmlspecialchars($student['roll_number']) ?> (<?= htmlspecialchars($student['course_name']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Certificate File (PDF/Image) <span class="text-red-500">*</span></label>
                        <input type="file" @change="certificateFile = $event.target.files[0]" accept=".pdf,image/*" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700">
                    </div>
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showModal = false" 
                            class="flex-1 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" :disabled="uploading"
                            class="flex-1 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 disabled:opacity-50">
                        <span x-show="!uploading">Upload Certificate</span>
                        <span x-show="uploading"><i class="fas fa-spinner fa-spin mr-2"></i>Uploading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function certificatesPage() {
    return {
        showModal: false,
        uploading: false,
        admissionId: '',
        certificateFile: null,
        
        async uploadCertificate() {
            if (!this.admissionId || !this.certificateFile) {
                showToast('Please fill all required fields', 'error');
                return;
            }
            
            this.uploading = true;
            
            const formData = new FormData();
            formData.append('_token', window.csrfToken);
            formData.append('admission_id', this.admissionId);
            formData.append('certificate', this.certificateFile);
            
            try {
                const response = await fetch('<?= BASE_URL ?>/admin/certificates', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
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
            
            this.uploading = false;
        }
    }
}
</script>

