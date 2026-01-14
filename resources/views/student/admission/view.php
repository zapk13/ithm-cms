<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="<?= BASE_URL ?>/student/dashboard" class="text-slate-500 hover:text-slate-700 text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">Application: <?= htmlspecialchars($admission['application_no']) ?></h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/student/applications/<?= $admission['id'] ?>/pdf" 
               target="_blank"
               class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <?php
            $statusColors = [
                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                'approved' => 'bg-green-100 text-green-700 border-green-200',
                'rejected' => 'bg-red-100 text-red-700 border-red-200',
                'update_required' => 'bg-blue-100 text-blue-700 border-blue-200'
            ];
            $color = $statusColors[$admission['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="px-4 py-2 text-sm font-semibold rounded-full border <?= $color ?>">
                <?= ucfirst(str_replace('_', ' ', $admission['status'])) ?>
            </span>
        </div>
    </div>
    
    <?php if ($admission['status'] === 'update_required'): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation text-blue-700"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800">Action Required</h3>
                <p class="text-sm text-blue-700 mt-1"><?= htmlspecialchars($admission['admin_remarks'] ?? 'Please update your application.') ?></p>
                <a href="<?= BASE_URL ?>/student/applications/<?= $admission['id'] ?>/edit" 
                   class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-edit mr-2"></i>Edit Application
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($admission['status'] === 'approved' && $admission['roll_number']): ?>
    <div class="bg-green-50 border border-green-200 rounded-2xl p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-700 text-xl"></i>
            </div>
            <div>
                <p class="text-green-800 font-medium">Congratulations! Your admission is confirmed.</p>
                <p class="text-green-700 text-lg mt-1">Roll Number: <strong><?= htmlspecialchars($admission['roll_number']) ?></strong></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- Personal Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-user mr-2 text-primary-600"></i>Personal Information</h2>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-500">Full Name</label>
                        <p class="font-medium"><?= htmlspecialchars($admission['personal_info']['full_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Father Name</label>
                        <p class="font-medium"><?= htmlspecialchars($admission['personal_info']['father_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">CNIC</label>
                        <p class="font-medium"><?= htmlspecialchars($admission['personal_info']['cnic'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-500">Phone</label>
                        <p class="font-medium"><?= htmlspecialchars($admission['personal_info']['phone'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Documents -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800"><i class="fas fa-file-alt mr-2 text-primary-600"></i>Documents</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($admission['documents'])): ?>
                    <p class="text-slate-500 text-center py-4">No documents uploaded</p>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($admission['documents'] as $doc): ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-slate-200">
                                    <i class="fas fa-file-image text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700"><?= ucwords(str_replace('_', ' ', $doc['document_type'])) ?></p>
                                    <p class="text-xs text-slate-500"><?= ucfirst($doc['status']) ?></p>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/storage/uploads/<?= $doc['file_path'] ?>" target="_blank" 
                               class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                View <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Application Details</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <label class="text-slate-500">Course</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['course_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-slate-500">Campus</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['campus_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-slate-500">Shift</label>
                        <p class="font-medium text-slate-800"><?= ucfirst($admission['shift']) ?></p>
                    </div>
                    <div>
                        <label class="text-slate-500">Submitted</label>
                        <p class="font-medium text-slate-800"><?= date('M d, Y', strtotime($admission['submitted_at'])) ?></p>
                    </div>
                    <?php if ($admission['batch']): ?>
                    <div>
                        <label class="text-slate-500">Batch</label>
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($admission['batch']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

