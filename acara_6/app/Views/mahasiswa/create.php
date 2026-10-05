<h1 class="mb-4">Tambah Mahasiswa</h1>

<form action="<?= htmlspecialchars(BASE_URL) ?>/mahasiswa" method="POST">
  <div class="mb-3">
    <label for="nim" class="form-label">NIM</label>
    <input type="text" class="form-control" id="nim" name="nim" required>
  </div>
  <div class="mb-3">
    <label for="nama" class="form-label">Nama</label>
    <input type="text" class="form-control" id="nama" name="nama" required>
  </div>
  <div class="mb-3">
    <label for="prodi" class="form-label">Prodi</label>
    <input type="text" class="form-control" id="prodi" name="prodi" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="<?= htmlspecialchars(BASE_URL) ?>/mahasiswa" class="btn btn-secondary">Batal</a>
</form>
