<div x-data="usersPage()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
            <p class="text-slate-500 mt-1">Manage system users and their roles</p>
        </div>
        <button @click="showModal = true; editMode = false; resetForm()"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">
            <i class="fas fa-plus mr-2"></i>Add User
        </button>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                       placeholder="Name or email" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                <select name="role_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= ($filters['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Campus</label>
                <select name="campus_id" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="">All Campuses</option>
                    <?php foreach ($campuses as $campus): ?>
                    <option value="<?= $campus['id'] ?>" <?= ($filters['campus_id'] ?? '') == $campus['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($campus['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">User</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Role</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Campus</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800"><?= htmlspecialchars($u['name']) ?></p>
                                    <p class="text-sm text-slate-500"><?= htmlspecialchars($u['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                <?= htmlspecialchars($u['role_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($u['campus_name'] ?? 'All') ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?= $u['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="editUser(<?= htmlspecialchars(json_encode($u)) ?>)"
                                    class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- User Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-semibold text-slate-800 mb-6" x-text="editMode ? 'Edit User' : 'Add New User'"></h3>
            
            <form @submit.prevent="saveUser()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" x-model="form.email" required :disabled="editMode"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 disabled:bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" x-model="form.phone"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div x-show="!editMode">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" x-model="form.password" :required="!editMode"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div x-show="editMode">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-slate-700">New Password</label>
                            <span class="text-xs text-slate-500">Leave blank to keep current</span>
                        </div>
                        <input type="password" x-model="form.password" placeholder="Enter new password"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select x-model="form.role_id" required
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Campus</label>
                            <select x-model="form.campus_id"
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                                <option value="">None (All Campuses)</option>
                                <?php foreach ($campuses as $campus): ?>
                                <option value="<?= $campus['id'] ?>"><?= htmlspecialchars($campus['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div x-show="editMode">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" x-model="form.is_active"
                                   class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-slate-700">Active User</span>
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
function usersPage() {
    return {
        showModal: false,
        editMode: false,
        saving: false,
        csrfToken: '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>',
        form: { id: null, name: '', email: '', phone: '', password: '', role_id: '', campus_id: '', is_active: true },
        
        resetForm() {
            this.form = { id: null, name: '', email: '', phone: '', password: '', role_id: '', campus_id: '', is_active: true };
        },
        
        editUser(user) {
            this.form = { ...user, is_active: !!user.is_active, password: '' };
            this.editMode = true;
            this.showModal = true;
        },
        
        async saveUser() {
            this.saving = true;
            const url = this.editMode ? `<?= BASE_URL ?>/admin/users/${this.form.id}` : '<?= BASE_URL ?>/admin/users';
            
            try {
                const payload = { ...this.form, csrf_token: this.csrfToken };
                const result = await window.api.post(url, payload);
                if (result.success) {
                    showToast(result.message);
                    this.showModal = false;
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(result.error || result.message || 'Failed to save user', 'error');
                }
            } catch (e) {
                const msg = e?.response?.data?.error || e?.message || 'An error occurred';
                showToast(msg, 'error');
            }
            this.saving = false;
        }
    }
}
</script>

