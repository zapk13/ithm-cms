<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/admin/campuses" class="text-slate-500 hover:text-slate-700 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Campuses
        </a>
        <h1 class="text-2xl font-bold text-slate-800 mt-2">Edit Campus</h1>
    </div>
    
    <form method="POST" action="<?= BASE_URL ?>/admin/campuses/<?= $campus['id'] ?>" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
        
        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Basic Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Campus Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($campus['name']) ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Campus Code</label>
                        <input type="text" value="<?= htmlspecialchars($campus['code']) ?>" disabled
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Type <span class="text-red-500">*</span></label>
                        <select name="type" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="sub" <?= $campus['type'] === 'sub' ? 'selected' : '' ?>>Sub Campus</option>
                            <option value="main" <?= $campus['type'] === 'main' ? 'selected' : '' ?>>Main Campus</option>
                        </select>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($campus['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">City</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($campus['city'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($campus['phone'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($campus['email'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Focal Person</label>
                        <input type="text" name="focal_person" value="<?= htmlspecialchars($campus['focal_person'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" <?= $campus['is_active'] ? 'checked' : '' ?>
                                   class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-slate-700">Active Campus</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Logo Upload -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Campus Logo</h2>
                <p class="text-sm text-slate-500 mt-1">Upload campus logo (will be displayed on PDFs)</p>
            </div>
            <div class="p-6">
                <?php if (!empty($campus['logo'])): ?>
                <div class="mb-4">
                    <p class="text-sm text-slate-600 mb-2">Current Logo:</p>
                    <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($campus['logo']) ?>" 
                         alt="Campus Logo" 
                         class="h-24 w-auto border border-slate-200 rounded-lg p-2 bg-white">
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Upload New Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <p class="text-xs text-slate-500 mt-2">Recommended: PNG or JPG, max 2MB, 300x300px or larger</p>
                </div>
            </div>
        </div>
        
        <!-- Contact Person Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Point of Contact</h2>
                <p class="text-sm text-slate-500 mt-1">Primary contact person for this campus</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Contact Person Name</label>
                        <input type="text" name="contact_person_name" value="<?= htmlspecialchars($campus['contact_person_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Contact Person Phone</label>
                        <input type="tel" name="contact_person_phone" value="<?= htmlspecialchars($campus['contact_person_phone'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Contact Person Email</label>
                        <input type="email" name="contact_person_email" value="<?= htmlspecialchars($campus['contact_person_email'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bank Account Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Bank Account Information</h2>
                <p class="text-sm text-slate-500 mt-1">Bank details for fee challan generation</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account Holder Name</label>
                        <input type="text" name="bank_account_name" value="<?= htmlspecialchars($campus['bank_account_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Account Number</label>
                        <input type="text" name="bank_account_number" value="<?= htmlspecialchars($campus['bank_account_number'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Bank Name</label>
                        <input type="text" name="bank_name" value="<?= htmlspecialchars($campus['bank_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Branch</label>
                        <input type="text" name="bank_branch" value="<?= htmlspecialchars($campus['bank_branch'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">IBAN (Optional)</label>
                        <input type="text" name="iban" value="<?= htmlspecialchars($campus['iban'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="PK00XXXX0000000000000000">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= BASE_URL ?>/admin/campuses" 
               class="px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                <i class="fas fa-save mr-2"></i>Update Campus
            </button>
        </div>
    </form>
</div>
