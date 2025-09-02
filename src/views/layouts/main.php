<?php
// Définir les headers de sécurité
SecurityHeaders::setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Camagru' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php
    // Synchroniser la session utilisateur si nécessaire
    if (isset($_SESSION['user_id']) && !isset($_SESSION['profile_picture'])) {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
        }
    }
    ?>
    <header class="bg-white shadow-sm border-b">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">Camagru</a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/gallery" class="text-gray-700 hover:text-gray-900">Gallery</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/camera" class="text-gray-700 hover:text-gray-900">Camera</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Profile Picture -->
                        <div class="relative">
                            <a href="/profile" class="block">
                                <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-300 hover:ring-2 hover:ring-blue-500 transition-all duration-200">
                                    <?php
                                    // Debug temporaire
                                    $sessionPicture = $_SESSION['profile_picture'] ?? 'null';
                                    // echo "<!-- Debug: profile_picture = " . $sessionPicture . " -->";
                                    ?>
                                    <?php if (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture']): ?>
                                        <img class="h-full w-full object-cover"
                                             src="/uploads/profiles/<?= htmlspecialchars($_SESSION['profile_picture']) ?>"
                                             alt="<?= htmlspecialchars($_SESSION['username']) ?>"
                                             title="<?= htmlspecialchars($_SESSION['username']) ?>">
                                    <?php else: ?>
                                        <div class="h-full w-full flex items-center justify-center text-lg">
                                            😊
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <a href="/logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</a>
                    <?php endif; ?>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/camera" class="block px-3 py-2 text-gray-700 hover:text-gray-900">Camera</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Profile in mobile menu -->
                        <a href="/profile" class="flex items-center px-3 py-2 text-gray-700 hover:text-gray-900">
                            <div class="h-6 w-6 rounded-full overflow-hidden bg-gray-300 mr-3">
                                <?php if (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture']): ?>
                                    <img class="h-full w-full object-cover"
                                         src="/uploads/profiles/<?= htmlspecialchars($_SESSION['profile_picture']) ?>"
                                         alt="<?= htmlspecialchars($_SESSION['username']) ?>">
                                <?php else: ?>
                                    <div class="h-full w-full flex items-center justify-center text-sm">
                                        😊
                                    </div>
                                <?php endif; ?>
                            </div>
                            Profile (<?= htmlspecialchars($_SESSION['username']) ?>)
                        </a>
                        <a href="/logout" class="block px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="block px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Login</a>
                    <?php endif; ?>
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
