<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | ITHM CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-100 min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <div class="text-9xl font-bold text-emerald-600 mb-4">404</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Page Not Found</h1>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <a href="<?= BASE_URL ?? '/' ?>" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-all shadow-lg">
            <i class="fas fa-home"></i>
            Go Home
        </a>
    </div>
</body>
</html>

