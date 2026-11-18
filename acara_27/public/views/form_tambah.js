const BASE_URL = 'http://localhost/acara_27/public';
const formTambahMahasiswa = document.querySelector('#form-tambah-mahasiswa');
const pesanForm = document.querySelector('#pesan-form');
const token = localStorage.getItem('token');

if (!token) {
    window.location.href = 'login.html';
} else {
    if (localStorage.getItem('role') !== 'admin') {
        pesanForm.textContent = 'Akun Anda bukan admin. Hanya admin yang dapat menyimpan data.';
    }

    formTambahMahasiswa.addEventListener('submit', async (event) => {
        event.preventDefault();
        pesanForm.textContent = '';

        try {
            const response = await fetch(`${BASE_URL}/mahasiswa`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: new FormData(formTambahMahasiswa),
            });
            const hasil = await response.json();

            if (!response.ok || !hasil.success) {
                throw new Error(hasil.message || 'Data mahasiswa gagal ditambahkan');
            }

            pesanForm.textContent = hasil.message;
            formTambahMahasiswa.reset();
        } catch (error) {
            pesanForm.textContent = error.message;
        }
    });
}
