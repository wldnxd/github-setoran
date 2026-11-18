<?php
class MahasiswaController {
    private Mahasiswa $model;

    public function __construct() {
        $this->model = new Mahasiswa();
    }

    public function index(): void {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $data = $this->model->find((int) $id);
            if (!$data) Response::json(404, false, 'Data tidak ditemukan');
            Response::json(200, true, 'Data berhasil diambil', $data);
        }
        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }

    public function store(): void {
        Auth::requireAdmin();
        $data = $_POST;
        if (!$data) {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        if (!is_array($data)) {
            $data = [];
        }

        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }

        if (strlen($data['nim']) !== 9) {
            Response::json(422, false, 'Format NIM tidak valid, harus 9 digit');
        }

        $this->processFotoUpload($data);
        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan', ['id' => $id]);
    }

    public function update($id): void {
        Auth::requireAdmin();
        $data = $_POST;
        if (!$data) {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        if (!is_array($data)) {
            $data = [];
        }

        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }

        if (strlen($data['nim']) !== 9) {
            Response::json(422, false, 'Format NIM tidak valid, harus 9 digit');
        }

        $id = (int) $id;
        $mahasiswaLama = $this->model->find($id);
        if (!$mahasiswaLama) {
            Response::json(404, false, 'Data tidak ditemukan');
        }

        $fotoBaruDiunggah = isset($_FILES['foto'])
            && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
        $this->processFotoUpload($data);
        $this->model->update($id, $data);

        if ($fotoBaruDiunggah) {
            $this->hapusFotoLama($mahasiswaLama['foto'] ?? null);
        }

        Response::json(200, true, 'Data berhasil diperbarui');
    }

    private function processFotoUpload(array &$data): void {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['foto'];
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                Response::json(422, false, 'Gagal mengunggah berkas foto');
            }

            $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $tipeGambar = exif_imagetype($file['tmp_name']);
            $ekstensiValid = ['jpg' => IMAGETYPE_JPEG, 'jpeg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG];
            if (!isset($ekstensiValid[$ekstensi]) || $tipeGambar !== $ekstensiValid[$ekstensi]) {
                Response::json(422, false, 'Berkas harus berupa gambar JPG/PNG yang valid');
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                Response::json(422, false, 'Ukuran berkas maksimal 2MB');
            }

            $namaBaru = uniqid('foto_', true) . '.' . $ekstensi;
            $direktoriUpload = __DIR__ . '/../../public/uploads';
            if (!move_uploaded_file($file['tmp_name'], $direktoriUpload . '/' . $namaBaru)) {
                Response::json(500, false, 'Foto gagal disimpan');
            }
            $data['foto'] = 'uploads/' . $namaBaru;
        }
    }

    private function hapusFotoLama(?string $foto): void {
        $prefix = 'uploads/';
        if ($foto === null || strpos($foto, $prefix) !== 0) {
            return;
        }

        $namaFile = substr($foto, strlen($prefix));
        if ($namaFile === '' || basename($namaFile) !== $namaFile) {
            error_log('Foto mahasiswa lama tidak dihapus: path tidak valid.');
            return;
        }

        $direktoriUpload = realpath(__DIR__ . '/../../public/uploads');
        $pathFoto = realpath(__DIR__ . '/../../public/uploads/' . $namaFile);
        if ($direktoriUpload === false || $pathFoto === false || !is_file($pathFoto)) {
            return;
        }

        if (strpos($pathFoto, $direktoriUpload . DIRECTORY_SEPARATOR) !== 0) {
            error_log('Foto mahasiswa lama tidak dihapus: path berada di luar direktori upload.');
            return;
        }

        if (!unlink($pathFoto)) {
            error_log('Foto mahasiswa lama gagal dihapus: ' . $pathFoto);
        }
    }

    public function destroy($id): void {
        Auth::requireAdmin();
        $this->model->delete((int) $id);
        Response::json(200, true, 'Data berhasil dihapus');
    }
}