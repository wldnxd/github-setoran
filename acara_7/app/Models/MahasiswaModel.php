<?php
namespace App\Models;

use App\Core\Model;

class MahasiswaModel extends Model
{
    public function all(): array
    {
        $sql = 'SELECT mahasiswa.nim, mahasiswa.nama, prodi.nama AS prodi
                FROM mahasiswa
                LEFT JOIN prodi ON prodi.id = mahasiswa.prodi_id
                ORDER BY mahasiswa.id';

        return $this->db->query($sql)->fetchAll();
    }
}