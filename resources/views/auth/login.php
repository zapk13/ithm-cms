<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ITHM CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f6fb',
                            100: '#e8e9f6',
                            200: '#cfd1ea',
                            300: '#aeb2da',
                            400: '#7f84c1',
                            500: '#5559a0',
                            600: '#353688',
                            700: '#2f2f74',
                            800: '#282864',
                            900: '#20204f'
                        },
                        accent: {
                            50: '#fff7e6',
                            100: '#ffeac2',
                            200: '#ffd98a',
                            300: '#ffc24d',
                            400: '#fcb116',
                            500: '#e19c0f',
                            600: '#c8850a',
                            700: '#a86808',
                            800: '#864f0a',
                            900: '#6b3d09'
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .bg-mesh {
            background: radial-gradient(circle at 20% 20%, rgba(252,177,22,0.25), transparent 35%),
                        radial-gradient(circle at 80% 0%, rgba(85,89,160,0.25), transparent 30%),
                        linear-gradient(135deg, #353688 0%, #282864 60%, #20204f 100%);
        }
        .glass {
            backdrop-filter: blur(16px);
            background: rgba(255,255,255,0.95);
        }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-900">
    <div class="min-h-screen flex" x-data="{ loading: false }">
        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-mesh relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-yellow-400 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-16 text-white">
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg shadow-black/10">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-12 h-12 object-contain">
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white tracking-tight">ITHM</h1>
                            <p class="text-accent-200 text-sm">College Management System</p>
                        </div>
                    </div>
                </div>
                
                <h2 class="text-4xl font-semibold mb-4 leading-tight">
                    Welcome to <span class="text-accent-300">ITHM CMS</span>
                </h2>
                <p class="text-accent-100 text-lg mb-8 max-w-md">
                    Secure staff access for admissions, finance, academics, and attendance.
                </p>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white border border-primary-100 rounded-xl flex items-center justify-center">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-10 h-10 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-primary-800">ITHM CMS</span>
                    </div>
                </div>
                
                <div class="glass rounded-3xl shadow-2xl shadow-primary-600/10 p-8 border border-primary-50">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-primary-800 mb-2">Sign In</h2>
                        <p class="text-slate-600">Staff-only access. Use your assigned credentials.</p>
                    </div>
                    
                    <?php if (isset($_SESSION['flash']['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['flash']['error']) ?></span>
                    </div>
                    <?php unset($_SESSION['flash']['error']); endif; ?>
                    
                    <?php if (isset($_SESSION['flash']['success'])): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-600 text-sm flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['flash']['success']) ?></span>
                    </div>
                    <?php unset($_SESSION['flash']['success']); endif; ?>
                    
                    <form method="POST" action="<?= BASE_URL ?>/login" @submit="loading = true">
                        <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" required
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="you@example.com">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input :type="show ? 'text' : 'password'" name="password" required
                                           class="w-full pl-12 pr-12 py-3.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all bg-gray-50 focus:bg-white"
                                           placeholder="••••••••">
                                    <button type="button" @click="show = !show" 
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fas"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">Remember me</span>
                                </label>
                                <a href="<?= BASE_URL ?>/forgot-password" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    Forgot password?
                                </a>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="loading"
                                    class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-lg shadow-primary-500/30 disabled:opacity-70">
                                <span x-show="!loading">Sign In</span>
                                <span x-show="loading" class="flex items-center justify-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i> Signing in...
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-8 text-center text-sm text-slate-500">
                        Need help? Contact IT support.
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

