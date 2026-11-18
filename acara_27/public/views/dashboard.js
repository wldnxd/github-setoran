if (!localStorage.getItem('token')) {
    window.location.href = 'login.html';
} else {
    const tombolTambah = document.querySelector('#tambah-mahasiswa');
    const infoAkses = document.querySelector('#info-akses');

    document.querySelector('#logout').addEventListener('click', () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        window.location.href = 'login.html';
    });

    if (localStorage.getItem('role') !== 'admin') {
        tombolTambah.remove();
        infoAkses.textContent = 'Akun Anda dapat melihat data mahasiswa. Fitur tambah, ubah, dan hapus hanya untuk admin.';
    } else {
        tombolTambah.addEventListener('click', () => {
            window.location.href = 'form_tambah.html';
        });
    }

    const BASE_URL = 'http://localhost/acara_27/public';
    const totalMahasiswa = document.querySelector('#total-mahasiswa');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    const pesanData = document.querySelector('#pesan-data');

    async function muatDataMahasiswa() {
        try {
            const response = await fetch(`${BASE_URL}/mahasiswa`);
            const hasil = await response.json();

            if (!response.ok || !hasil.success || !Array.isArray(hasil.data)) {
                throw new Error(hasil.message || 'Data mahasiswa gagal dimuat');
            }

            totalMahasiswa.textContent = hasil.data.length;
            if (hasil.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4">Belum ada data</td></tr>';
                return;
            }

            tbody.replaceChildren();
            hasil.data.forEach((mahasiswa) => {
                const row = document.createElement('tr');
                [mahasiswa.nim, mahasiswa.nama, mahasiswa.prodi].forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = value;
                    row.appendChild(cell);
                });

                const actionCell = document.createElement('td');
                if (localStorage.getItem('role') === 'admin') {
                    const editButton = document.createElement('button');
                    editButton.type = 'button';
                    editButton.textContent = 'Ubah';
                    editButton.addEventListener('click', () => {
                        window.location.href = `form_edit.html?id=${encodeURIComponent(mahasiswa.id)}`;
                    });
                    actionCell.appendChild(editButton);

                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.textContent = 'Hapus';
                    deleteButton.addEventListener('click', () => hapusData(mahasiswa.id));
                    actionCell.appendChild(deleteButton);
                }
                row.appendChild(actionCell);
                tbody.appendChild(row);
            });
        } catch (error) {
            console.error('Gagal memuat data mahasiswa:', error);
            totalMahasiswa.textContent = 'Gagal dimuat';
            tbody.innerHTML = '<tr><td colspan="4">Data gagal dimuat</td></tr>';
            pesanData.textContent = error.message;
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

            pesanData.textContent = hasil.message;
            await muatDataMahasiswa();
        } catch (error) {
            pesanData.textContent = error.message;
        }
    }

    muatDataMahasiswa();
}
