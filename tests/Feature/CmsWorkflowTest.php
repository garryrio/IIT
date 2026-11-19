<?php

namespace Tests\Feature;

use App\Models\InformationPost;
use App\Models\StatCategory;
use App\Models\User;
use App\Models\VillageStatistic;
use Database\Seeders\VillageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VillageSeeder::class);
    }

    private function user(string $role): User
    {
        return User::create(['name' => 'Akun '.$role, 'email' => $role.'@margamulya.test', 'password' => 'rahasia-uji-123', 'is_admin' => true, 'role' => $role]);
    }

    private function png(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('dok.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));
    }

    public function test_footer_publik_tidak_menautkan_ke_cms(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Masuk CMS')->assertDontSee(route('admin.login'), false);
        $this->get('/admin/login')->assertOk();
    }

    public function test_kategori_statistik_berupa_dropdown_dari_daftar_baku(): void
    {
        $this->assertGreaterThan(5, StatCategory::count());
        $this->actingAs($this->user('admin'));

        $this->get('/admin/kelola/statistik/baru')->assertOk()
            ->assertSee('<select id="fld-category"', false)
            ->assertSee('Penggunaan Lahan');

        // Kategori di luar daftar ditolak
        $this->post('/admin/kelola/statistik', ['category' => 'Kategori Karangan', 'indicator' => 'X', 'value' => 1, 'year' => 2025, 'chart_type' => 'bar'])
            ->assertSessionHasErrors('category');

        $this->post('/admin/kelola/statistik', ['category' => 'Penggunaan Lahan', 'indicator' => 'Tambak', 'value' => 2.5, 'unit' => 'ha', 'year' => 2025, 'chart_type' => 'donut'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('village_statistics', ['category' => 'Penggunaan Lahan', 'indicator' => 'Tambak']);
    }

    public function test_hanya_super_admin_yang_bisa_mengelola_kategori(): void
    {
        $this->actingAs($this->user('admin'));
        $this->get('/admin/kelola/kategori-statistik')->assertForbidden();
        $this->post('/admin/kelola/kategori-statistik', ['name' => 'Kesehatan'])->assertForbidden();
        $this->get('/admin')->assertDontSee('Kategori statistik');

        $this->actingAs($this->user('super_admin'));
        $this->get('/admin')->assertSee('Kategori statistik');
        $this->post('/admin/kelola/kategori-statistik', ['name' => 'Kesehatan Masyarakat', 'display_order' => 99])->assertSessionHasNoErrors();
        $this->get('/admin/kelola/statistik/baru')->assertSee('Kesehatan Masyarakat');
    }

    public function test_ganti_nama_kategori_ikut_memperbarui_indikator_dan_kategori_terpakai_tidak_bisa_dihapus(): void
    {
        $this->actingAs($this->user('super_admin'));
        $cat = StatCategory::where('name', 'Penggunaan Lahan')->firstOrFail();
        $count = VillageStatistic::where('category', 'Penggunaan Lahan')->count();

        $this->put('/admin/kelola/kategori-statistik/'.$cat->id, ['name' => 'Tata Guna Lahan', 'display_order' => 1])->assertSessionHasNoErrors();
        $this->assertSame($count, VillageStatistic::where('category', 'Tata Guna Lahan')->count());

        $this->delete('/admin/kelola/kategori-statistik/'.$cat->id)->assertSessionHasErrors('hapus');
        $this->assertDatabaseHas('stat_categories', ['id' => $cat->id]);
    }

    public function test_agenda_selesai_dijadikan_berita_wajib_foto_dan_isi(): void
    {
        $this->actingAs($this->user('admin'));
        $past = InformationPost::where('type', 'event')->whereDate('event_date', '<', today())->firstOrFail();
        $upcoming = InformationPost::upcomingEvents()->firstOrFail();

        // Agenda yang belum lewat tidak bisa dijadikan berita
        $this->post(route('admin.events.report', $upcoming->id))->assertSessionHasErrors('hapus');

        $this->get('/admin')->assertSee('Agenda selesai yang belum dijadikan berita')->assertSee($past->title);
        $this->get('/admin/kelola/informasi?type=event')->assertSee('Jadikan berita');

        $this->post(route('admin.events.report', $past->id))->assertRedirect();
        $news = $past->fresh()->report;
        $this->assertNotNull($news);
        $this->assertSame(['news', 'draft'], [$news->type, $news->status]);
        $this->assertStringContainsString(InformationPost::REPORT_PLACEHOLDER, $news->content);

        // Klik kedua tidak membuat duplikat
        $this->post(route('admin.events.report', $past->id));
        $this->assertSame(1, InformationPost::where('source_event_id', $past->id)->count());

        // Terbit tanpa foto & isi → ditolak (form maupun tombol cepat)
        $base = ['type' => 'news', 'status' => 'published', 'title' => $news->title, 'content' => $news->content];
        $this->put('/admin/kelola/informasi/'.$news->id, $base)->assertSessionHasErrors(['cover_image_url_file', 'content']);
        $this->post('/admin/kelola/informasi/'.$news->id.'/status')->assertSessionHasErrors('hapus');
        $this->assertSame('draft', $news->fresh()->status);

        // Lengkap → terbit dan tampil di halaman agenda publik
        $this->put('/admin/kelola/informasi/'.$news->id, ['content' => '<p>Kegiatan berjalan lancar dan diikuti banyak warga.</p>', 'cover_image_url_file' => $this->png()] + $base)
            ->assertSessionHasNoErrors();
        $news->refresh();
        $this->assertSame('published', $news->status);
        $this->get('/informasi/'.$news->slug)->assertOk();
        $this->get('/informasi/'.$past->slug)->assertSee('Baca laporan kegiatannya');
    }

    public function test_perintah_admin_create_menjaga_peran(): void
    {
        $this->artisan('admin:create', ['email' => 'pertama@margamulya.test', '--password' => 'rahasia-uji-123'])->assertSuccessful();
        $this->assertSame('super_admin', User::where('email', 'pertama@margamulya.test')->value('role'));

        $this->artisan('admin:create', ['email' => 'kedua@margamulya.test', '--password' => 'rahasia-uji-123'])->assertSuccessful();
        $this->assertSame('admin', User::where('email', 'kedua@margamulya.test')->value('role'));

        // Reset kata sandi super admin tidak menurunkan perannya
        $this->artisan('admin:create', ['email' => 'pertama@margamulya.test', '--password' => 'kata-sandi-baru-1'])->assertSuccessful();
        $this->assertSame('super_admin', User::where('email', 'pertama@margamulya.test')->value('role'));
    }
}
