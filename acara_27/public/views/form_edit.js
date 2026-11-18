const BASE_URL = 'http://localhost/acara_27/public';
const formEditMahasiswa = document.querySelector('#form-edit-mahasiswa');
const pesanForm = document.querySelector('#pesan-form');
const tombolSimpan = document.querySelector('#simpan-perubahan');
const fotoSaatIniWrap = document.querySelector('#foto-saat-ini-wrap');
const fotoSaatIni = document.querySelector('#foto-saat-ini');
const token = localStorage.getItem('token');
const id = new URLSearchParams(window.location.search).get('id');

if (!token) {
    window.location.href = 'login.html';
} else if (localStorage.getItem('role') !== 'admin') {
    window.location.href = 'dashboard.html';
} else if (!id) {
    pesanForm.textContent = 'ID mahasiswa tidak ditemukan';
} else {
    muatDataUntukEdit(id);

    formEditMahasiswa.addEventListener('submit', async (event) => {
        event.preventDefault();
        await simpanPerubahan(id);
    });
}

async function muatDataUntukEdit(id) {
    try {
        const res = await fetch(`${BASE_URL}/mahasiswa?id=${encodeURIComponent(id)}`);
        const hasil = await res.json();

        if (!res.ok || !hasil.success || !hasil.data) {
            throw new Error(hasil.message || 'Data mahasiswa gagal dimuat');
        }

        formEditMahasiswa.elements.nim.value = hasil.data.nim;
        formEditMahasiswa.elements.nama.value = hasil.data.nama;
        formEditMahasiswa.elements.prodi.value = hasil.data.prodi;
        if (typeof hasil.data.foto === 'string' && hasil.data.foto.startsWith('uploads/')) {
            fotoSaatIni.src = `${BASE_URL}/${encodeURI(hasil.data.foto)}`;
            fotoSaatIniWrap.hidden = false;
        }
        tombolSimpan.disabled = false;
    } catch (error) {
        pesanForm.textContent = error.message;
    }
}

async function simpanPerubahan(id) {
    const formData = new FormData();
    formData.append('nim', formEditMahasiswa.elements.nim.value);
    formData.append('nama', formEditMahasiswa.elements.nama.value);
    formData.append('prodi', formEditMahasiswa.elements.prodi.value);
    const fotoBaru = formEditMahasiswa.elements.foto.files[0];
    if (fotoBaru) {
        formData.append('foto', fotoBaru);
    }
    formData.append('_method', 'PUT');
    pesanForm.textContent = '';

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa?id=${encodeURIComponent(id)}`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData,
        });
        const hasil = await response.json();

        if (!response.ok || !hasil.success) {
            throw new Error(hasil.message || 'Perubahan data mahasiswa gagal disimpan');
        }

        pesanForm.textContent = hasil.message;
    } catch (error) {
        pesanForm.textContent = error.message;
    }
}
