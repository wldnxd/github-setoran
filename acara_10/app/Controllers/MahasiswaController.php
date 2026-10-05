<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ProdiModel;
use App\Repositories\MahasiswaRepository;

class MahasiswaController extends BaseController
{
    public function __construct(private MahasiswaRepository $mahasiswaRepository)
    {
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $mahasiswaList = $this->mahasiswaRepository->all($search);
        $this->view('mahasiswa/index', compact('search', 'mahasiswaList'));
    }

    public function create(): void
    {
        $mahasiswa = [];
        $prodiList = (new ProdiModel())->all();
        $isEditing = false;
        $this->view('mahasiswa/create', compact('mahasiswa', 'prodiList', 'isEditing'));
    }

    public function store(): void
    {
        $data = $this->formData();
        if ($data === null) {
            $this->redirect('/mahasiswa/create');
        }

        $this->mahasiswaRepository->create($data);
        $this->redirect('/mahasiswa');
    }

    public function show(int $id): void
    {
        $mhs = $this->mahasiswaRepository->findWithProdi($id);
        if ($mhs === null) {
            $this->notFound();
            return;
        }

        $this->view('mahasiswa/show', compact('mhs'));
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
        $this->view('mahasiswa/create', compact('mahasiswa', 'prodiList', 'isEditing'));
    }

    public function update(int $id): void
    {
        $data = $this->formData();
        if ($data !== null) {
            $this->mahasiswaRepository->update($id, $data);
        }

        $this->redirect('/mahasiswa');
    }

    public function delete(int $id): void
    {
        $this->mahasiswaRepository->delete($id);
        $this->redirect('/mahasiswa');
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
        $this->view('errors/404');
    }
}
