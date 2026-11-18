<?php
class Mahasiswa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query('SELECT * FROM mahasiswa ORDER BY id DESC')->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): string {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO mahasiswa (nim, nama, prodi, foto) VALUES (:nim, :nama, :prodi, :foto)'
            );
            $stmt->execute([
                ':nim' => $data['nim'],
                ':nama' => $data['nama'],
                ':prodi' => $data['prodi'],
                ':foto' => $data['foto'] ?? null,
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }
    }

    public function update(int $id, array $data): void {
        $query = 'UPDATE mahasiswa SET nim = :nim, nama = :nama, prodi = :prodi';
        $params = [':nim' => $data['nim'], ':nama' => $data['nama'], ':prodi' => $data['prodi']];
        if (array_key_exists('foto', $data)) {
            $query .= ', foto = :foto';
            $params[':foto'] = $data['foto'];
        }
        $query .= ' WHERE id = :id';
        $params[':id'] = $id;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}