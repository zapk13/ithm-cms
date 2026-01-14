<div x-data="notificationsPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Notifications</h1>
            <p class="text-slate-500 mt-1">Stay updated with your application status</p>
        </div>
        <button @click="markAllRead()" class="px-4 py-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
            <i class="fas fa-check-double mr-2"></i>Mark all as read
        </button>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            <?php if (empty($notifications)): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-bell-slash text-4xl mb-3 opacity-50"></i>
                <p>No notifications yet</p>
            </div>
            <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
            <div class="p-4 hover:bg-slate-50 transition-colors <?= !$notif['is_read'] ? 'bg-blue-50/50 border-l-4 border-l-primary-500' : '' ?>"
                 @click="markRead(<?= $notif['id'] ?>)">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 
                         <?php 
                         $iconConfig = [
                             'admission' => ['bg-blue-100', 'fa-file-lines', 'text-blue-600'],
                             'fee' => ['bg-amber-100', 'fa-receipt', 'text-amber-600'],
                             'certificate' => ['bg-green-100', 'fa-certificate', 'text-green-600'],
                             'system' => ['bg-slate-100', 'fa-bell', 'text-slate-600'],
                             'manual' => ['bg-purple-100', 'fa-bullhorn', 'text-purple-600']
                         ];
                         $config = $iconConfig[$notif['type']] ?? $iconConfig['system'];
                         echo $config[0];
                         ?>">
                        <i class="fas <?= $config[1] ?> <?= $config[2] ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-medium text-slate-800"><?= htmlspecialchars($notif['title']) ?></h3>
                                <p class="text-sm text-slate-600 mt-1"><?= htmlspecialchars($notif['message']) ?></p>
                            </div>
                            <?php if (!$notif['is_read']): ?>
                            <span class="w-2 h-2 bg-primary-500 rounded-full flex-shrink-0 mt-2"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            <?= date('M d, Y \a\t h:i A', strtotime($notif['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function notificationsPage() {
    return {
        async markRead(id) {
            await window.api.post(`<?= BASE_URL ?>/student/notifications/${id}/read`);
        },
        
        async markAllRead() {
            await window.api.post('<?= BASE_URL ?>/student/notifications/read-all');
            showToast('All notifications marked as read');
            setTimeout(() => location.reload(), 1000);
        }
    }
}
</script>

