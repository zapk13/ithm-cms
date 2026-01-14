<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">My Certificates</h1>
        <p class="text-slate-500 mt-1">Download your course completion certificates</p>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($certificates)): ?>
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-medium text-slate-800 mb-2">No Certificates Yet</h3>
            <p class="text-slate-500 max-w-md mx-auto">
                Your certificates will appear here once you complete your course and the administration issues them.
            </p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-slate-100">
            <?php foreach ($certificates as $cert): ?>
            <div class="p-6 flex items-center justify-between hover:bg-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-certificate text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800"><?= htmlspecialchars($cert['course_name']) ?></h3>
                        <p class="text-sm text-slate-500">
                            <?= ucfirst($cert['certificate_type']) ?> Certificate • 
                            Issued: <?= date('M d, Y', strtotime($cert['issued_at'])) ?>
                        </p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/storage/uploads/<?= $cert['file_path'] ?>" target="_blank"
                   class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                    <i class="fas fa-download mr-2"></i>Download
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-amber-700"></i>
            </div>
            <div>
                <h3 class="font-semibold text-amber-800">Important Note</h3>
                <p class="text-sm text-amber-700 mt-1">
                    Please also collect your printed certificate from the campus. Contact the administration office for collection timing.
                </p>
            </div>
        </div>
    </div>
</div>

