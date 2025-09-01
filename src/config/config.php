<?php

define('APP_NAME', 'Camagru');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8080');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');

define('UPLOAD_PATH', __DIR__ . '/../../uploads/');
define('UPLOAD_MAX_SIZE', $_ENV['UPLOAD_MAX_SIZE'] ?? 5242880);
define('ALLOWED_EXTENSIONS', explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,gif'));
define('MAX_IMAGE_WIDTH', $_ENV['MAX_IMAGE_WIDTH'] ?? 1920);
define('MAX_IMAGE_HEIGHT', $_ENV['MAX_IMAGE_HEIGHT'] ?? 1080);

define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? 'localhost');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? 587);
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? '');
define('MAIL_FROM', $_ENV['MAIL_FROM'] ?? 'noreply@camagru.local');

define('SESSION_LIFETIME', 3600);
define('PASSWORD_MIN_LENGTH', 8);
define('IMAGES_PER_PAGE', 5);
