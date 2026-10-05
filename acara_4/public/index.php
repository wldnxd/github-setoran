<?php
require_once __DIR__ . '/../app/Models/Mahasiswa.php';

use App\Models\Mahasiswa;

// Membuat beberapa object Mahasiswa (data sementara)
$mahasiswaList = [
    new Mahasiswa('2401001', 'Budi Santoso', 'Teknik Informatika'),
    new Mahasiswa('2401002', 'Siti Aminah', 'Sistem Informasi'),
    new Mahasiswa('2301003', 'Andi Wijaya', 'Teknik Informatika'),
];

// Kirim data ke View melalui layout
$content = __DIR__ . '/../app/Views/mahasiswa/index.php';
require __DIR__ . '/../app/Views/layouts/main.php';
