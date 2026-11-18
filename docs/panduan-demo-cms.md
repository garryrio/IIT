# Panduan Demo CMS — Marga Mulya Hub

Panduan singkat untuk juri, tim, atau perangkat desa (juga dapat dipakai saat program VIP hari ke-2).

## Masuk

1. Buka `/admin/login` (atau tombol **Masuk CMS** di footer).
2. Masuk dengan akun admin dari `ADMIN_EMAIL`/`ADMIN_PASSWORD` atau yang dibuat lewat `php artisan admin:create`.

## Skenario demo (± 5 menit)

| # | Langkah | Hasil yang ditunjukkan |
|---|---|---|
| 1 | **Dashboard** | Jumlah konten, pengumuman aktif, agenda mendatang, kontribusi menunggu, aktivitas terbaru |
| 2 | **Pengaturan situs** → ubah *Judul hero beranda* → Simpan → buka beranda | Perubahan CMS langsung tampil di website publik |
| 3 | **Berita → Tambah Berita** → isi judul, ringkasan, isi (editor), unggah gambar, status *Terbit* → Simpan | Gambar dipratinjau & dikompres otomatis; berita tampil di `/informasi` dan beranda |
| 4 | Di daftar berita klik **Jadikan draf** | Berita hilang dari website publik (404) |
| 5 | **Pengumuman → Tambah** dengan prioritas *Sangat penting* dan tanggal berakhir | Muncul di beranda & banner prioritas; setelah tanggal berakhir otomatis menjadi arsip |
| 6 | **Agenda → Tambah** dengan tanggal, jam, lokasi, dan klik titik di peta | Muncul di kalender agenda, lapisan "Agenda" pada peta, dan bisa disimpan ke Google Calendar/.ics |
| 7 | **Data & statistik → Tambah** (kategori sama, tahun baru) | Grafik & filter tahun di `/data-desa` ikut berubah |
| 8 | **Potensi desa → Ubah** → geser penanda di peta, centang *Unggulan* | Lokasi & bento beranda berubah |
| 9 | **Kontribusi warga** → buka kiriman *Menunggu* → *Setujui* + catatan → **Jadikan draf berita** → terbitkan | Alur moderasi Citizen-to-Village; data pengirim tidak ikut terbit |
| 10 | **Pesan kontak** → buka pesan | Status otomatis *Sudah dibaca*, tombol balas via email/WhatsApp |
| 11 | **Analisis penghasilan** → filter RW, *Belum menerima* → **Ekspor CSV** | Daftar prioritas penyaluran bantuan untuk musyawarah desa |
| 12 | **Kelas penghasilan** → ubah ambang batas | Klasifikasi & grafik langsung menyesuaikan |
| 13 | Hapus konten uji → dialog konfirmasi | Konten & gambarnya terhapus |

## Tips pengelolaan

- Gunakan status **Draf** untuk menyiapkan konten; isi *Waktu terbit* di masa depan untuk penjadwalan.
- Selalu isi **teks alternatif** foto galeri agar ramah pembaca layar.
- Gunakan **kode KK internal** — jangan memasukkan NIK atau nomor KK asli.
- Ukuran gambar maksimal 2 MB (otomatis diperkecil sebelum diunggah).
