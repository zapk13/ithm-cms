<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | ITHM CMS</title>
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
                        sans: ['Montserrat', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .bg-mesh {
            background: radial-gradient(circle at 15% 20%, rgba(252,177,22,0.25), transparent 35%),
                        radial-gradient(circle at 80% 0%, rgba(85,89,160,0.25), transparent 30%),
                        linear-gradient(135deg, #353688 0%, #282864 60%, #20204f 100%);
        }
        .glass { backdrop-filter: blur(16px); background: rgba(255,255,255,0.96); }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-900">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden border border-primary-50">
            <!-- Left panel -->
            <div class="hidden md:flex bg-mesh text-white relative p-8 items-center">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-16 left-16 w-48 h-48 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute bottom-10 right-14 w-60 h-60 bg-yellow-400 rounded-full blur-3xl"></div>
                </div>
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg shadow-black/10">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <p class="text-sm text-accent-200">ITHM CMS</p>
                            <h2 class="text-2xl font-semibold">Password Assistance</h2>
                        </div>
                    </div>
                    <p class="text-accent-100 leading-relaxed">
                        Securely reset your password and get back to the dashboard. Your data stays protected with our latest authentication experience.
                    </p>
                </div>
            </div>

            <!-- Right panel -->
            <div class="p-8">
                <div class="mb-8 text-center md:text-left">
                    <div class="md:hidden inline-flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-9 h-9 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-primary-800">ITHM CMS</span>
                    </div>
                    <h1 class="text-3xl font-bold text-primary-800 mb-2">Forgot Password</h1>
                    <p class="text-slate-600">Enter your email and we’ll email you reset instructions.</p>
                </div>

                <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-600 text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['flash']['success']) ?></span>
                </div>
                <?php unset($_SESSION['flash']['success']); endif; ?>

                <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['flash']['error']) ?></span>
                </div>
                <?php unset($_SESSION['flash']['error']); endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/forgot-password">
                    <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">

                    <div class="mb-6">
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

                    <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 focus:ring-4 focus:ring-primary-500/30 transition-all shadow-lg shadow-primary-500/30">
                        Send reset link
                    </button>
                </form>

                <div class="mt-6 text-center md:text-left">
                    <a href="<?= BASE_URL ?>/login" class="text-sm text-primary-600 hover:text-primary-700 font-medium inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

