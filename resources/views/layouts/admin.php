<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> | ITHM Admin</title>
    
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
                        },
                        slate: { 750:'#2c2f3f',850:'#232635' }
                    },
                    fontFamily: { sans: ['Montserrat', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #9aa0b5; border-radius: 3px; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-900" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="fixed inset-y-0 left-0 z-50 bg-primary-800 text-white transition-all duration-300 hidden lg:block shadow-xl">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-700">
                <a href="<?= BASE_URL ?>/admin/dashboard" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                        <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-8 h-8 object-contain">
                    </div>
                    <span x-show="sidebarOpen" class="font-bold text-lg tracking-tight">ITHM CMS</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-200 hover:text-white lg:block hidden">
                    <i :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'" class="fas"></i>
                </button>
            </div>
            
            <!-- User Info -->
            <div class="p-4 border-b border-slate-700" x-show="sidebarOpen">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent-400 text-primary-900 rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold"><?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars($user['name'] ?? '') ?></p>
                        <p class="text-xs text-slate-200/80 truncate"><?= htmlspecialchars($user['role_name'] ?? '') ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto scrollbar-thin" style="height: calc(100vh - 180px);">
                <?php
                $currentPath = $_SERVER['REQUEST_URI'];
                $menuItems = [
                    ['icon' => 'fa-gauge-high', 'label' => 'Dashboard', 'url' => '/admin/dashboard', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-building', 'label' => 'Campuses', 'url' => '/admin/campuses', 'roles' => ['system_admin']],
                    ['icon' => 'fa-book-open', 'label' => 'Courses', 'url' => '/admin/courses', 'roles' => ['system_admin', 'main_campus_admin']],
                    ['icon' => 'fa-money-bill', 'label' => 'Fee Structures', 'url' => '/admin/fee-structures', 'roles' => ['system_admin', 'main_campus_admin']],
                    ['icon' => 'fa-file-lines', 'label' => 'Admissions', 'url' => '/admin/admissions', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-receipt', 'label' => 'Fee Vouchers', 'url' => '/admin/fee-vouchers', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-clock', 'label' => 'Pending Payments', 'url' => '/admin/pending-payments', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-clipboard-list', 'label' => 'Exams', 'url' => '/admin/exams', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-chart-line', 'label' => 'Results', 'url' => '/admin/results', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-user-check', 'label' => 'Attendance', 'url' => '/admin/attendance', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-users', 'label' => 'Users', 'url' => '/admin/users', 'roles' => ['system_admin']],
                    ['icon' => 'fa-certificate', 'label' => 'Certificates', 'url' => '/admin/certificates', 'roles' => ['system_admin', 'main_campus_admin', 'sub_campus_admin']],
                    ['icon' => 'fa-cog', 'label' => 'Settings', 'url' => '/admin/settings', 'roles' => ['system_admin']],
                ];
                
                foreach ($menuItems as $item):
                    if (!in_array($user['role_slug'] ?? '', $item['roles'])) continue;
                    $isActive = strpos($currentPath, $item['url']) !== false;
                ?>
                <a href="<?= BASE_URL . $item['url'] ?>" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all <?= $isActive ? 'bg-accent-400 text-primary-900 font-semibold' : 'text-slate-200 hover:bg-primary-700 hover:text-white' ?>">
                    <i class="fas <?= $item['icon'] ?> w-5 text-center"></i>
                    <span x-show="sidebarOpen"><?= $item['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        
        <!-- Mobile Sidebar -->
        <div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 lg:hidden">
            <div class="fixed inset-0 bg-black/50" @click="mobileMenu = false"></div>
            <aside class="fixed inset-y-0 left-0 w-64 bg-primary-800 text-white">
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-700">
                    <a href="<?= BASE_URL ?>/admin/dashboard" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
                            <img src="<?= BASE_URL ?>/assets/images/ithm_logo_ai.png" alt="ITHM" class="w-8 h-8 object-contain">
                        </div>
                        <span class="font-bold tracking-tight">ITHM CMS</span>
                    </a>
                    <button @click="mobileMenu = false" class="text-slate-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <nav class="p-4 space-y-1">
                    <?php foreach ($menuItems as $item):
                        if (!in_array($user['role_slug'] ?? '', $item['roles'])) continue;
                        $isActive = strpos($currentPath, $item['url']) !== false;
                    ?>
                    <a href="<?= BASE_URL . $item['url'] ?>" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isActive ? 'bg-accent-400 text-primary-900 font-semibold' : 'text-slate-200 hover:bg-primary-700' ?>">
                        <i class="fas <?= $item['icon'] ?>"></i>
                        <span><?= $item['label'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </nav>
            </aside>
        </div>
        
        <!-- Main Content -->
        <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'" class="flex-1 transition-all duration-300">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button @click="mobileMenu = true" class="lg:hidden text-primary-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-primary-800"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 text-slate-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-accent-400 rounded-full"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 py-2">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <h3 class="font-semibold text-slate-800">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <p class="px-4 py-8 text-center text-slate-500 text-sm">No new notifications</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-primary-50 rounded-lg">
                            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-500"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-2">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="font-medium text-slate-800"><?= htmlspecialchars($user['name'] ?? '') ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                            </div>
                            <form method="POST" action="<?= BASE_URL ?>/logout">
                                <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
                                <button type="submit" class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="p-4 lg:p-6">
                <?php
                $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $_content) . '.php';
                if (file_exists($viewPath)) {
                    include $viewPath;
                }
                ?>
            </main>
        </div>
    </div>
    
    <!-- Toast -->
    <?php if (isset($_SESSION['flash'])): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
         class="fixed bottom-4 right-4 z-50">
        <div class="px-6 py-4 rounded-xl shadow-lg text-white flex items-center gap-3 <?= isset($_SESSION['flash']['success']) ? 'bg-primary-600' : 'bg-red-500' ?>">
            <i class="fas <?= isset($_SESSION['flash']['success']) ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <span><?= htmlspecialchars($_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? '') ?></span>
            <button @click="show = false" class="ml-2"><i class="fas fa-times"></i></button>
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

