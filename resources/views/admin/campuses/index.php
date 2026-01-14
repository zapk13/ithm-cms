<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Campus Management</h1>
            <p class="text-slate-500 mt-1">Manage main and sub campuses</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/campuses/create" 
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
            <i class="fas fa-plus mr-2"></i>Add Campus
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($campuses as $campus): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center <?= $campus['type'] === 'main' ? 'bg-primary-100' : 'bg-blue-100' ?>">
                            <i class="fas fa-building text-xl <?= $campus['type'] === 'main' ? 'text-primary-600' : 'text-blue-600' ?>"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800"><?= htmlspecialchars($campus['name']) ?></h3>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full <?= $campus['type'] === 'main' ? 'bg-primary-100 text-primary-700' : 'bg-blue-100 text-blue-700' ?>">
                                <?= ucfirst($campus['type']) ?>
                            </span>
                        </div>
                    </div>
                    <span class="w-3 h-3 rounded-full <?= $campus['is_active'] ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                </div>
                
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <?php if ($campus['city']): ?>
                    <p><i class="fas fa-map-marker-alt w-5 text-slate-400"></i><?= htmlspecialchars($campus['city']) ?></p>
                    <?php endif; ?>
                    <?php if ($campus['phone']): ?>
                    <p><i class="fas fa-phone w-5 text-slate-400"></i><?= htmlspecialchars($campus['phone']) ?></p>
                    <?php endif; ?>
                    <?php if ($campus['focal_person']): ?>
                    <p><i class="fas fa-user w-5 text-slate-400"></i><?= htmlspecialchars($campus['focal_person']) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?= $campus['student_count'] ?? 0 ?></p>
                        <p class="text-xs text-slate-500">Students</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?= $campus['course_count'] ?? 0 ?></p>
                        <p class="text-xs text-slate-500">Courses</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-600"><?= $campus['pending_admissions'] ?? 0 ?></p>
                        <p class="text-xs text-slate-500">Pending</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <a href="<?= BASE_URL ?>/admin/campuses/<?= $campus['id'] ?>/edit" 
                   class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                    <i class="fas fa-edit mr-1"></i>Edit Campus
                </a>
                <?php if ($campus['type'] !== 'main'): ?>
                <button onclick="deleteCampus(<?= (int)$campus['id'] ?>)"
                        class="text-xs text-red-600 hover:text-red-700 font-medium">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
async function deleteCampus(id) {
    if (!confirm('Are you sure you want to delete this campus? This is only allowed when no admissions or vouchers exist.')) {
        return;
    }
    try {
        const result = await window.api.post(`<?= BASE_URL ?>/admin/campuses/${id}/delete`, {});
        if (result.success) {
            showToast(result.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(result.error || 'Failed to delete campus', 'error');
        }
    } catch (e) {
        showToast('An error occurred', 'error');
    }
}
</script>

