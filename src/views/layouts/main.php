<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Camagru' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm border-b">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">Camagru</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/gallery" class="text-gray-700 hover:text-gray-900">Gallery</a>
                    <a href="/camera" class="text-gray-700 hover:text-gray-900">Camera</a>
                    <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-1">
        <?= $content ?>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 Camagru. School 42.</p>
        </div>
    </footer>
</body>
</html>
