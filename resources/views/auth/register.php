<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ITHM CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 500: '#059669', 600: '#047857', 700: '#065f46' },
                        accent: { 400: '#fbbf24', 500: '#f59e0b' }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .bg-mesh { background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%); }
        .glass { backdrop-filter: blur(16px); background: rgba(255,255,255,0.95); }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex" x-data="{ loading: false }">
        <!-- Left Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-mesh relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-yellow-400 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-16 text-white">
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur">
                            <i class="fas fa-graduation-cap text-3xl text-accent-400"></i>
                        </div>
                        <div>
                            <h1 class="font-display text-3xl font-bold">ITHM</h1>
                            <p class="text-emerald-200 text-sm">Central Management System</p>
                        </div>
                    </div>
                </div>
                
                <h2 class="font-display text-4xl font-bold mb-6 leading-tight">
                    Start Your Journey<br>
                    <span class="text-accent-400">with ITHM</span>
                </h2>
                
                <p class="text-emerald-100 text-lg mb-8 max-w-md">
                    Join thousands of students pursuing excellence in tourism and hospitality management.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-accent-400"></i>
                        <span>Access to online admission portal</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-accent-400"></i>
                        <span>Track your application status</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-accent-400"></i>
                        <span>Download fee vouchers & certificates</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="w-full max-w-lg">
                <div class="lg:hidden text-center mb-6">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-2xl text-white"></i>
                        </div>
                        <span class="font-display text-2xl font-bold text-gray-900">ITHM CMS</span>
                    </div>
                </div>
                
                <div class="glass rounded-3xl shadow-2xl shadow-emerald-500/10 p-8 border border-gray-100">
                    <div class="text-center mb-6">
                        <h2 class="font-display text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                        <p class="text-gray-500">Register as a student to apply for admissions</p>
                    </div>
                    
                    <?php if (isset($_SESSION['flash']['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= $_SESSION['flash']['error'] ?>
                    </div>
                    <?php unset($_SESSION['flash']['error']); endif; ?>
                    
                    <?php $old = $_SESSION['old_input'] ?? []; unset($_SESSION['old_input']); ?>
                    
                    <form method="POST" action="<?= BASE_URL ?>/register" @submit="loading = true">
                        <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" name="name" required
                                           value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="John Doe">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" required
                                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="you@example.com">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" name="phone" required
                                               value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                                               class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                               placeholder="03XX-XXXXXXX">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CNIC</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <input type="text" name="cnic" required
                                               value="<?= htmlspecialchars($old['cnic'] ?? '') ?>"
                                               class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                               placeholder="XXXXX-XXXXXXX-X">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input :type="show ? 'text' : 'password'" name="password" required
                                           class="w-full pl-12 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="Minimum 8 characters">
                                    <button type="button" @click="show = !show" 
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" required
                                           class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="Re-enter password">
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <input type="checkbox" name="terms" required class="mt-1 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label class="text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-primary-600 hover:underline">Terms of Service</a> 
                                    and <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="loading"
                                    class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-lg shadow-primary-500/30 disabled:opacity-70">
                                <span x-show="!loading">Create Account</span>
                                <span x-show="loading" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Creating account...
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-gray-500">
                            Already have an account? 
                            <a href="<?= BASE_URL ?>/login" class="text-primary-600 hover:text-primary-700 font-semibold">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

