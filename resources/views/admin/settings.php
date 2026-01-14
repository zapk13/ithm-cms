<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
        <p class="text-slate-500 mt-1">Configure institute and system settings</p>
    </div>
    
    <form method="POST" action="<?= BASE_URL ?>/admin/settings">
        <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
        
        <!-- Institute Information -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800"><i class="fas fa-building mr-2 text-primary-600"></i>Institute Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Institute Name</label>
                        <input type="text" name="institute_name" value="<?= htmlspecialchars($settings['institute_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Short Name</label>
                        <input type="text" name="institute_short_name" value="<?= htmlspecialchars($settings['institute_short_name'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="institute_email" value="<?= htmlspecialchars($settings['institute_email'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input type="tel" name="institute_phone" value="<?= htmlspecialchars($settings['institute_phone'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <input type="text" name="institute_address" value="<?= htmlspecialchars($settings['institute_address'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fee Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800"><i class="fas fa-money-bill mr-2 text-primary-600"></i>Fee Settings</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fee Due Reminder (Days Before)</label>
                        <input type="number" name="fee_due_reminder_days" value="<?= htmlspecialchars($settings['fee_due_reminder_days'] ?? '7') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Admission Fee Due Days</label>
                        <input type="number" name="admission_fee_due_days" value="<?= htmlspecialchars($settings['admission_fee_due_days'] ?? '14') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SMTP Settings - Internal (Admin Notifications) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">
                    <i class="fas fa-envelope mr-2 text-blue-600"></i>Internal Email (Admin Notifications)
                </h2>
                <p class="text-sm text-slate-500 mt-1">SMTP settings for System Admin, Main Campus Admin, and Sub Campus Admin notifications</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_internal_host" value="<?= htmlspecialchars($settings['smtp_internal_host'] ?? '') ?>"
                               placeholder="mail.yourdomain.com"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Port</label>
                        <input type="number" name="smtp_internal_port" value="<?= htmlspecialchars($settings['smtp_internal_port'] ?? '587') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Username</label>
                        <input type="text" name="smtp_internal_username" value="<?= htmlspecialchars($settings['smtp_internal_username'] ?? '') ?>"
                               placeholder="noreply@yourdomain.com"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Password</label>
                        <input type="password" name="smtp_internal_password" value="<?= htmlspecialchars($settings['smtp_internal_password'] ?? '') ?>"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Email</label>
                        <input type="email" name="smtp_internal_from_email" value="<?= htmlspecialchars($settings['smtp_internal_from_email'] ?? '') ?>"
                               placeholder="admin@yourdomain.com"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Name</label>
                        <input type="text" name="smtp_internal_from_name" value="<?= htmlspecialchars($settings['smtp_internal_from_name'] ?? 'ITHM Admin') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Encryption</label>
                        <select name="smtp_internal_encryption"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="tls" <?= ($settings['smtp_internal_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($settings['smtp_internal_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="" <?= empty($settings['smtp_internal_encryption']) ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="testSmtp('internal')" class="px-4 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>Test Connection
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SMTP Settings - External (Student Notifications) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">
                    <i class="fas fa-users mr-2 text-green-600"></i>External Email (Student Notifications)
                </h2>
                <p class="text-sm text-slate-500 mt-1">SMTP settings for student notifications (admission status, fee vouchers, reminders, certificates)</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="smtp_external_same_as_internal" value="1" 
                               <?= ($settings['smtp_external_same_as_internal'] ?? '0') == '1' ? 'checked' : '' ?>
                               onchange="toggleExternalSmtp(this)"
                               class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-600">Use same settings as Internal Email</span>
                    </label>
                </div>
                
                <div id="external-smtp-fields" class="<?= ($settings['smtp_external_same_as_internal'] ?? '0') == '1' ? 'hidden' : '' ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Host</label>
                            <input type="text" name="smtp_external_host" value="<?= htmlspecialchars($settings['smtp_external_host'] ?? '') ?>"
                                   placeholder="mail.yourdomain.com"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Port</label>
                            <input type="number" name="smtp_external_port" value="<?= htmlspecialchars($settings['smtp_external_port'] ?? '587') ?>"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Username</label>
                            <input type="text" name="smtp_external_username" value="<?= htmlspecialchars($settings['smtp_external_username'] ?? '') ?>"
                                   placeholder="notifications@yourdomain.com"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">SMTP Password</label>
                            <input type="password" name="smtp_external_password" value="<?= htmlspecialchars($settings['smtp_external_password'] ?? '') ?>"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">From Email</label>
                            <input type="email" name="smtp_external_from_email" value="<?= htmlspecialchars($settings['smtp_external_from_email'] ?? '') ?>"
                                   placeholder="notifications@yourdomain.com"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">From Name</label>
                            <input type="text" name="smtp_external_from_name" value="<?= htmlspecialchars($settings['smtp_external_from_name'] ?? 'ITHM Notifications') ?>"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Encryption</label>
                            <select name="smtp_external_encryption"
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                                <option value="tls" <?= ($settings['smtp_external_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($settings['smtp_external_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="" <?= empty($settings['smtp_external_encryption']) ? 'selected' : '' ?>>None</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="testSmtp('external')" class="px-4 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Test Connection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<script>
function toggleExternalSmtp(checkbox) {
    const fields = document.getElementById('external-smtp-fields');
    if (checkbox.checked) {
        fields.classList.add('hidden');
    } else {
        fields.classList.remove('hidden');
    }
}

async function testSmtp(type) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    btn.disabled = true;
    
    try {
        const response = await fetch('<?= BASE_URL ?>/admin/test-smtp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type: type,
                _token: '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✓ SMTP connection successful! Test email sent.');
        } else {
            alert('✗ SMTP Error: ' + (result.error || 'Connection failed'));
        }
    } catch (e) {
        alert('✗ Failed to test SMTP connection');
    }
    
    btn.innerHTML = originalText;
    btn.disabled = false;
}
</script>

