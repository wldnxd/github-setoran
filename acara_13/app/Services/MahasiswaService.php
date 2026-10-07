<?php
namespace App\Services;

use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use PDOException;

class MahasiswaService
{
    public function __construct(
        private MahasiswaRepository $mahasiswaRepository,
        private ProdiRepository $prodiRepository
    ) {
    }

    public function create(array $input): string
    {
        $data = $this->validatedData($input);
        if ($data === null) {
            return 'failure';
        }

        if (!$this->prodiRepository->exists($data['prodi_id'])) {
            return 'failure';
        }

        if ($this->mahasiswaRepository->existsByNim($data['nim'])) {
            return 'duplicate';
        }

        try {
            return $this->mahasiswaRepository->create($data) ? 'success' : 'failure';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            return $this->isDuplicateKey($exception) ? 'duplicate' : 'failure';
        }
    }

    public function update(int $id, array $input): string
    {
        $data = $this->validatedData($input);
        if ($data === null) {
            return 'failure';
        }

        if (!$this->prodiRepository->exists($data['prodi_id'])) {
            return 'failure';
        }

        if ($this->mahasiswaRepository->existsByNim($data['nim'], $id)) {
            return 'duplicate';
        }

        try {
            return $this->mahasiswaRepository->update($id, $data) ? 'success' : 'failure';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            return $this->isDuplicateKey($exception) ? 'duplicate' : 'failure';
        }
    }

    private function validatedData(array $input): ?array
    {
        $data = [
            'nim' => trim($input['nim'] ?? ''),
            'nama' => trim($input['nama'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'prodi_id' => filter_var($input['prodi_id'] ?? null, FILTER_VALIDATE_INT) ?: 0,
            'angkatan' => filter_var($input['angkatan'] ?? null, FILTER_VALIDATE_INT) ?: 0,
            'status' => $input['status'] ?? '',
        ];

        if (
            !ctype_digit($data['nim']) || $data['nama'] === '' ||
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
            $data['prodi_id'] < 1 || $data['angkatan'] < 1 ||
            !in_array($data['status'], ['aktif', 'cuti', 'lulus'], true)
        ) {
            return null;
        }

        return $data;
    }

    private function isDuplicateKey(PDOException $exception): bool
    {
        return $exception->getCode() === '23000' &&
            (int) ($exception->errorInfo[1] ?? 0) === 1062;
    }
}
