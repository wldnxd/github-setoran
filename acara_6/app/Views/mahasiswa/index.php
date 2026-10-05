<h1 class="mb-4">Daftar Mahasiswa</h1>
<a href="<?= htmlspecialchars(BASE_URL) ?>/mahasiswa/create" class="btn btn-primary mb-3">Tambah Mahasiswa</a>
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
    <?php foreach ($mahasiswaList as $i => $mhs): ?>
    <tr>
      <td><?= htmlspecialchars($mhs->getNim()) ?></td>
      <td><?= htmlspecialchars($mhs->getNama()) ?></td>
      <td><?= htmlspecialchars($mhs->getProdi()) ?></td>
      <td><?= htmlspecialchars($mhs->getAngkatan()) ?></td>
      <td>
        <a href="<?= htmlspecialchars(BASE_URL) ?>/mahasiswa/<?= $i + 1 ?>" class="btn btn-sm btn-info">Detail</a>
        <a href="#" class="btn btn-sm btn-warning">Edit</a>
        <a href="#" class="btn btn-sm btn-danger">Hapus</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="text-muted small mt-2">
  Tombol "Detail" menguji routing dengan parameter URL: <code>/mahasiswa/{id}</code>.
</p>
