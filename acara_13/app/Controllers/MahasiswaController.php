<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\ProdiRepository;
use App\Repositories\MahasiswaRepository;
use App\Services\MahasiswaService;

class MahasiswaController extends BaseController
{
    public function __construct(
        private MahasiswaRepository $mahasiswaRepository,
        private ProdiRepository $prodiRepository,
        private MahasiswaService $mahasiswaService
    ) {
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
        $prodiList = $this->prodiRepository->all();
        $isEditing = false;
        $this->view('mahasiswa/create', compact('mahasiswa', 'prodiList', 'isEditing'));
    }

    public function store(): void
    {
        $this->setFlash($this->mahasiswaService->create($_POST));
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

        $prodiList = $this->prodiRepository->all();
        $isEditing = true;
        $this->view('mahasiswa/create', compact('mahasiswa', 'prodiList', 'isEditing'));
    }

    public function update(int $id): void
    {
        $this->setFlash($this->mahasiswaService->update($id, $_POST), true);
        $this->redirect('/mahasiswa');
    }

    public function delete(int $id): void
    {
        $this->mahasiswaRepository->delete($id);
        $this->redirect('/mahasiswa');
    }

    private function setFlash(string $result, bool $isUpdate = false): void
    {
        if ($result === 'success') {
            $_SESSION['flash_success'] = $isUpdate
                ? 'Data berhasil diubah.'
                : 'Data berhasil ditambahkan.';
            return;
        }

        $_SESSION['flash_error'] = $result === 'duplicate'
            ? 'NIM sudah terdaftar.'
            : 'Data gagal disimpan.';
    }

    private function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404');
    }
}
