const BASE_URL = 'http://localhost/acara_27/public';
const aksiAdmin = document.querySelector('#aksi-admin');
const pesanForm = document.querySelector('#pesan-form');
const isAdmin = localStorage.getItem('role') === 'admin';

if (!localStorage.getItem('token')) {
 window.location.href = 'login.html';
} else {
 if (!isAdmin) {
  aksiAdmin.remove();
 }
 document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
}

async function muatDataMahasiswa() {
 const tbody = document.querySelector('#tabel-mahasiswa tbody');
 tbody.innerHTML = '<tr><td colspan="4">Memuat data...</td></tr>';

 try {
  const response = await fetch(`${BASE_URL}/mahasiswa`);
  const hasil = await response.json();

  if (!response.ok || !hasil.success || !Array.isArray(hasil.data)) {
   throw new Error(hasil.message || 'Data mahasiswa gagal dimuat');
  }

  if (hasil.data.length === 0) {
   tbody.innerHTML = '<tr><td colspan="4">Belum ada data</td></tr>';
   return;
  }

  tbody.replaceChildren();
  hasil.data.forEach((mhs) => {
   const row = document.createElement('tr');
   [mhs.nim, mhs.nama, mhs.prodi].forEach((value) => {
    const cell = document.createElement('td');
    cell.textContent = value;
    row.appendChild(cell);
   });

   const actionCell = document.createElement('td');
   if (isAdmin) {
    const editButton = document.createElement('button');
    editButton.type = 'button';
    editButton.textContent = 'Ubah';
    editButton.addEventListener('click', () => {
     window.location.href = `form_edit.html?id=${encodeURIComponent(mhs.id)}`;
    });
    actionCell.appendChild(editButton);

    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.textContent = 'Hapus';
    deleteButton.addEventListener('click', () => hapusData(mhs.id));
    actionCell.appendChild(deleteButton);
   }
   row.appendChild(actionCell);
   tbody.appendChild(row);
  });
 } catch (error) {
  pesanForm.textContent = error.message;
 }
}

async function hapusData(id) {
 if (!confirm('Hapus data ini?')) return;

 try {
  const response = await fetch(`${BASE_URL}/mahasiswa?id=${encodeURIComponent(id)}`, {
   method: 'DELETE',
   headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
  });
  const hasil = await response.json();

  if (!response.ok || !hasil.success) {
   throw new Error(hasil.message || 'Data mahasiswa gagal dihapus');
  }

  pesanForm.textContent = hasil.message;
  await muatDataMahasiswa();
 } catch (error) {
  pesanForm.textContent = error.message;
 }
}
