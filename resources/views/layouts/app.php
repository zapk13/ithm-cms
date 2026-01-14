<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
    <title><?= htmlspecialchars($title ?? 'ITHM CMS') ?> | ITHM CMS</title>
    
    <!-- Tailwind CSS -->
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
                            900: '#20204f',
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
                            900: '#6b3d09',
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-900">
    <?php
    $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $_content) . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    }
    ?>
    
    <!-- Toast Notifications -->
    <div x-data="{ show: <?= isset($_SESSION['flash']) ? 'true' : 'false' ?>, type: '<?= isset($_SESSION['flash']['success']) ? 'success' : (isset($_SESSION['flash']['error']) ? 'error' : 'info') ?>', message: '<?= htmlspecialchars($_SESSION['flash']['success'] ?? $_SESSION['flash']['error'] ?? $_SESSION['flash']['info'] ?? '') ?>' }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-cloak
         class="fixed top-4 right-4 z-50">
        <div :class="type === 'success' ? 'bg-primary-600' : type === 'error' ? 'bg-red-500' : 'bg-primary-400'"
             class="px-6 py-4 rounded-lg shadow-lg text-white flex items-center gap-3 animate-pulse">
            <i :class="type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'" class="fas text-xl"></i>
            <span x-html="message"></span>
            <button @click="show = false" class="ml-4 hover:opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
    
    <script>
        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Global fetch wrapper
        window.api = {
            post: async (url, data = {}) => {
                const formData = new FormData();
                formData.append('_token', window.csrfToken);
                Object.keys(data).forEach(key => formData.append(key, data[key]));
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                return response.json();
            },
            get: async (url) => {
                const response = await fetch(url);
                return response.json();
            }
        };
    </script>
</body>
</html>

