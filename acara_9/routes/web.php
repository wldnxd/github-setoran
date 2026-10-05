<?php
// routes/web.php
// Daftar routing: [METHOD][URI] => [
//     'controller'  => nama Controller,
//     'action'      => nama method,
//     'middleware'  => daftar middleware (opsional) yang dijalankan sebelum action,
// ]

$authMiddleware = ['App\\Core\\Middleware\\AuthMiddleware'];

return [
    'GET' => [
        '/' => [
            'controller' => 'HomeController',
            'action'     => 'index',
        ],
        '/login' => [
            'controller' => 'AuthController',
            'action'     => 'loginForm',
        ],
        '/logout' => [
            'controller' => 'AuthController',
            'action'     => 'logout',
        ],
        '/dashboard' => [
            'controller' => 'DashboardController',
            'action'     => 'index',
            'middleware' => $authMiddleware,
        ],
        '/mahasiswa' => [
            'controller' => 'MahasiswaController',
            'action'     => 'index',
            'middleware' => $authMiddleware,
        ],
        '/mahasiswa/create' => [
            'controller' => 'MahasiswaController',
            'action'     => 'create',
            'middleware' => $authMiddleware,
        ],
        '/prodi' => [
            'controller' => 'ProdiController',
            'action'     => 'index',
            'middleware' => $authMiddleware,
        ],
        '/prodi/create' => [
            'controller' => 'ProdiController',
            'action'     => 'create',
            'middleware' => $authMiddleware,
        ],
        '/matakuliah' => [
            'controller' => 'MatakuliahController',
            'action'     => 'index',
            'middleware' => $authMiddleware,
        ],
        '/matakuliah/create' => [
            'controller' => 'MatakuliahController',
            'action'     => 'create',
            'middleware' => $authMiddleware,
        ],
    ],
    'POST' => [
        '/login' => [
            'controller' => 'AuthController',
            'action'     => 'login',
        ],
        '/mahasiswa/create' => [
            'controller' => 'MahasiswaController',
            'action'     => 'store',
            'middleware' => $authMiddleware,
        ],
        '/mahasiswa' => [
            'controller' => 'MahasiswaController',
            'action'     => 'store',
            'middleware' => $authMiddleware,
        ],
        '/prodi' => [
            'controller' => 'ProdiController',
            'action'     => 'store',
            'middleware' => $authMiddleware,
        ],
        '/matakuliah' => [
            'controller' => 'MatakuliahController',
            'action'     => 'store',
            'middleware' => $authMiddleware,
        ],
    ],
];
