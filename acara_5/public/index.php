<?php
// public/index.php - Front Controller

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

// Hilangkan base path karena project ada di subfolder Laragon (/acara_5/public)
if (str_starts_with($uri, $config['base_url'])) {
    $uri = substr($uri, strlen($config['base_url'])) ?: '/';
}
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

// 2. Ambil method (GET/POST)
$method = $_SERVER['REQUEST_METHOD'];

// 3. Cari di array routes (rute statis)
if (isset($routes[$method][$uri])) {
    [$controllerName, $action] = $routes[$method][$uri];
    $controllerClass = "App\\Controllers\\{$controllerName}";
    $controller = new $controllerClass();
    $controller->$action();
    return;
}

// Tugas Mandiri: dukungan parameter URL sederhana -> /mahasiswa/5 memanggil show($id)
if ($method === 'GET' && preg_match('#^/mahasiswa/(\d+)$#', $uri, $matches)) {
    $id = (int) $matches[1];
    $controller = new App\Controllers\MahasiswaController();
    $controller->show($id);
    return;
}

// 4. Jika tidak ketemu, tampilkan 404
http_response_code(404);
$content = __DIR__ . '/../app/Views/errors/404.php';
require __DIR__ . '/../app/Views/layouts/main.php';
