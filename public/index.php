<?php

require_once '../src/bootstrap.php';

$router = new Router();

$router->get('/', 'GalleryController@index');
$router->get('/gallery', 'GalleryController@index');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/camera', 'CameraController@index');
$router->post('/camera/capture', 'CameraController@capture');
$router->get('/profile', 'UserController@profile');
$router->post('/profile/update', 'UserController@update');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
