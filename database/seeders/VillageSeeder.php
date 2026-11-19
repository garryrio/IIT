<?php

namespace Database\Seeders;

use App\Models\CitizenSubmission;
use App\Models\ContactMessage;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\Household;
use App\Models\IncomeClass;
use App\Models\InformationPost;
use App\Models\Official;
use App\Models\Potential;
use App\Models\SiteSetting;
use App\Models\StatCategory;
use App\Models\Story;
use App\Models\VillageProfile;
use App\Models\VillageStatistic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Data CONTOH (dummy) Desa Marga Mulya.
 * Struktur mengikuti dokumen "Data EcoQuest IIT Challenge 2026" (A. Geografis, B. Pemerintahan,
 * C. Kependudukan & Angkatan Kerja, E. Pendidikan). Seluruh angka & nama adalah data contoh —
 * ganti dengan data resmi desa melalui CMS. Kategori D (Agama) sengaja tidak ditampilkan
 * untuk menghindari konten bernuansa SARA sesuai ketentuan kompetisi.
 */
class VillageSeeder extends Seeder
{
    private const LAT = -6.035507;

    private const LNG = 106.5230712;

    private const SOURCE = 'Data contoh berdasarkan struktur Data EcoQuest Desa Marga Mulya';

    public function run(): void
    {
        $this->settings();
        $this->profile();
        $this->officials();
        $this->facilities();
        $this->statistics();
        $this->potentials();
        $this->posts();
        $this->gallery();
        $this->stories();
        $this->participation();
        $this->income();
    }

    private function img(string $name): string
    {
        return '/images/seed/'.$name.'.svg';
    }

    private function at(float $dLat, float $dLng): array
    {
        return ['latitude' => round(self::LAT + $dLat, 7), 'longitude' => round(self::LNG + $dLng, 7)];
    }

    private function settings(): void
    {
        SiteSetting::create([
            'village_name' => 'Desa Marga Mulya',
            'tagline' => 'Informasi Desa, Potensi Lokal, dan Partisipasi Masyarakat dalam Satu Platform.',
            'short_description' => 'Marga Mulya Hub adalah pusat informasi resmi Desa Marga Mulya: berita, agenda, data desa, potensi lokal, dan ruang partisipasi warga.',
            'hero_title' => 'Tumbuh Bersama dari Sawah, Usaha, dan Gotong Royong',
            'hero_description' => 'Kenali Desa Marga Mulya lebih dekat — temukan informasi layanan, agenda kegiatan, data desa yang mudah dipahami, serta potensi lokal yang siap mendukung ekonomi warga.',
            'hero_image_url' => $this->img('hero-sawah'),
            'hero_cta_primary' => 'Jelajahi Desa',
            'hero_cta_secondary' => 'Lihat Potensi',
            'address' => 'Kantor Desa Marga Mulya, Kecamatan Mauk, Kabupaten Tangerang, Banten',
            'phone' => '0800-0000-0000',
            'whatsapp' => '0800-0000-0000',
            'email' => 'pemdes.margamulya@example.com',
            'service_hours' => 'Senin–Jumat, 08.00–15.00 WIB',
            'latitude' => self::LAT,
            'longitude' => self::LNG,
            'social_links' => [],
            'contribution_cta_title' => 'Punya informasi tentang desa?',
            'contribution_cta_text' => 'Kirimkan kabar kegiatan, potensi usaha, atau kondisi lingkungan di sekitar Anda. Setiap kontribusi diperiksa admin desa sebelum dipublikasikan.',
            'demo_notice' => 'Seluruh data pada situs ini merupakan data contoh (dummy) untuk keperluan IIT Challenge 2026, disusun mengikuti struktur Data EcoQuest Desa Marga Mulya. Gambar berupa ilustrasi.',
            'show_income_summary' => true,
        ]);
    }

    private function profile(): void
    {
        VillageProfile::create([
            'official_name' => 'Desa Marga Mulya',
            'district' => 'Mauk',
            'regency' => 'Kabupaten Tangerang',
            'province' => 'Banten',
            'description' => 'Desa Marga Mulya adalah desa agraris dengan hamparan sawah yang luas, permukiman yang tertata di sepanjang jalan desa, serta warga yang aktif dalam kegiatan gotong royong, kelompok tani, dan usaha rumahan.',
            'history' => '<p>Bagian ini berisi sejarah singkat desa. <strong>Teks berikut adalah contoh</strong> dan perlu diganti dengan sejarah resmi dari pemerintah desa.</p><p>Nama "Marga Mulya" kerap dimaknai sebagai harapan akan jalan (marga) menuju kehidupan yang mulia dan sejahtera. Seiring waktu, desa berkembang dari kawasan pertanian menjadi desa yang juga tumbuh lewat usaha rumahan, pelatihan keterampilan, dan kegiatan kemasyarakatan.</p>',
            'vision' => 'Terwujudnya Desa Marga Mulya yang maju, mandiri, dan sejahtera melalui pelayanan yang terbuka serta pemberdayaan potensi lokal.',
            'mission' => "Meningkatkan kualitas pelayanan administrasi yang cepat dan transparan\nMengembangkan pertanian dan usaha mikro warga sebagai penggerak ekonomi desa\nMendorong partisipasi masyarakat dalam perencanaan dan pembangunan desa\nMenjaga kelestarian lingkungan dan kebersihan desa\nMemperluas akses pendidikan dan pelatihan keterampilan bagi warga",
            'area_hectare' => 425.5,
            'border_north' => 'Lahan persawahan dan saluran irigasi',
            'border_east' => 'Jalan penghubung antardesa',
            'border_south' => 'Permukiman dan kebun warga',
            'border_west' => 'Aliran sungai kecil dan area perkebunan',
            'geography_notes' => 'Wilayah desa didominasi dataran rendah. Sebagian besar lahan dimanfaatkan sebagai persawahan dan permukiman (data contoh).',
            'rw_count' => 6,
            'rt_count' => 24,
            'image_url' => $this->img('balai-desa'),
        ]);
    }

    private function officials(): void
    {
        $rows = [
            ['Ahmad Suryadi', 'Kepala Desa', 'Pemerintah Desa', 'Perangkat Desa', 'Laki-laki', 'S1'],
            ['Siti Rahmawati', 'Sekretaris Desa', 'Pemerintah Desa', 'PNS', 'Perempuan', 'S1'],
            ['Budi Santoso', 'Kaur Keuangan', 'Pemerintah Desa', 'Perangkat Desa', 'Laki-laki', 'D3'],
            ['Dewi Lestari', 'Kaur Umum & Perencanaan', 'Pemerintah Desa', 'Perangkat Desa', 'Perempuan', 'S1'],
            ['Agus Setiawan', 'Kasi Pemerintahan', 'Pemerintah Desa', 'Perangkat Desa', 'Laki-laki', 'SMA/SMK'],
            ['Rina Marlina', 'Kasi Kesejahteraan', 'Pemerintah Desa', 'Perangkat Desa', 'Perempuan', 'S1'],
            ['Hendra Gunawan', 'Kasi Pelayanan', 'Pemerintah Desa', 'Perangkat Desa', 'Laki-laki', 'SMA/SMK'],
            ['Joko Prasetyo', 'Kepala Dusun I', 'Pemerintah Desa', 'Perangkat Desa', 'Laki-laki', 'SMA/SMK'],
            ['Nurhayati', 'Kepala Dusun II', 'Pemerintah Desa', 'Perangkat Desa', 'Perempuan', 'SMA/SMK'],
            ['Dedi Kurniawan', 'Ketua BPD', 'BPD', 'Non-PNS', 'Laki-laki', 'S1'],
            ['Wulan Sari', 'Anggota BPD', 'BPD', 'Non-PNS', 'Perempuan', 'D3'],
            ['Sri Wahyuni', 'Pendamping Desa (Kecamatan)', 'Kecamatan', 'PNS', 'Perempuan', 'S1'],
        ];
        foreach ($rows as $i => [$name, $pos, $unit, $status, $gender, $edu]) {
            Official::create(['name' => $name, 'position' => $pos, 'unit' => $unit, 'employment_status' => $status,
                'gender' => $gender, 'education' => $edu, 'display_order' => $i + 1, 'status' => 'published']);
        }
    }

    private function facilities(): void
    {
        $rows = [
            ['Kantor Desa Marga Mulya', 'pemerintahan', 'Pusat pelayanan administrasi warga dan kegiatan pemerintahan desa.', [0, 0], 'kantor-desa'],
            ['Balai Desa', 'pemerintahan', 'Ruang pertemuan untuk musyawarah desa, pelatihan, dan kegiatan warga.', [-0.0004, 0.0009], 'balai-desa'],
            ['Posyandu Melati', 'kesehatan', 'Layanan kesehatan ibu dan anak setiap bulan, termasuk penimbangan balita dan imunisasi.', [-0.0042, 0.0031], 'posyandu'],
            ['Sekolah Dasar Negeri (contoh)', 'pendidikan', 'Fasilitas pendidikan dasar bagi anak-anak desa.', [0.0022, 0.0060], 'sekolah'],
            ['Taman Baca Warga', 'pendidikan', 'Pojok baca dan ruang belajar bersama untuk anak dan remaja.', [-0.0030, -0.0015], null],
            ['Pos Kamling RW 02', 'keamanan', 'Pos ronda untuk menjaga keamanan lingkungan secara bergiliran.', [0.0028, 0.0075], null],
            ['Lapangan Desa', 'olahraga', 'Lapangan terbuka untuk olahraga, upacara, dan acara desa.', [-0.0045, -0.0040], 'lapangan'],
            ['Bank Sampah Mulya Lestari', 'umum', 'Tempat warga menabung sampah terpilah yang dapat ditukar dengan nilai ekonomi.', [0.0021, 0.0063], 'bank-sampah'],
        ];
        foreach ($rows as $i => [$name, $type, $desc, $d, $img]) {
            Facility::create(['name' => $name, 'type' => $type, 'description' => $desc, 'address' => 'Desa Marga Mulya',
                'image_url' => $img ? $this->img($img) : null, 'display_order' => $i + 1, 'status' => 'published'] + $this->at($d[0], $d[1]));
        }
    }

    private function stat(string $cat, string $ind, float $val, string $unit, int $year, string $chart, int $order, bool $hl = false): void
    {
        VillageStatistic::create(['category' => $cat, 'indicator' => $ind, 'value' => $val, 'unit' => $unit, 'year' => $year,
            'chart_type' => $chart, 'display_order' => $order, 'is_highlight' => $hl, 'source' => self::SOURCE, 'is_published' => true]);
    }

    private function statistics(): void
    {
        // Ringkasan (kartu & beranda) — dua tahun agar filter tahun dapat digunakan
        foreach ([2024 => [6771, 1996, 3402, 3369], 2025 => [6842, 2018, 3451, 3391]] as $y => [$pop, $kk, $l, $p]) {
            $this->stat('Ringkasan Desa', 'Jumlah penduduk', $pop, 'jiwa', $y, 'cards', 1, true);
            $this->stat('Ringkasan Desa', 'Kepala keluarga (KK)', $kk, 'KK', $y, 'cards', 2, true);
            $this->stat('Ringkasan Desa', 'Luas wilayah', 425.5, 'ha', $y, 'cards', 3, true);
            $this->stat('Ringkasan Desa', 'Jumlah RT', 24, 'RT', $y, 'cards', 4, true);
            $this->stat('Penduduk Menurut Jenis Kelamin', 'Laki-laki', $l, 'jiwa', $y, 'donut', 10);
            $this->stat('Penduduk Menurut Jenis Kelamin', 'Perempuan', $p, 'jiwa', $y, 'donut', 11);
        }

        foreach ([2021 => 6512, 2022 => 6601, 2023 => 6688, 2024 => 6771, 2025 => 6842] as $y => $v) {
            $this->stat('Tren Jumlah Penduduk', 'Jumlah penduduk', $v, 'jiwa', $y, 'line', 5);
        }

        $land = ['Persawahan' => 196.4, 'Permukiman' => 118.2, 'Pekarangan' => 41.3, 'Perkebunan' => 38.5,
            'Prasarana umum lainnya' => 21.5, 'Perkuburan' => 4.6, 'Taman' => 3.2, 'Perkantoran' => 1.8];
        $i = 0;
        foreach ($land as $k => $v) {
            $this->stat('Penggunaan Lahan', $k, $v, 'ha', 2025, 'donut', 20 + $i++);
        }

        $jobs = [2025 => ['Petani' => 1184, 'Karyawan swasta' => 1036, 'Pengrajin' => 142, 'Nelayan' => 96, 'Guru' => 74, 'PNS' => 58, 'POLRI' => 12, 'TNI' => 9, 'Lainnya' => 1247],
            2024 => ['Petani' => 1203, 'Karyawan swasta' => 998, 'Pengrajin' => 131, 'Nelayan' => 99, 'Guru' => 71, 'PNS' => 57, 'POLRI' => 11, 'TNI' => 9, 'Lainnya' => 1221]];
        foreach ($jobs as $y => $rows) {
            $i = 0;
            foreach ($rows as $k => $v) {
                $this->stat('Mata Pencaharian Pokok', $k, $v, 'orang', $y, 'horizontal', 30 + $i++);
            }
        }

        $edu = ['Tidak/belum sekolah' => 812, 'SD/sederajat' => 1964, 'SMP/sederajat' => 1488, 'SMA/SMK/sederajat' => 1902, 'Diploma' => 214, 'S1' => 412, 'S2/S3' => 50];
        $i = 0;
        foreach ($edu as $k => $v) {
            $this->stat('Tingkat Pendidikan Penduduk', $k, $v, 'jiwa', 2025, 'bar', 40 + $i++);
        }

        foreach ([2024 => [4071, 341], 2025 => [4126, 318]] as $y => [$tk, $pk]) {
            $this->stat('Angkatan Kerja', 'Tenaga kerja usia 18–56 tahun', $tk, 'orang', $y, 'bar', 50);
            $this->stat('Angkatan Kerja', 'Pencari kerja terdaftar', $pk, 'orang', $y, 'bar', 51);
        }
        foreach ([2024 => [58, 9], 2025 => [64, 7]] as $y => [$n, $c]) {
            $this->stat('Peristiwa Kependudukan', 'Pernikahan', $n, 'peristiwa', $y, 'bar', 55);
            $this->stat('Peristiwa Kependudukan', 'Perceraian', $c, 'peristiwa', $y, 'bar', 56);
        }

        $schools = ['SD/MI negeri' => 3, 'SD/MI swasta' => 1, 'SLTP/MTs negeri' => 1, 'SLTP/MTs swasta' => 1, 'SLTA/SMK/MA swasta' => 1];
        $i = 0;
        foreach ($schools as $k => $v) {
            $this->stat('Jumlah Sekolah', $k, $v, 'sekolah', 2025, 'bar', 60 + $i++);
        }
        foreach (['SD/MI' => [38, 812, 28], 'SLTP/MTs' => [24, 386, 14], 'SLTA/SMK/MA' => [16, 214, 9]] as $lvl => [$g, $m, $r]) {
            $this->stat('Guru per Jenjang', $lvl, $g, 'guru', 2025, 'bar', 65);
            $this->stat('Murid per Jenjang', $lvl, $m, 'murid', 2025, 'bar', 66);
            $this->stat('Ruang Kelas per Jenjang', $lvl, $r, 'ruang', 2025, 'bar', 67);
        }
        $i = 0;
        foreach (['Menjahit' => 2, 'Komputer' => 2, 'Bahasa' => 1, 'Kecantikan' => 1, 'Montir' => 1, 'Elektronik' => 1] as $k => $v) {
            $this->stat('Lembaga Pendidikan Non-formal', $k, $v, 'lembaga', 2025, 'horizontal', 70 + $i++);
        }
        foreach (['Paket A' => 12, 'Paket B' => 25, 'Paket C' => 41] as $k => $v) {
            $this->stat('Pendidikan Kesetaraan', $k, $v, 'peserta', 2025, 'bar', 75);
        }

        foreach (['Pos polisi' => 1, 'Anggota Linmas' => 32, 'Pos kamling' => 14, 'Jumlah RW' => 6, 'Jumlah RT' => 24] as $k => $v) {
            $this->stat('Pemerintahan & Keamanan', $k, $v, in_array($k, ['Anggota Linmas']) ? 'orang' : 'unit', 2025, 'cards', 80);
        }

        // Daftar kategori baku untuk dropdown CMS, urut sesuai urutan tampil grafik.
        VillageStatistic::query()->selectRaw('category, MIN(display_order) as ord')->groupBy('category')->orderBy('ord')->get()
            ->each(fn ($row, $i) => StatCategory::firstOrCreate(['name' => $row->category], ['display_order' => $i + 1]));
    }

    private function potentials(): void
    {
        $rows = [
            ['Kelompok Tani Marga Makmur', 'pertanian', 'Hamparan sawah padi yang dikelola kelompok tani dengan pola tanam dua kali setahun.', 'sawah-padi', true, [-0.0055, 0.0010], 'Kelompok Tani Marga Makmur', 'Senin–Sabtu, 07.00–16.00', 'Gabah & beras: menyesuaikan musim'],
            ['Dapur Mulya — Keripik & Rengginang', 'umkm', 'Usaha rumahan olahan keripik singkong, keripik pisang, dan rengginang dengan kemasan siap jual.', 'umkm-olahan', true, [-0.0014, 0.0022], 'Kelompok Wanita Tani RW 01', 'Setiap hari, 08.00–17.00', 'Rp10.000 – Rp35.000'],
            ['Anyaman Bambu Warga', 'kerajinan', 'Kerajinan tangan berupa keranjang, tampah, dan wadah serbaguna dari bambu buatan pengrajin lokal.', 'kerajinan-bambu', true, [-0.0028, -0.0030], 'Paguyuban Pengrajin RW 03', 'Senin–Sabtu, 09.00–16.00', 'Rp15.000 – Rp120.000'],
            ['Taman Desa & Jalur Sehat', 'wisata', 'Ruang terbuka hijau untuk bersantai, berolahraga ringan, dan kegiatan keluarga di akhir pekan.', 'taman-desa', true, [-0.0036, -0.0047], 'Pemerintah Desa', 'Setiap hari, 06.00–18.00', 'Gratis'],
            ['Kebun Sayur Pemuda', 'pertanian', 'Kebun sayur yang dikelola karang taruna untuk sayuran segar seperti kangkung, bayam, dan cabai.', 'kebun-sayur', false, [-0.0010, 0.0052], 'Karang Taruna Desa', 'Panen: Selasa & Jumat', 'Rp5.000 – Rp20.000 per ikat'],
            ['Sentra Kuliner Sore', 'kuliner', 'Deretan lapak kuliner warga yang buka sore hari dengan menu jajanan pasar dan makanan rumahan.', 'kuliner-desa', false, [-0.0008, 0.0051], 'Paguyuban Pedagang Desa', 'Setiap hari, 15.00–21.00', 'Rp3.000 – Rp25.000'],
            ['Pelatihan Menjahit & Konveksi Rumahan', 'jasa', 'Lembaga keterampilan menjahit yang melatih warga dan menerima pesanan konveksi skala kecil.', 'pelatihan', false, [-0.0016, 0.0004], 'Lembaga Kursus Menjahit Desa', 'Senin–Jumat, 09.00–15.00', 'Sesuai paket pelatihan'],
            ['Bank Sampah Mulya Lestari', 'jasa', 'Layanan tabungan sampah terpilah yang mengubah sampah plastik dan kertas menjadi nilai ekonomi.', 'bank-sampah', false, [0.0021, 0.0063], 'Pengelola Bank Sampah', 'Sabtu, 08.00–12.00', 'Sesuai harga jenis sampah'],
            ['Pasar Tani Mingguan', 'umkm', 'Pasar mingguan tempat petani dan pelaku UMKM menjual hasil panen dan produk olahan langsung ke pembeli.', 'pasar-desa', false, [0.0008, 0.0016], 'BUMDes (contoh)', 'Minggu, 06.00–10.00', 'Bervariasi'],
        ];
        foreach ($rows as [$name, $cat, $sum, $img, $feat, $d, $owner, $hours, $price]) {
            Potential::create([
                'name' => $name, 'slug' => Str::slug($name), 'category' => $cat, 'summary' => $sum,
                'description' => '<p>'.e($sum).'</p><p>Deskripsi ini merupakan <strong>data contoh</strong>. Pengelola dapat memperbarui informasi produk, foto, harga, dan kontak melalui CMS desa agar promosi selalu akurat.</p><ul><li>Lokasi berada di wilayah Desa Marga Mulya</li><li>Kunjungan atau pemesanan dapat dikoordinasikan melalui kontak pengelola</li></ul>',
                'image_url' => $this->img($img), 'owner_or_manager' => $owner, 'address' => 'Desa Marga Mulya',
                'whatsapp' => '0800-0000-0000', 'operating_hours' => $hours, 'price_range' => $price, 'social_links' => [],
                'is_featured' => $feat, 'status' => 'published',
            ] + $this->at($d[0], $d[1]));
        }
    }

    private function post(array $a): void
    {
        $a['slug'] = Str::slug($a['title']);
        $a['status'] ??= 'published';
        InformationPost::create($a);
    }

    private function posts(): void
    {
        $t = Carbon::today();
        $body = fn (string $lead, array $points = []) => '<p>'.$lead.'</p>'.($points ? '<ul><li>'.implode('</li><li>', $points).'</li></ul>' : '')
            .'<p><em>Konten ini adalah contoh dan dapat diperbarui melalui CMS desa.</em></p>';

        // Berita
        $this->post(['type' => 'news', 'title' => 'Warga Gotong Royong Bersihkan Saluran Irigasi Menjelang Musim Tanam', 'cover_image_url' => $this->img('irigasi'),
            'excerpt' => 'Warga bersama kelompok tani membersihkan saluran irigasi agar aliran air ke sawah lancar saat musim tanam.',
            'content' => $body('Puluhan warga dan anggota kelompok tani bergotong royong membersihkan saluran irigasi utama desa. Kegiatan ini bertujuan memastikan pasokan air ke sawah tetap lancar.', ['Sedimen dan sampah diangkat dari saluran', 'Titik rawan tersumbat dicatat untuk perbaikan']),
            'published_at' => $t->copy()->subDays(3)->setTime(9, 0), 'location' => 'Saluran irigasi RW 04']);
        $this->post(['type' => 'news', 'title' => 'Pelatihan Pengemasan Produk untuk Pelaku UMKM Desa', 'cover_image_url' => $this->img('umkm-olahan'),
            'excerpt' => 'Pelaku UMKM mendapat pelatihan desain kemasan dan label agar produk lokal lebih menarik dan siap dipasarkan.',
            'content' => $body('Pelatihan diikuti pelaku usaha olahan makanan dan kerajinan. Peserta belajar membuat label sederhana, mencantumkan informasi produk, dan memotret produk dengan ponsel.'),
            'published_at' => $t->copy()->subDays(8)->setTime(10, 0), 'location' => 'Balai Desa']);
        $this->post(['type' => 'news', 'title' => 'Musyawarah Desa Bahas Prioritas Pembangunan Tahun Depan', 'cover_image_url' => $this->img('musyawarah'),
            'excerpt' => 'Perwakilan RT, RW, BPD, dan tokoh masyarakat menyampaikan usulan prioritas pembangunan desa.',
            'content' => $body('Musyawarah desa membahas usulan dari setiap RW, mulai dari perbaikan jalan lingkungan hingga penguatan usaha warga.', ['Perbaikan jalan lingkungan', 'Penambahan lampu jalan', 'Pelatihan keterampilan warga']),
            'published_at' => $t->copy()->subDays(15)->setTime(13, 0), 'location' => 'Balai Desa']);
        $this->post(['type' => 'news', 'title' => 'Bank Sampah Desa Ajak Warga Memilah Sampah dari Rumah', 'cover_image_url' => $this->img('bank-sampah'),
            'excerpt' => 'Bank sampah desa mengajak warga memilah sampah plastik, kertas, dan logam agar bernilai ekonomi.',
            'content' => $body('Pengelola bank sampah menyosialisasikan cara memilah sampah rumah tangga. Sampah terpilah dapat ditabung dan ditukar dengan nilai ekonomi.'),
            'published_at' => $t->copy()->subDays(22)->setTime(8, 30), 'location' => 'Bank Sampah Mulya Lestari']);
        $this->post(['type' => 'news', 'title' => 'Draf: Rencana Festival Panen Desa', 'status' => 'draft',
            'excerpt' => 'Draf internal — tidak tampil di website publik sampai diterbitkan admin.', 'content' => $body('Rencana kegiatan masih dibahas.')]);

        // Pengumuman (dengan masa berlaku)
        $this->post(['type' => 'announcement', 'title' => 'Pelayanan Administrasi Kependudukan Keliling', 'priority' => 2,
            'excerpt' => 'Petugas akan melayani perekaman dan pembaruan dokumen kependudukan di balai desa. Bawa dokumen pendukung.',
            'content' => $body('Pelayanan administrasi kependudukan keliling dibuka untuk warga. Mohon membawa dokumen pendukung yang diperlukan.', ['Fotokopi KK', 'Dokumen pendukung sesuai keperluan']),
            'start_date' => $t->copy()->subDays(2), 'end_date' => $t->copy()->addDays(10), 'published_at' => $t->copy()->subDays(2)->setTime(7, 0), 'location' => 'Balai Desa']);
        $this->post(['type' => 'announcement', 'title' => 'Pemutakhiran Data Ekonomi Keluarga untuk Penyaluran Bantuan', 'priority' => 1,
            'excerpt' => 'Petugas RT/RW akan mendata ulang kondisi ekonomi keluarga agar penyaluran bantuan lebih tepat sasaran.',
            'content' => $body('Pemerintah desa melakukan pemutakhiran data ekonomi keluarga. Data hanya digunakan untuk perencanaan bantuan dan tidak dipublikasikan per keluarga.'),
            'start_date' => $t->copy()->subDays(5), 'end_date' => $t->copy()->addDays(20), 'published_at' => $t->copy()->subDays(5)->setTime(8, 0)]);
        $this->post(['type' => 'announcement', 'title' => 'Pendaftaran Pelatihan Menjahit Gelombang 2', 'priority' => 0,
            'excerpt' => 'Warga usia produktif dapat mendaftar pelatihan menjahit gratis di kantor desa. Kuota terbatas.',
            'content' => $body('Pendaftaran dibuka bagi warga usia produktif. Silakan datang ke kantor desa pada jam layanan.'),
            'start_date' => $t->copy()->subDay(), 'end_date' => $t->copy()->addDays(14), 'published_at' => $t->copy()->subDay()->setTime(9, 0)]);
        $this->post(['type' => 'announcement', 'title' => 'Pemeliharaan Jaringan Air Bersih (Telah Selesai)', 'priority' => 1,
            'excerpt' => 'Contoh pengumuman yang sudah kedaluwarsa — otomatis tidak tampil sebagai pengumuman aktif.',
            'content' => $body('Pemeliharaan jaringan air bersih telah selesai dilaksanakan.'),
            'start_date' => $t->copy()->subDays(20), 'end_date' => $t->copy()->subDays(12), 'published_at' => $t->copy()->subDays(20)->setTime(7, 0)]);

        // Agenda (tanggal relatif agar selalu ada agenda mendatang)
        $events = [
            ['Posyandu Balita dan Lansia', 3, '08:00', '11:00', 'Posyandu Melati', 'Kader Posyandu', 'posyandu', [-0.0042, 0.0031]],
            ['Kerja Bakti Lingkungan Serentak', 6, '07:00', '10:00', 'Seluruh RW', 'Pemerintah Desa', 'gotong-royong', [-0.0006, 0.0003]],
            ['Pasar Tani Mingguan', 10, '06:00', '10:00', 'Halaman Balai Desa', 'BUMDes (contoh)', 'pasar-desa', [0.0008, 0.0016]],
            ['Musyawarah Desa Perencanaan Pembangunan', 15, '13:00', '16:00', 'Balai Desa', 'BPD & Pemerintah Desa', 'musyawarah', [-0.0004, 0.0009]],
            ['Pelatihan Pemasaran Digital untuk UMKM', 21, '09:00', '14:00', 'Balai Desa', 'Pemerintah Desa', 'pelatihan', [-0.0004, 0.0009]],
            ['Turnamen Olahraga Antar-RW', 28, '15:00', '17:30', 'Lapangan Desa', 'Karang Taruna', 'lapangan', [-0.0045, -0.0040]],
            ['Lomba Kebersihan Lingkungan RT', -12, '08:00', '12:00', 'Seluruh RT', 'Pemerintah Desa', 'jalan-desa', [0.0001, 0.0001]],
        ];
        foreach ($events as [$title, $days, $s, $e, $loc, $org, $img, $d]) {
            $this->post(['type' => 'event', 'title' => $title, 'cover_image_url' => $this->img($img),
                'excerpt' => $title.' di '.$loc.'. Seluruh warga diundang untuk hadir dan berpartisipasi.',
                'content' => $body('Kegiatan '.Str::lower($title).' akan dilaksanakan di '.$loc.'. Warga diharapkan hadir tepat waktu.'),
                'event_date' => $t->copy()->addDays($days), 'event_time' => $s, 'event_end_time' => $e, 'location' => $loc, 'organizer' => $org,
                'published_at' => $t->copy()->subDays(max(1, 30 - $days))->setTime(8, 0)] + $this->at($d[0], $d[1]));
        }

        // Informasi layanan
        $services = [
            ['Layanan Surat Keterangan Domisili', 'Surat keterangan domisili untuk keperluan administrasi warga.', ['Fotokopi KTP dan KK', 'Surat pengantar RT/RW', 'Datang ke kantor desa pada jam layanan']],
            ['Layanan Surat Keterangan Usaha', 'Surat keterangan usaha bagi pelaku UMKM untuk keperluan perizinan atau permodalan.', ['Fotokopi KTP dan KK', 'Surat pengantar RT/RW', 'Foto tempat usaha']],
            ['Pengaduan dan Aspirasi Warga', 'Warga dapat menyampaikan pengaduan atau aspirasi melalui formulir kontak di website maupun langsung ke kantor desa.', ['Isi formulir kontak di halaman Kontak', 'Atau datang ke kantor desa', 'Admin akan menindaklanjuti sesuai kewenangan']],
        ];
        foreach ($services as $i => [$title, $ex, $steps]) {
            $this->post(['type' => 'service', 'title' => $title, 'excerpt' => $ex, 'content' => $body($ex.' Persyaratan & langkah:', $steps),
                'published_at' => $t->copy()->subDays(40 + $i)->setTime(8, 0), 'location' => 'Kantor Desa Marga Mulya']);
        }
    }

    private function gallery(): void
    {
        $rows = [
            ['Gotong royong warga', 'kegiatan', 'gotong-royong', 'Ilustrasi warga bergotong royong membersihkan jalan desa', -5],
            ['Hamparan sawah saat pagi', 'lingkungan', 'hero-sawah', 'Ilustrasi hamparan sawah hijau dengan rumah warga dan pepohonan', -9],
            ['Musyawarah desa', 'pemerintahan', 'musyawarah', 'Ilustrasi warga duduk bersama dalam musyawarah desa', -15],
            ['Produk olahan UMKM', 'umkm', 'umkm-olahan', 'Ilustrasi lapak UMKM dengan toples produk olahan', -8],
            ['Kerajinan anyaman bambu', 'umkm', 'kerajinan-bambu', 'Ilustrasi pengrajin dengan keranjang anyaman bambu', -30],
            ['Saluran irigasi yang bersih', 'pembangunan', 'irigasi', 'Ilustrasi saluran irigasi di tepi sawah', -3],
            ['Posyandu Melati', 'fasilitas', 'posyandu', 'Ilustrasi bangunan posyandu dengan warga', -20],
            ['Taman desa', 'lingkungan', 'taman-desa', 'Ilustrasi taman desa dengan pepohonan dan bangku', -25],
            ['Kantor desa', 'fasilitas', 'kantor-desa', 'Ilustrasi kantor desa dengan tiang bendera', -40],
            ['Senja di persawahan', 'lingkungan', 'sawah-senja', 'Ilustrasi sawah saat matahari terbenam', -12],
            ['Pelatihan menjahit', 'kegiatan', 'pelatihan', 'Ilustrasi warga mengikuti pelatihan menjahit', -18],
            ['Pasar tani mingguan', 'umkm', 'pasar-desa', 'Ilustrasi lapak pasar tani dengan pembeli', -6],
        ];
        foreach ($rows as [$title, $cat, $img, $alt, $days]) {
            GalleryItem::create(['title' => $title, 'category' => $cat, 'image_url' => $this->img($img), 'alt_text' => $alt,
                'description' => 'Dokumentasi contoh: '.Str::lower($title).' di Desa Marga Mulya.', 'event_date' => Carbon::today()->addDays($days),
                'location' => 'Desa Marga Mulya', 'status' => 'published']);
        }
    }

    private function stories(): void
    {
        $rows = [
            ['Ibu Rohmah', 'Pelaku UMKM olahan keripik', 'Sejak produk kami tampil di website desa, pembeli dari luar desa lebih mudah menemukan kontak kami. (cerita contoh)'],
            ['Pak Darto', 'Anggota kelompok tani', 'Jadwal kerja bakti dan informasi musim tanam sekarang bisa dilihat kapan saja, tidak hanya dari pengumuman di balai. (cerita contoh)'],
            ['Nadia', 'Karang Taruna', 'Kami bisa mengirim kabar kegiatan pemuda lewat formulir kontribusi, lalu admin desa memeriksanya sebelum terbit. (cerita contoh)'],
        ];
        foreach ($rows as $i => [$name, $role, $quote]) {
            Story::create(['name' => $name, 'role' => $role, 'quote' => $quote, 'display_order' => $i + 1, 'status' => 'published']);
        }
    }

    private function participation(): void
    {
        ContactMessage::create(['name' => 'Warga RW 02', 'contact' => 'warga@example.com', 'subject' => 'Jadwal posyandu bulan depan',
            'message' => 'Mohon informasi apakah jadwal posyandu bulan depan tetap di hari yang sama. Terima kasih.', 'status' => 'unread']);
        ContactMessage::create(['name' => 'Pengunjung', 'contact' => '0800-0000-0001', 'subject' => 'Pemesanan anyaman bambu',
            'message' => 'Saya ingin memesan keranjang anyaman bambu dalam jumlah banyak. Bagaimana cara menghubungi pengrajin?', 'status' => 'read']);

        CitizenSubmission::create(['submitter_name' => 'Rudi', 'contact' => '0800-0000-0002', 'category' => 'infrastruktur', 'title' => 'Lampu jalan padam di RT 03/RW 02',
            'description' => 'Dua lampu jalan di dekat pos kamling RT 03 padam sejak minggu lalu sehingga jalan gelap pada malam hari.',
            'location' => 'Dekat Pos Kamling RW 02', 'status' => 'pending'] + $this->at(0.0026, 0.0073));
        CitizenSubmission::create(['submitter_name' => 'Karang Taruna', 'contact' => null, 'category' => 'kegiatan', 'title' => 'Kelas belajar bersama untuk anak setiap Sabtu',
            'description' => 'Karang taruna membuka kelas belajar bersama gratis untuk anak SD setiap Sabtu sore di taman baca warga.',
            'location' => 'Taman Baca Warga', 'status' => 'pending']);
        CitizenSubmission::create(['submitter_name' => 'Ibu Sari', 'contact' => 'sari@example.com', 'category' => 'potensi', 'title' => 'Produk sambal rumahan siap dipasarkan',
            'description' => 'Saya memproduksi sambal rumahan dalam botol dan ingin produk ini dikenal lebih luas melalui katalog potensi desa.',
            'status' => 'approved', 'admin_notes' => 'Akan diverifikasi untuk dimasukkan ke katalog potensi.', 'reviewed_at' => now()->subDay()]);
    }

    private function income(): void
    {
        $classes = [
            ['Sangat rendah', 0, 450000, 1, '#b5452f', 'Penghasilan per kapita sangat rendah — sasaran utama bantuan.'],
            ['Rendah', 450001, 750000, 2, '#d9822b', 'Penghasilan per kapita rendah — prioritas kedua.'],
            ['Rentan', 750001, 1000000, 3, '#d4b23a', 'Mendekati batas rendah — dipertimbangkan bila ada tanggungan/kondisi rumah kurang layak.'],
            ['Menengah', 1000001, 2500000, 0, '#5f9e57', 'Bukan sasaran bantuan.'],
            ['Atas', 2500001, null, 0, '#1f4d2b', 'Bukan sasaran bantuan.'],
        ];
        foreach ($classes as $i => [$name, $min, $max, $prio, $color, $desc]) {
            IncomeClass::create(['name' => $name, 'min_per_capita' => $min, 'max_per_capita' => $max, 'aid_priority' => $prio,
                'color' => $color, 'description' => $desc.' (Ambang batas contoh, dapat diubah sesuai kebijakan desa.)', 'display_order' => $i]);
        }

        // 84 KK data contoh dengan generator acak ber-seed tetap (hasil selalu sama)
        mt_srand(2026);
        $first = ['Ahmad', 'Budi', 'Cecep', 'Dadang', 'Eko', 'Fajar', 'Gilang', 'Hasan', 'Iwan', 'Jajang', 'Kurnia', 'Lukman', 'Maman', 'Nanang', 'Oman',
            'Ujang', 'Rahmat', 'Supri', 'Tatang', 'Wawan', 'Yusuf', 'Asep', 'Siti', 'Euis', 'Ratna', 'Neneng', 'Yati', 'Imas', 'Lilis', 'Tuti'];
        $last = ['Saputra', 'Hidayat', 'Kusnadi', 'Permana', 'Suhendar', 'Ramdani', 'Firmansyah', 'Gunawan', 'Wijaya', 'Sutisna', 'Mulyadi', 'Hermawan', 'Rohman', 'Sopian', 'Komarudin'];
        $jobs = ['Petani', 'Buruh tani', 'Pedagang/UMKM', 'Pengrajin', 'Karyawan swasta', 'Buruh harian', 'Nelayan', 'Guru', 'PNS', 'Tidak bekerja'];
        $n = 0;
        for ($rw = 1; $rw <= 6; $rw++) {
            for ($k = 0; $k < 14; $k++) {
                $n++;
                $members = mt_rand(1, 6);
                $job = $jobs[min(count($jobs) - 1, (int) floor((mt_rand(0, 1000) / 1000) ** 1.6 * count($jobs)))];
                $baseIncome = match ($job) {
                    'PNS', 'Guru' => mt_rand(3500, 7000), 'Karyawan swasta' => mt_rand(2800, 6000), 'Pedagang/UMKM' => mt_rand(1500, 6500),
                    'Pengrajin' => mt_rand(1200, 3500), 'Petani' => mt_rand(1000, 4000), 'Nelayan' => mt_rand(900, 3000),
                    'Buruh tani', 'Buruh harian' => mt_rand(700, 2200), default => mt_rand(0, 900),
                };
                // RW 05–06 dibuat sedikit lebih rendah agar sebaran antar-RW terlihat pada grafik
                $income = (int) round($baseIncome * ($rw >= 5 ? 0.8 : 1) * 1000 / 50000) * 50000;
                $perCapita = $income / $members;
                $poor = $perCapita <= 750000;
                $receives = $poor ? mt_rand(1, 100) <= 55 : mt_rand(1, 100) <= 6;
                Household::create([
                    'household_code' => sprintf('KK-%02d-%03d', $rw, $n),
                    'head_name' => $first[mt_rand(0, count($first) - 1)].' '.$last[mt_rand(0, count($last) - 1)],
                    'rw' => $rw, 'rt' => mt_rand(1, 4), 'members_count' => $members,
                    'dependents_count' => max(0, $members - mt_rand(1, 3)),
                    'main_occupation' => $job, 'monthly_income' => $income,
                    'house_condition' => $poor ? ['layak', 'kurang_layak', 'tidak_layak'][mt_rand(0, 2)] : (mt_rand(1, 10) > 8 ? 'kurang_layak' : 'layak'),
                    'receives_aid' => $receives,
                    'aid_program' => $receives ? ['Bantuan pangan', 'BLT Dana Desa', 'Bantuan pendidikan'][mt_rand(0, 2)] : null,
                    'notes' => 'Data contoh (bukan data warga asli).',
                ]);
            }
        }
    }
}
