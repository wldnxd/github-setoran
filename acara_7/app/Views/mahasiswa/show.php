<h1 class="mb-4">Detail Mahasiswa</h1>

<table class="table table-bordered w-auto">
  <tr>
    <th>NIM</th>
    <td><?= htmlspecialchars($mhs->getNim()) ?></td>
  </tr>
  <tr>
    <th>Nama</th>
    <td><?= htmlspecialchars($mhs->getNama()) ?></td>
  </tr>
  <tr>
    <th>Prodi</th>
    <td><?= htmlspecialchars($mhs->getProdi()) ?></td>
  </tr>
  <tr>
    <th>Angkatan</th>
    <td><?= htmlspecialchars($mhs->getAngkatan()) ?></td>
  </tr>
</table>

<a href="<?= htmlspecialchars(BASE_URL) ?>/mahasiswa" class="btn btn-secondary">
  &larr; Kembali ke Daftar
</a>
