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

                <div class="hidden md:flex items-center space-x-4">
                    <a href="/gallery" class="text-gray-700 hover:text-gray-900">Gallery</a>
                    <a href="/camera" class="text-gray-700 hover:text-gray-900">Camera</a>
                    <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-gray-900 focus:outline-none focus:text-gray-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="md:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200">
                    <a href="/gallery" class="block px-3 py-2 text-gray-700 hover:text-gray-900">Gallery</a>
                    <a href="/camera" class="block px-3 py-2 text-gray-700 hover:text-gray-900">Camera</a>
                    <a href="/login" class="block px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Login</a>
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

    <script src="/assets/js/mobile-menu.js"></script>
</body>
</html>
