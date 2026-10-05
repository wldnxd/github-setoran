<?php
namespace App\Controllers;

use App\Models\ProdiModel;
use App\Repositories\MahasiswaRepository;

class MahasiswaController
{
    public function __construct(private MahasiswaRepository $mahasiswaRepository)
    {
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $mahasiswaList = $this->mahasiswaRepository->all($search);
        $content = __DIR__ . '/../Views/mahasiswa/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $mahasiswa = [];
        $prodiList = (new ProdiModel())->all();
        $isEditing = false;
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $data = $this->formData();
        if ($data === null) {
            header('Location: ' . BASE_URL . '/mahasiswa/create');
            exit;
        }

        $this->mahasiswaRepository->create($data);
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function show(int $id): void
    {
        $mhs = $this->mahasiswaRepository->findWithProdi($id);
        if ($mhs === null) {
            $this->notFound();
            return;
        }

        $content = __DIR__ . '/../Views/mahasiswa/show.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(int $id): void
    {
        $mahasiswa = $this->mahasiswaRepository->find($id);
        if ($mahasiswa === null) {
            $this->notFound();
            return;
        }

        $prodiList = (new ProdiModel())->all();
        $isEditing = true;
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function update(int $id): void
    {
        $data = $this->formData();
        if ($data !== null) {
            $this->mahasiswaRepository->update($id, $data);
        }

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function delete(int $id): void
    {
        $this->mahasiswaRepository->delete($id);
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    private function formData(): ?array
    {
        $data = [
            'nim' => trim($_POST['nim'] ?? ''),
            'nama' => trim($_POST['nama'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'prodi_id' => (int) ($_POST['prodi_id'] ?? 0),
            'angkatan' => (int) ($_POST['angkatan'] ?? 0),
            'status' => $_POST['status'] ?? '',
        ];

        if (
            !ctype_digit($data['nim']) || $data['nama'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
            $data['prodi_id'] < 1 || $data['angkatan'] < 1 ||
            !in_array($data['status'], ['aktif', 'cuti', 'lulus'], true)
        ) {
            return null;
        }

        return $data;
    }

    private function notFound(): void
    {
        http_response_code(404);
        $content = __DIR__ . '/../Views/errors/404.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}
