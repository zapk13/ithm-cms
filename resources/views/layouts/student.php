<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> | ITHM Student Portal</title>
    
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
                    fontFamily: { sans: ['Montserrat', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased bg-white min-h-screen text-slate-900">
    <div x-data="{ mobileMenu: false }">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <a href="<?= BASE_URL ?>/student/dashboard" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white border border-primary-100 rounded-xl flex items-center justify-center">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-8 h-8 object-contain">
                        </div>
                        <div class="hidden sm:block">
                            <span class="font-bold text-primary-800">ITHM</span>
                            <span class="text-slate-600 text-sm ml-1">Student Portal</span>
                        </div>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center gap-1">
                        <?php
                        $currentPath = $_SERVER['REQUEST_URI'];
                        $navItems = [
                            ['icon' => 'fa-gauge-high', 'label' => 'Dashboard', 'url' => '/student/dashboard'],
                            ['icon' => 'fa-file-lines', 'label' => 'Apply', 'url' => '/student/admission/new'],
                            ['icon' => 'fa-receipt', 'label' => 'Fees', 'url' => '/student/fees'],
                            ['icon' => 'fa-certificate', 'label' => 'Certificates', 'url' => '/student/certificates'],
                            ['icon' => 'fa-bell', 'label' => 'Notifications', 'url' => '/student/notifications', 'badge' => $unreadCount ?? 0],
                        ];
                        foreach ($navItems as $item):
                            $isActive = strpos($currentPath, $item['url']) !== false;
                        ?>
                        <a href="<?= BASE_URL . $item['url'] ?>" 
                           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $isActive ? 'bg-accent-100 text-primary-800' : 'text-slate-700 hover:bg-primary-50' ?>">
                            <i class="fas <?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                            <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                            <span class="w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $item['badge'] ?></span>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </nav>
                    
                    <div class="flex items-center gap-4">
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-primary-50 rounded-lg">
                                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    <?= strtoupper(substr($user['name'] ?? 'S', 0, 1)) ?>
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-slate-700"><?= htmlspecialchars($user['name'] ?? '') ?></span>
                                <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-2">
                                <a href="<?= BASE_URL ?>/student/profile" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <hr class="my-2 border-slate-100">
                                <form method="POST" action="<?= BASE_URL ?>/logout">
                                    <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
                                    <button type="submit" class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-200">
                <nav class="px-4 py-3 space-y-1">
                    <?php foreach ($navItems as $item):
                        $isActive = strpos($currentPath, $item['url']) !== false;
                    ?>
                    <a href="<?= BASE_URL . $item['url'] ?>" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $isActive ? 'bg-accent-100 text-primary-800' : 'text-slate-700 hover:bg-primary-50' ?>">
                        <i class="fas <?= $item['icon'] ?>"></i>
                        <span><?= $item['label'] ?></span>
                        <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                        <span class="ml-auto w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $item['badge'] ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php
            $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $_content) . '.php';
            if (file_exists($viewPath)) {
                include $viewPath;
            }
            ?>
        </main>
        
        <!-- Footer -->
        <footer class="mt-auto py-6 text-center text-sm text-slate-500">
            <p>&copy; <?= date('Y') ?> ITHM - Institute of Tourism and Hospitality Management</p>
        </footer>
    </div>
    
    <!-- Toast -->
    <?php if (isset($_SESSION['flash'])): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
         class="fixed bottom-4 right-4 z-50">
        <div class="px-6 py-4 rounded-xl shadow-lg text-white flex items-center gap-3 <?= isset($_SESSION['flash']['success']) ? 'bg-primary-600' : 'bg-red-500' ?>">
            <i class="fas <?= isset($_SESSION['flash']['success']) ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <span><?= htmlspecialchars($_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? '') ?></span>
            <button @click="show = false"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); endif; ?>
    
    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        window.api = {
            post: async (url, data = {}) => {
                const formData = new FormData();
                formData.append('_token', window.csrfToken);
                Object.entries(data).forEach(([key, value]) => formData.append(key, value));
                const response = await fetch(url, { method: 'POST', body: formData });
                return response.json();
            }
        };
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg text-white flex items-center gap-3 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    </script>
</body>
</html>

