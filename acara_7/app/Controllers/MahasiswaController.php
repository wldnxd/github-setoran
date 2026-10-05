<?php
namespace App\Controllers;

use App\Models\Mahasiswa;
use App\Models\MahasiswaModel;

class MahasiswaController
{
    private function getData(): array
    {
        $rows = (new MahasiswaModel())->all();

        return array_map(
            static fn (array $row): Mahasiswa => new Mahasiswa(
                $row['nim'],
                $row['nama'],
                $row['prodi'] ?? ''
            ),
            $rows
        );
    }

    // GET /mahasiswa
    public function index(): void
    {
        $mahasiswaList = $this->getData();
        $content = __DIR__ . '/../Views/mahasiswa/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    // GET /mahasiswa/create
    public function create(): void
    {
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    // POST /mahasiswa
    public function store(): void
    {
        $nim   = trim($_POST['nim'] ?? '');
        $nama  = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');

        // Validasi sederhana
        if ($nim === '' || $nama === '' || $prodi === '') {
            header('Location: ' . BASE_URL . '/mahasiswa/create');
            exit; // penting! stop eksekusi setelah header Location
        }

        // Catatan: penyimpanan permanen ke database baru diimplementasikan
        // pada Acara 7-8. Untuk saat ini cukup redirect kembali ke daftar.
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    // GET /mahasiswa/{id}  (Tugas Mandiri: dukungan parameter URL sederhana)
    public function show(int $id): void
    {
        $mahasiswaList = $this->getData();
        $index = $id - 1; // id sederhana berdasarkan urutan data

        if (!isset($mahasiswaList[$index])) {
            http_response_code(404);
            $content = __DIR__ . '/../Views/errors/404.php';
            require __DIR__ . '/../Views/layouts/main.php';
            return;
        }

        $mhs = $mahasiswaList[$index];
        $content = __DIR__ . '/../Views/mahasiswa/show.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}
