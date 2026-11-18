# Marga Mulya Hub

> **Informasi Desa, Potensi Lokal, dan Partisipasi Masyarakat dalam Satu Platform.**

Website + CMS + Village Data Hub + Katalog Potensi + Community Platform untuk **Desa Marga Mulya**, dibuat untuk kategori **EcoQuest AI Web Application — IIT Challenge 2026**.

> ⚠️ Seluruh data di aplikasi ini adalah **data contoh (dummy)** yang disusun mengikuti struktur dokumen *Data EcoQuest IIT Challenge 2026*. Gambar berupa ilustrasi SVG orisinal. Ganti dengan data & foto resmi desa melalui CMS.

---

## 1. Gambaran produk

| Pengguna | Yang didapat |
|---|---|
| Warga desa | Pengumuman aktif, agenda + kalender, berita, layanan, fasilitas, dan formulir kontribusi informasi |
| Perangkat desa | CMS lengkap tanpa menyentuh kode: konten, data statistik, potensi, galeri, moderasi kontribusi, analisis kesejahteraan |
| Pelaku UMKM / pengelola potensi | Halaman katalog dengan foto, lokasi di peta, jam operasional, harga, dan tombol WhatsApp |
| Pengunjung umum | Profil desa, Village Data Hub (grafik + tabel), peta interaktif, galeri |

## 2. Fitur

**Halaman publik (9 route halaman — batas lomba maksimal 10):**

| Route | Halaman |
|---|---|
| `/` | Beranda: hero (CMS), statistik singkat, pengumuman aktif berprioritas, bento potensi unggulan, agenda terdekat, **peta berlapis**, berita terbaru, Cerita Marga Mulya, CTA kontribusi |
| `/profil` | Identitas, sejarah, visi & misi, geografis (batas wilayah), pemerintahan (aparat + ringkasan gender/pendidikan dinamis), keamanan, fasilitas + peta |
| `/data-desa` | **Village Data Hub**: kartu statistik, grafik batang/horizontal/donat/garis, filter tahun & kategori, ringkasan teks + tabel untuk setiap grafik, sebaran agregat kelas penghasilan |
| `/potensi` | Katalog + pencarian + filter kategori + **peta interaktif** yang tersinkron dengan filter |
| `/potensi/{slug}` | Detail potensi, kontak WhatsApp, petunjuk arah, peta lokasi |
| `/informasi` | Berita, pengumuman, agenda, layanan: tab jenis, pencarian, filter tanggal & status (aktif/arsip), **tampilan Kalender** |
| `/informasi/{slug}` | Detail; agenda dapat ditambahkan ke **Google Calendar** atau diunduh **.ics** |
| `/galeri` | Grid responsif, filter kategori, lightbox (keyboard ←/→/Esc), lazy loading |
| `/kontak` | Info kontak, peta & navigasi, formulir pesan, formulir **Kontribusi Warga** (foto + lokasi GPS opsional) |

Endpoint non-halaman: `/cari.json` (JSON smart search), `/media/{id}` (gambar), `/informasi/{slug}/kalender.ics`, `/sitemap.xml`, `/robots.txt`.

**Fitur inovasi**
1. **Village Data Hub** — semua statistik & jenis grafik dari database; alternatif teks/tabel yang aksesibel.
2. **Peta interaktif** (Leaflet + OpenStreetMap) — lapisan Potensi / Fasilitas / Agenda, penanda berwarna per kategori, daftar lokasi sebagai fallback bila peta gagal dimuat, pemilih koordinat di CMS.
3. **Smart Search / Advanced Search** — `Ctrl + K` atau `/` dari halaman mana pun; multi-kata, filter jenis konten, rentang tanggal, urut relevansi/terbaru, hasil dikelompokkan per jenis, navigasi keyboard.
4. **Kalender agenda** — tampilan bulanan, daftar agenda bulan ini, ekspor `.ics` & Google Calendar.
5. **Citizen to Village** — kontribusi warga → *pending* → admin setuju/tolak + catatan → jadikan draf berita (tanpa data pribadi pengirim) → terbitkan.
6. **Analisis kelas penghasilan (khusus admin)** — klasifikasi otomatis per kapita, ambang kelas dapat diatur, grafik sebaran & komposisi per RW, cakupan bantuan, **daftar prioritas penyaluran bantuan** dengan skor transparan, deteksi penerima di luar sasaran, ekspor CSV. Publik hanya melihat **agregat**.

**CMS (`/admin`)**: login, dashboard (jumlah konten, pengumuman aktif, agenda mendatang, kontribusi menunggu, aktivitas terbaru), pengaturan situs, profil desa, perangkat desa, fasilitas, data & statistik, berita, pengumuman, agenda, layanan, potensi, galeri, cerita warga, pesan kontak, kontribusi warga, data ekonomi KK, kelas penghasilan, analisis kesejahteraan, logout. Setiap modul: CRUD, status draf/terbit (toggle cepat), validasi, dialog konfirmasi hapus, pesan berhasil/gagal, pratinjau & kompres gambar otomatis, pencarian & filter, timestamp dibuat/diperbarui.

## 3. Tech stack

Mengikuti ketentuan lomba **PHP ≥ 8.2 + MySQL + HTML/CSS/JS**:

| Lapisan | Teknologi |
|---|---|
| Backend | **Laravel 13** (PHP 8.3+; diuji pada PHP 8.5), Blade |
| Database | **MySQL / MariaDB** (diuji pada MariaDB 10.11) |
| Frontend | Tailwind CSS 4, Alpine.js, **framer-motion** (`framer-motion/dom`, tanpa React) |
| Peta | Leaflet + OpenStreetMap |
| Grafik | Chart.js |
| Editor | Trix (rich text) + HTML Purifier (sanitasi) |
| Ikon | Lucide (blade-lucide-icons) |
| Font | Plus Jakarta Sans & Fraunces (SIL OFL, di-host mandiri saat build) |
| Deploy | Vercel + runtime komunitas `vercel-php@0.9.0` |

> Catatan: prompt awal menyebut Next.js + Supabase, tetapi **aturan resmi lomba mewajibkan PHP 8.2+ dan MySQL**, sehingga stack disesuaikan. Komponen 21st.dev (React) dipakai sebagai **referensi pola desain** (editorial collage hero, bento grid, animated gallery) lalu ditulis ulang dengan Blade + Tailwind + framer-motion.

## 4. Struktur folder

```
app/
  Admin/               # Sistem CMS generik: Field, Resource, Registry
    Resources/         # Satu kelas per modul (field, kolom, filter, aturan)
  Http/Controllers/    # Publik: Page, Potential, Information, Contact, Search, Media, Sitemap
    Admin/             # Auth, Dashboard, Resource (CRUD generik), Message, Submission, Income
  Http/Middleware/     # EnsureAdmin, SecurityHeaders
  Models/              # Eloquent model + konstanta kategori
  Support/             # MediaStore, Html (sanitasi), Search, MapData, IncomeAnalytics
database/migrations/   # Skema lengkap + index
database/seeders/      # VillageSeeder (data contoh)
resources/css/app.css  # Design tokens (CSS variables) + komponen
resources/js/          # app.js, admin.js, motion.js, components/*
resources/views/       # public/*, admin/*, components/*, partials/*
scripts/               # generate-illustrations.py (ilustrasi seed)
api/index.php          # Entry point Vercel
docs/                  # Sinopsis, outline presentasi, panduan demo CMS
tests/Feature/         # Uji otomatis acceptance criteria
```

## 5. Instalasi lokal

Prasyarat: PHP 8.3+ (ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`), Composer 2, Node.js 20+, MySQL/MariaDB.

```bash
git clone <repo> marga-mulya-hub && cd marga-mulya-hub
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Buat database `marga_mulya_hub` (utf8mb4), isi `DB_*` di `.env`, lalu isi **`ADMIN_EMAIL` dan `ADMIN_PASSWORD`** (min. 10 karakter).

```bash
php artisan migrate --seed      # migration + data contoh + admin pertama
npm run build                   # atau: npm run dev
php artisan serve               # http://localhost:8000
```

### Environment variables

| Variabel | Keterangan |
|---|---|
| `APP_KEY` | Hasil `php artisan key:generate` (wajib, rahasia) |
| `APP_URL` | URL publik (mis. `https://marga-mulya-hub.vercel.app`) |
| `APP_TIMEZONE`, `APP_LOCALE` | `Asia/Jakarta`, `id` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Koneksi MySQL |
| `MYSQL_ATTR_SSL_CA` | Path CA bila database mewajibkan SSL (Vercel: `/etc/pki/tls/certs/ca-bundle.crt`) |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Admin pertama (dipakai seeder) |
| `MEDIA_DRIVER` | `database` (default, permanen & cocok untuk Vercel) atau `disk` |
| `MEDIA_DISK`, `MEDIA_MAX_KB` | Disk Laravel bila `disk` (mis. `s3`), batas ukuran unggahan (KB) |

Tidak ada secret di repository; `.env` diabaikan Git.

### Membuat admin pertama

- Otomatis lewat seeder bila `ADMIN_EMAIL` & `ADMIN_PASSWORD` terisi, **atau**
- `php artisan admin:create admin@desa.id --name="Admin Desa"` (kata sandi diminta secara tersembunyi).

Kata sandi di-hash bcrypt oleh Laravel; tidak ada penyimpanan kata sandi manual.

## 6. Deploy ke Vercel

1. **Siapkan MySQL publik gratis**, mis. *TiDB Cloud Serverless* atau *Aiven for MySQL* (free tier). Catat host, port, user, password, nama database. Aktifkan SSL.
2. **Jalankan migration & seed dari komputer lokal** ke database tersebut:
   ```bash
   # sementara arahkan .env ke database produksi
   php artisan migrate --seed --force
   ```
3. Push project ke GitHub, lalu **Import Project** di Vercel (Framework Preset: *Other*). `vercel.json` sudah mengatur runtime `vercel-php@0.9.0`, build aset (`npm run build`), dan routing.
4. Isi **Environment Variables** di Vercel: `APP_KEY`, `APP_URL`, `DB_*`, `MYSQL_ATTR_SSL_CA=/etc/pki/tls/certs/ca-bundle.crt` (bila SSL), `ADMIN_*` (opsional). Variabel cache/log/session sudah diatur di `vercel.json` (ditulis ke `/tmp`, log ke `stderr`, session & cache di database).
5. Deploy. Buka `/admin/login` untuk masuk CMS.

Gambar unggahan disimpan **di tabel `media` (MySQL)** sehingga tetap permanen walaupun filesystem Vercel bersifat sementara. Gambar diperkecil otomatis di browser (maks. 1920 px, WebP) sebelum diunggah.

## 7. Keamanan

- Semua route `/admin/*` dilindungi middleware `auth` + `admin` (kolom `is_admin`), login dengan rate limit.
- Pengunjung hanya membaca konten berstatus **published**; konten terjadwal (tanggal terbit di masa depan) belum tampil.
- Pengunjung hanya dapat membuat pesan kontak & kontribusi warga; keduanya punya **honeypot**, waktu isi minimum, dan pembatasan frekuensi.
- Validasi server untuk semua input (pesan Bahasa Indonesia) + validasi/pratinjau di klien.
- Rich text disanitasi HTML Purifier (saat disimpan **dan** saat ditampilkan).
- Unggahan dibatasi JPG/PNG/WebP, ≤ 2 MB, dimensi wajar.
- Kontak pengirim kontribusi tidak pernah ditampilkan publik; draf berita dari kontribusi tidak memuat nama/kontak pengirim.
- Data ekonomi per KK hanya di CMS; publik melihat agregat. Header keamanan (`X-Frame-Options`, `nosniff`, `Referrer-Policy`, `noindex` untuk admin). CSRF bawaan Laravel.
- *Row Level Security* (konsep Supabase pada prompt awal) diimplementasikan di level aplikasi melalui scope `published()` dan middleware admin, karena MySQL tidak memiliki RLS.

## 8. Aksesibilitas & performa

HTML semantik, skip link, label & `aria-describedby` pada form, ringkasan error yang difokuskan, `aria-invalid`, fokus terlihat, target sentuh ≥ 44 px, menu seluler ber-ARIA, dialog keyboard-friendly, alt text wajib di galeri, grafik dengan ringkasan teks + tabel, warna tidak menjadi satu-satunya penanda, `prefers-reduced-motion` dihormati (animasi framer-motion dimatikan). Diuji dari lebar 320 px tanpa horizontal scroll.

Peta & Chart.js dimuat malas (dynamic import saat terlihat), gambar `loading="lazy"`, font di-host mandiri, gambar media di-cache 1 tahun dengan ETag, metadata & Open Graph dinamis, `sitemap.xml`, `robots.txt`, halaman 404/419/500 khusus.

## 9. Uji otomatis

```bash
php artisan test
```

13 test (59 asersi) mencakup: semua halaman publik, batas ≤10 route halaman, draf tersembunyi, pengumuman kedaluwarsa, proteksi admin, CRUD → tampil publik + sanitasi + upload, validasi, alur moderasi kontribusi, honeypot, smart search, klasifikasi penghasilan & privasi data KK.

## 10. Aset & lisensi

| Aset | Lisensi |
|---|---|
| Laravel, Alpine.js, Tailwind CSS, Chart.js, Leaflet, framer-motion, Trix | MIT / BSD-2 (Leaflet) |
| HTML Purifier | LGPL-2.1 |
| Lucide icons (blade-lucide-icons) | ISC / MIT |
| Plus Jakarta Sans (Tokotype), Fraunces | SIL Open Font License 1.1 |
| Data peta OpenStreetMap | ODbL — atribusi “© kontributor OpenStreetMap” tampil di peta & footer |
| Ilustrasi `public/images/seed/*.svg`, logo/favicon | Karya orisinal tim (dibuat oleh `scripts/generate-illustrations.py`) |
| Logo IIT Challenge | **Tidak disertakan** — unduh file resmi dari panitia untuk materi presentasi/Instagram |

## 11. Known limitations

- **Lokasi desa diasumsikan** Desa Marga Mulya, Kec. Mauk, Kab. Tangerang, Banten (titik OpenStreetMap), karena dokumen EcoQuestData tidak menyebut lokasi. Ada ≥15 desa bernama Marga Mulya di Indonesia; konfirmasi ke panitia lalu ubah di *Pengaturan situs* & *Profil desa*.
- Dokumen EcoQuestData hanya memuat **struktur kategori** (tanpa angka); semua angka, nama, dan koordinat adalah contoh.
- Kategori **D. Agama** sengaja tidak ditampilkan untuk menghindari konten bernuansa SARA.
- Aturan lomba meminta konten website berbahasa Indonesia, sementara submission diminta berbahasa Inggris — website ini berbahasa Indonesia; sinopsis & outline presentasi berbahasa Inggris. Disarankan konfirmasi di Technical Meeting.
- `vercel-php` adalah runtime komunitas (bukan resmi Vercel). Cold start pertama bisa ±1–3 detik. Deploy ke Vercel **belum diuji** karena membutuhkan akun Vercel & database MySQL publik.
- Fitur AI "Tanya Marga Mulya" (opsional) belum diimplementasikan; seluruh fitur inti & inovasi diprioritaskan.
- Smart search memakai pencocokan `LIKE` multi-kata (cukup untuk skala desa), belum full-text search.
- Ekstensi GD tidak dibutuhkan; kompresi gambar dilakukan di browser admin.

Dokumentasi tambahan: [`docs/project-synopsis.md`](docs/project-synopsis.md), [`docs/presentation-outline.md`](docs/presentation-outline.md), [`docs/panduan-demo-cms.md`](docs/panduan-demo-cms.md).
