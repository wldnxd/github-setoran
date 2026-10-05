<h1 class="mb-4">Daftar Mahasiswa</h1>
<a href="../app/Views/mahasiswa/create.php" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>NIM</th>
      <th>Nama</th>
      <th>Prodi</th>
      <th>Angkatan</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($mahasiswaList as $mhs): ?>
    <tr>
      <td><?= htmlspecialchars($mhs->getNim()) ?></td>
      <td><?= htmlspecialchars($mhs->getNama()) ?></td>
      <td><?= htmlspecialchars($mhs->getProdi()) ?></td>
      <td><?= htmlspecialchars($mhs->getAngkatan()) ?></td>
      <td>
        <a href="#" class="btn btn-sm btn-warning">Edit</a>
        <a href="#" class="btn btn-sm btn-danger">Hapus</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
