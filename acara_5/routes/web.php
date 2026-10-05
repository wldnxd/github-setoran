<?php
// routes/web.php
// Daftar routing sederhana: [METHOD][URI] => [Controller, method]

return [
    'GET' => [
        '/'                => ['HomeController', 'index'],
        '/mahasiswa'       => ['MahasiswaController', 'index'],
        '/mahasiswa/create'=> ['MahasiswaController', 'create'],
    ],
    'POST' => [
        '/mahasiswa' => ['MahasiswaController', 'store'],
    ],
];
