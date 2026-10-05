<?php
// public/index.php - Front Controller
// Semua request (lewat .htaccess) masuk ke sini.

// Mulai session (harus dipanggil sebelum output apa pun) - dipakai untuk Auth
session_start();

// Autoload sederhana untuk class di dalam namespace App\ (tanpa Composer)
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// Konfigurasi & routing
$config = require __DIR__ . '/../config/app.php';
$routes = require __DIR__ . '/../routes/web.php';

define('BASE_URL', $config['base_url']);

// 1. Ambil URL yang diminta (tanpa query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Hilangkan base path karena project ada di subfolder Laragon (/acara_6/public)
if (str_starts_with($uri, $config['base_url'])) {
    $uri = substr($uri, strlen($config['base_url'])) ?: '/';
}
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

// 2. Ambil method (GET/POST)
$method = $_SERVER['REQUEST_METHOD'];

// Jalankan middleware (jika ada) yang terpasang pada route, baru panggil Controller@action
$dispatch = function (array $route, ...$params): void {
    foreach ($route['middleware'] ?? [] as $middlewareClass) {
        (new $middlewareClass())->handle();
    }

    $controllerClass = "App\\Controllers\\{$route['controller']}";
    $controller = new $controllerClass();
    $controller->{$route['action']}(...$params);
};

// 3. Cari di array routes (rute statis)
if (isset($routes[$method][$uri])) {
    $dispatch($routes[$method][$uri]);
    return;
}

// Tugas Mandiri (Acara 5): dukungan parameter URL -> /mahasiswa/5 memanggil show($id)
// Tetap dilindungi AuthMiddleware seperti route /mahasiswa lainnya.
if ($method === 'GET' && preg_match('#^/mahasiswa/(\d+)$#', $uri, $matches)) {
    $dispatch([
        'controller' => 'MahasiswaController',
        'action'     => 'show',
        'middleware' => ['App\\Core\\Middleware\\AuthMiddleware'],
    ], (int) $matches[1]);
    return;
}

// 4. Jika tidak ketemu, tampilkan 404
http_response_code(404);
$content = __DIR__ . '/../app/Views/errors/404.php';
require __DIR__ . '/../app/Views/layouts/main.php';
