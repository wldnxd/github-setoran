const BASE_URL = 'http://localhost/acara_26/public';
const aksiAdmin = document.querySelector('#aksi-admin');
const formMahasiswa = document.querySelector('#form-mahasiswa');
const pesanForm = document.querySelector('#pesan-form');
const isAdmin = localStorage.getItem('role') === 'admin';

if (!isAdmin) {
 aksiAdmin?.remove();
}

async function muatDataMahasiswa() {
 const tbody = document.querySelector('#tabel-mahasiswa tbody');
 tbody.innerHTML = '<tr><td colspan="4">Memuat data...</td></tr>';

 try {
 const response = await fetch(`${BASE_URL}/mahasiswa`);
 const hasil = await response.json();

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
   editButton.addEventListener('click', () => bukaFormUbah(mhs));
   actionCell.appendChild(editButton);
  }
  row.appendChild(actionCell);
  tbody.appendChild(row);
 });
 } catch (error) {
 console.error('Gagal memuat data:', error);
 }
}

function bukaFormUbah(mahasiswa) {
 formMahasiswa.querySelector('#mahasiswa-id').value = mahasiswa.id;
 formMahasiswa.elements.nim.value = mahasiswa.nim;
 formMahasiswa.elements.nama.value = mahasiswa.nama;
 formMahasiswa.elements.prodi.value = mahasiswa.prodi;
 formMahasiswa.elements.foto.value = '';
 document.querySelector('#simpan-mahasiswa').textContent = 'Simpan Perubahan';
 pesanForm.textContent = '';
 formMahasiswa.hidden = false;
}

aksiAdmin?.querySelector('#tambah-mahasiswa').addEventListener('click', () => {
 formMahasiswa.reset();
 formMahasiswa.querySelector('#mahasiswa-id').value = '';
 document.querySelector('#simpan-mahasiswa').textContent = 'Simpan';
 pesanForm.textContent = '';
 formMahasiswa.hidden = false;
});

document.querySelector('#batal-mahasiswa').addEventListener('click', () => {
 formMahasiswa.reset();
 formMahasiswa.hidden = true;
 pesanForm.textContent = '';
});

formMahasiswa.addEventListener('submit', async (event) => {
 event.preventDefault();
 const data = new FormData();
 data.append('nim', formMahasiswa.elements.nim.value);
 data.append('nama', formMahasiswa.elements.nama.value);
 data.append('prodi', formMahasiswa.elements.prodi.value);

 const foto = formMahasiswa.elements.foto.files[0];
 if (foto) data.append('foto', foto);

 const id = formMahasiswa.querySelector('#mahasiswa-id').value;
 let url = `${BASE_URL}/mahasiswa`;
 if (id) {
  data.append('_method', 'PUT');
  url += `?id=${encodeURIComponent(id)}`;
 }

 try {
  const response = await fetch(url, {
   method: 'POST',
   headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
   body: data,
  });
  const hasil = await response.json();
  if (!response.ok || !hasil.success) {
   throw new Error(hasil.message || 'Data mahasiswa gagal disimpan');
  }

  pesanForm.textContent = hasil.message;
  formMahasiswa.reset();
  formMahasiswa.hidden = true;
  await muatDataMahasiswa();
 } catch (error) {
  pesanForm.textContent = error.message;
 }
});

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);