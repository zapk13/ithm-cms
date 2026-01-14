<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-3xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">Welcome, <?= htmlspecialchars($user['name'] ?? 'Student') ?>!</h1>
            <p class="text-primary-100">Manage your admissions, fees, and certificates from your dashboard.</p>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Applications</p>
                    <p class="text-2xl font-bold text-slate-800"><?= count($applications ?? []) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Pending Fees</p>
                    <p class="text-2xl font-bold text-slate-800"><?= count(array_filter($vouchers ?? [], fn($v) => $v['status'] !== 'paid')) ?></p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-xl text-amber-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Certificates</p>
                    <p class="text-2xl font-bold text-slate-800"><?= count($certificates ?? []) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-certificate text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Notifications</p>
                    <p class="text-2xl font-bold text-slate-800"><?= $unreadCount ?? 0 ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bell text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Applications -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">My Applications</h2>
                <a href="<?= BASE_URL ?>/student/admission/new" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>New Application
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if (empty($applications)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-lines text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-800 mb-2">No Applications Yet</h3>
                    <p class="text-slate-500 mb-4">Start your journey by submitting an admission application.</p>
                    <a href="<?= BASE_URL ?>/student/admission/new" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <i class="fas fa-plus mr-2"></i>Apply Now
                    </a>
                </div>
                <?php else: ?>
                <?php foreach ($applications as $app): ?>
                <div class="p-4 hover:bg-slate-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($app['course_name']) ?></p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($app['campus_name']) ?> • <?= htmlspecialchars($app['application_no'] ?? '') ?></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'update_required' => 'bg-blue-100 text-blue-700'
                            ];
                            $color = $statusColors[$app['status']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?= $color ?>">
                                <?= ucfirst(str_replace('_', ' ', $app['status'])) ?>
                            </span>
                            <a href="<?= BASE_URL ?>/student/applications/<?= $app['id'] ?>" class="text-primary-600 hover:text-primary-700">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <?php if ($app['roll_number']): ?>
                    <div class="mt-2 p-2 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-700"><i class="fas fa-id-card mr-2"></i>Roll Number: <strong><?= htmlspecialchars($app['roll_number']) ?></strong></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Notifications & Alerts -->
        <div class="space-y-6">
            <!-- Pending Fees Alert -->
            <?php 
            $pendingVouchers = array_filter($vouchers ?? [], fn($v) => in_array($v['status'], ['unpaid', 'overdue']));
            if (!empty($pendingVouchers)):
            ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-700"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-amber-800">Pending Fee Payment</h3>
                        <p class="text-sm text-amber-700 mt-1">You have <?= count($pendingVouchers) ?> unpaid fee voucher(s).</p>
                        <a href="<?= BASE_URL ?>/student/fees" class="inline-block mt-3 text-sm font-medium text-amber-800 hover:underline">
                            View Vouchers <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Recent Notifications -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">Recent Notifications</h2>
                    <a href="<?= BASE_URL ?>/student/notifications" class="text-sm text-primary-600 hover:underline">View All</a>
                </div>
                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    <?php if (empty($notifications)): ?>
                    <div class="p-6 text-center text-slate-500">
                        <i class="fas fa-bell-slash text-2xl mb-2"></i>
                        <p class="text-sm">No notifications</p>
                    </div>
                    <?php else: ?>
                    <?php foreach (array_slice($notifications, 0, 5) as $notif): ?>
                    <div class="p-4 hover:bg-slate-50 <?= !$notif['is_read'] ? 'bg-blue-50/50' : '' ?>">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 <?= !$notif['is_read'] ? 'bg-primary-100' : 'bg-slate-100' ?>">
                                <i class="fas fa-bell text-sm <?= !$notif['is_read'] ? 'text-primary-600' : 'text-slate-400' ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800"><?= htmlspecialchars($notif['title']) ?></p>
                                <p class="text-xs text-slate-500 mt-0.5"><?= date('M d, H:i', strtotime($notif['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

