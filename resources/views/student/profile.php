<div x-data="profilePage()" class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
        <p class="text-slate-500 mt-1">Manage your account settings</p>
    </div>
    
    <!-- Profile Info -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Profile Information</h2>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/student/profile" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
            
            <div class="flex items-center gap-6 mb-6">
                <div class="w-20 h-20 bg-primary-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                    <?php if ($user['profile_image']): ?>
                    <img src="<?= BASE_URL ?>/storage/uploads/<?= $user['profile_image'] ?>" class="w-20 h-20 rounded-full object-cover">
                    <?php else: ?>
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Profile Photo</label>
                    <input type="file" name="profile_image" accept="image/*"
                           class="text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-500">
                    <p class="text-xs text-slate-500 mt-1">Email cannot be changed</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
    
    <!-- Change Password -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Change Password</h2>
        </div>
        <form @submit.prevent="changePassword()" class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                    <input type="password" x-model="passwordForm.current_password" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                    <input type="password" x-model="passwordForm.new_password" required minlength="8"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                    <input type="password" x-model="passwordForm.new_password_confirmation" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" :disabled="changing" class="px-6 py-3 bg-slate-800 text-white rounded-xl hover:bg-slate-900 font-medium disabled:opacity-50">
                    <span x-show="!changing"><i class="fas fa-key mr-2"></i>Change Password</span>
                    <span x-show="changing"><i class="fas fa-spinner fa-spin mr-2"></i>Changing...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function profilePage() {
    return {
        passwordForm: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        changing: false,
        
        async changePassword() {
            if (this.passwordForm.new_password !== this.passwordForm.new_password_confirmation) {
                showToast('Passwords do not match', 'error');
                return;
            }
            
            this.changing = true;
            
            try {
                const result = await window.api.post('<?= BASE_URL ?>/student/change-password', this.passwordForm);
                
                if (result.success) {
                    showToast('Password changed successfully');
                    this.passwordForm = { current_password: '', new_password: '', new_password_confirmation: '' };
                } else {
                    showToast(result.error || 'Failed to change password', 'error');
                }
            } catch (e) {
                showToast('An error occurred', 'error');
            }
            
            this.changing = false;
        }
    }
}
</script>

