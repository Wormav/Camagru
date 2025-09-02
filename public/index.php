<?php

require_once '../src/bootstrap.php';

// Si c'est une requête pour un fichier statique dans uploads, ne pas router
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

if (strpos($path, '/uploads/') === 0) {
    // Tenter de servir le fichier directement
    $filePath = __DIR__ . $path;
    if (file_exists($filePath) && is_file($filePath)) {
        // Déterminer le type MIME
        $mimeType = mime_content_type($filePath);
        if ($mimeType === false) {
            // Fallback pour les types courants
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'gif':
                    $mimeType = 'image/gif';
                    break;
                default:
                    $mimeType = 'application/octet-stream';
            }
        }
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        echo '404 - File not found';
        exit;
    }
}

$router = new Router();

$router->get('/', 'GalleryController@index');
$router->get('/gallery', 'GalleryController@index');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/verify', 'AuthController@verify');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password', 'AuthController@resetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');
$router->get('/camera', 'CameraController@index');
$router->post('/camera/capture', 'CameraController@capture');
$router->post('/camera/delete', 'CameraController@delete');
$router->get('/profile', 'ProfileController@index');
$router->post('/profile/update', 'ProfileController@update');
$router->post('/profile/upload-picture', 'ProfileController@uploadProfilePicture');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
