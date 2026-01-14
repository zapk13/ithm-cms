<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= BASE_URL ?>/admin/campuses" class="text-slate-500 hover:text-slate-700 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Campuses
        </a>
        <h1 class="text-2xl font-bold text-slate-800 mt-2">Add New Campus</h1>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="<?= BASE_URL ?>/admin/campuses" class="p-6">
            <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Campus Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g., ITHM Lahore Campus">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Campus Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" required maxlength="20"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g., LHR">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Type <span class="text-red-500">*</span></label>
                        <select name="type" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="sub">Sub Campus</option>
                            <option value="main">Main Campus</option>
                        </select>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                                  placeholder="Full address"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">City</label>
                        <input type="text" name="city"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g., Lahore">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" name="phone"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="+92-XXX-XXXXXXX">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="campus@ithm.edu.pk">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Focal Person</label>
                        <input type="text" name="focal_person"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"
                               placeholder="Contact person name">
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="<?= BASE_URL ?>/admin/campuses" 
                       class="px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                        <i class="fas fa-save mr-2"></i>Create Campus
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

