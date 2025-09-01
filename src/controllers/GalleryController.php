<?php

class GalleryController extends Controller {

    public function index() {
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-red-500 text-4xl font-bold mb-4">Camagru Gallery - Coming Soon</h1>
        <p class="text-green-600 text-lg mb-2">Docker setup successful!</p>
        <p class="text-blue-600 text-lg">Using Tailwind CSS for styling</p>
    </div>
</body>
</html>';
    }
}
