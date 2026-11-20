<?php

namespace Tests\Feature;

use App\Models\CitizenSubmission;
use App\Models\Household;
use App\Models\IncomeClass;
use App\Models\InformationPost;
use App\Models\Potential;
use App\Models\User;
use App\Models\VillageStatistic;
use Database\Seeders\VillageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VillageHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VillageSeeder::class);
    }

    private function admin(): User
    {
        return User::create(['name' => 'Admin Uji', 'email' => 'uji@margamulya.test', 'password' => 'rahasia-uji-123', 'is_admin' => true]);
    }

    public function test_semua_halaman_publik_dapat_diakses(): void
    {
        foreach (['/', '/profil', '/data-desa', '/potensi', '/informasi', '/informasi?tampilan=kalender', '/galeri', '/kontak', '/sitemap.xml', '/robots.txt'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/potensi/'.Potential::first()->slug)->assertOk();
        $this->get('/informasi/'.InformationPost::published()->first()->slug)->assertOk();
    }

    public function test_jumlah_route_halaman_publik_maksimal_sepuluh(): void
    {
        $pages = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($r) => in_array('GET', $r->methods()))
            ->reject(fn ($r) => str_starts_with($r->uri(), 'admin') || $r->uri() === 'cari.json'
                || str_starts_with($r->uri(), 'media/') || str_ends_with($r->uri(), '.ics')
                || in_array($r->uri(), ['sitemap.xml', 'robots.txt', 'up', 'storage/{path}']));
        $this->assertLessThanOrEqual(10, $pages->count(), $pages->map->uri()->implode(', '));
    }

    public function test_konten_draf_tidak_terlihat_publik(): void
    {
        $draft = InformationPost::where('status', 'draft')->firstOrFail();
        $this->get('/informasi/'.$draft->slug)->assertNotFound();
        $this->get('/informasi')->assertDontSee($draft->title);
    }

    public function test_pengumuman_kedaluwarsa_tidak_aktif(): void
    {
        $expired = InformationPost::where('type', 'announcement')->whereDate('end_date', '<', today())->firstOrFail();
        $this->assertFalse(InformationPost::activeAnnouncements()->pluck('id')->contains($expired->id));
        $this->get('/')->assertDontSee($expired->title);
    }

    public function test_route_admin_terlindungi(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/kelola/informasi')->assertRedirect('/admin/login');
        $this->get('/admin/kesejahteraan')->assertRedirect('/admin/login');
    }

    public function test_pengguna_non_admin_ditolak(): void
    {
        $user = User::create(['name' => 'Warga', 'email' => 'warga@margamulya.test', 'password' => 'rahasia-uji-123', 'is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_crud_konten_tampil_di_publik(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/kelola/informasi', [
            'type' => 'news', 'status' => 'published', 'title' => 'Berita Uji Otomatis',
            'excerpt' => 'Ringkasan', 'content' => '<p>Halo</p><script>alert(1)</script>',
            // PNG 1x1 asli (tanpa bergantung pada ekstensi GD)
            'cover_image_url_file' => UploadedFile::fake()->createWithContent('foto.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==')),
        ])->assertRedirect();

        $post = InformationPost::where('title', 'Berita Uji Otomatis')->firstOrFail();
        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringStartsWith('/media/', $post->cover_image_url);
        $this->get('/informasi/berita-uji-otomatis')->assertOk()->assertSee('Berita Uji Otomatis');
        $this->get($post->cover_image_url)->assertOk();

        $this->put('/admin/kelola/informasi/'.$post->id, ['type' => 'news', 'status' => 'draft', 'title' => 'Berita Uji Diubah'])->assertRedirect();
        $this->get('/informasi/'.$post->fresh()->slug)->assertNotFound();

        $this->delete('/admin/kelola/informasi/'.$post->id)->assertRedirect();
        $this->assertDatabaseMissing('information_posts', ['id' => $post->id]);
    }

    public function test_validasi_form_admin(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/kelola/informasi', ['type' => 'tidak-valid', 'status' => 'published'])
            ->assertSessionHasErrors(['type', 'title']);
    }

    public function test_kontribusi_warga_masuk_moderasi_dan_kontak_tidak_dipublikasikan(): void
    {
        $this->post('/kontak/kontribusi', [
            '_ts' => time() - 10, 'submitter_name' => 'Pengirim Rahasia', 'contact' => '0800-1111-2222',
            'category' => 'lingkungan', 'title' => 'Sampah menumpuk', 'description' => 'Sampah menumpuk di dekat jembatan kecil sejak tiga hari.',
        ])->assertRedirect();

        $s = CitizenSubmission::where('title', 'Sampah menumpuk')->firstOrFail();
        $this->assertSame('pending', $s->status);

        $this->actingAs($this->admin());
        $this->patch('/admin/kontribusi/'.$s->id, ['status' => 'approved'])->assertRedirect();
        $this->post('/admin/kontribusi/'.$s->id.'/jadikan-berita')->assertRedirect();
        $post = $s->fresh()->publishedPost;
        $this->assertSame('draft', $post->status);
        $this->assertStringNotContainsString('Pengirim Rahasia', $post->content);
        $this->assertStringNotContainsString('0800-1111-2222', $post->content);
    }

    public function test_honeypot_menolak_spam(): void
    {
        $this->post('/kontak/pesan', ['_ts' => time() - 10, 'website' => 'http://spam.example', 'name' => 'Bot', 'contact' => 'bot@example.com', 'subject' => 'Spam', 'message' => 'Pesan spam panjang sekali'])
            ->assertSessionHasErrors('form');
        $this->assertDatabaseMissing('contact_messages', ['name' => 'Bot']);
    }

    public function test_pencarian_mengelompokkan_hasil(): void
    {
        $json = $this->getJson('/cari.json?q=posyandu')->assertOk()->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertNotEmpty(collect($json['groups'])->pluck('key')->intersect(['event', 'facility']));
    }

    public function test_klasifikasi_penghasilan_dan_data_kk_tidak_publik(): void
    {
        $this->assertSame('Sangat rendah', IncomeClass::classify(450000.5)->name);
        $this->assertSame('Rendah', IncomeClass::classify(450001)->name);
        $this->assertSame('Atas', IncomeClass::classify(9_000_000)->name);

        $kk = Household::firstOrFail();
        $this->get('/data-desa')->assertOk()->assertDontSee($kk->head_name)->assertDontSee($kk->household_code);
    }

    public function test_data_tahun_baru_satu_kategori_tidak_menghilangkan_grafik_lain(): void
    {
        // Admin menambah data tahun baru hanya untuk satu kategori
        VillageStatistic::create(['category' => 'Tren Jumlah Penduduk', 'indicator' => 'Jumlah penduduk', 'value' => 6900,
            'unit' => 'jiwa', 'year' => 2026, 'chart_type' => 'line', 'is_published' => true]);

        // Default (data terbaru): kategori lain tetap tampil dengan tahun terbarunya masing-masing
        $this->get('/data-desa')->assertOk()
            ->assertSee('Penggunaan Lahan')->assertSee('Mata Pencaharian Pokok')->assertSee('Data terbaru');

        // Filter eksplisit 2026: hanya kategori yang punya data 2026 (dan tren) yang tampil
        $this->get('/data-desa?tahun=2026')->assertOk()->assertDontSee('Grafik Penggunaan Lahan');
    }
}
