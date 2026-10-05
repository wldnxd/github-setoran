<?php
namespace App\Controllers;

use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use PDOException;

class MahasiswaController
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $mahasiswaList = (new MahasiswaModel())->all($search);
        $content = __DIR__ . '/../Views/mahasiswa/index.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function create(): void
    {
        $mahasiswa = $_SESSION['old_mahasiswa'] ?? [];
        unset($_SESSION['old_mahasiswa']);
        $prodiList = (new ProdiModel())->all();
        $isEditing = false;
        $content = __DIR__ . '/../Views/mahasiswa/create.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function store(): void
    {
        $data = $this->formData();
        if ($data === null) {
            $_SESSION['old_mahasiswa'] = $this->submittedData();
            header('Location: ' . BASE_URL . '/mahasiswa/create');
            exit;
        }

        try {
            (new MahasiswaModel())->create($data);
            $_SESSION['flash_success'] = 'Data mahasiswa berhasil ditambahkan.';
        } catch (PDOException $exception) {
            $_SESSION['old_mahasiswa'] = $data;
            $_SESSION['flash_error'] = $this->databaseErrorMessage($exception);
            header('Location: ' . BASE_URL . '/mahasiswa/create');
            exit;
        }

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function show(int $id): void
    {
        $mhs = (new MahasiswaModel())->findWithProdi($id);
        if ($mhs === null) {
            $this->notFound();
            return;
        }

        $content = __DIR__ . '/../Views/mahasiswa/show.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }

    public function edit(int $id): void
    {
        $mahasiswa = (new MahasiswaModel())->find($id);
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
            (new MahasiswaModel())->update($id, $data);
        }

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function delete(int $id): void
    {
        (new MahasiswaModel())->delete($id);
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    private function formData(): ?array
    {
        $data = $this->submittedData();
        $errors = [];

        if ($data['nim'] === '') {
            $errors[] = 'NIM wajib diisi';
        }
        if ($data['nama'] === '') {
            $errors[] = 'nama wajib diisi';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'email harus valid';
        }
        if ($data['prodi_id'] < 1) {
            $errors[] = 'program studi wajib dipilih';
        }
        if ($data['angkatan'] < 1901 || $data['angkatan'] > 2155) {
            $errors[] = 'angkatan harus antara 1901 dan 2155';
        }
        if (!in_array($data['status'], ['aktif', 'cuti', 'lulus'], true)) {
            $errors[] = 'status tidak valid';
        }

        if ($errors !== []) {
            $_SESSION['flash_error'] = 'Data belum disimpan: ' . implode(', ', $errors) . '.';
            return null;
        }

        return $data;
    }

    private function submittedData(): array
    {
        $value = static fn (string $key): string => is_scalar($_POST[$key] ?? null)
            ? trim((string) $_POST[$key])
            : '';

        return [
            'nim' => $value('nim'),
            'nama' => $value('nama'),
            'email' => $value('email'),
            'prodi_id' => (int) $value('prodi_id'),
            'angkatan' => (int) $value('angkatan'),
            'status' => $value('status'),
        ];
    }

    private function databaseErrorMessage(PDOException $exception): string
    {
        if ($exception->getCode() === '23000') {
            return 'NIM sudah digunakan atau program studi yang dipilih tidak valid.';
        }

        return 'Data mahasiswa gagal disimpan. Periksa database lalu coba lagi.';
    }

    private function notFound(): void
    {
        http_response_code(404);
        $content = __DIR__ . '/../Views/errors/404.php';
        require __DIR__ . '/../Views/layouts/main.php';
    }
}
