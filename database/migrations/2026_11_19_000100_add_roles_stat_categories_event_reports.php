<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peran akun CMS: super_admin (boleh mengelola kategori statistik) atau admin.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('is_admin');
        });
        // Akun admin yang sudah ada sebelum fitur peran dibuat dijadikan super admin.
        DB::table('users')->where('is_admin', true)->update(['role' => 'super_admin']);

        // Daftar kategori statistik yang baku (dipilih lewat dropdown).
        Schema::create('stat_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });
        $existing = DB::table('village_statistics')
            ->select('category', DB::raw('MIN(display_order) as ord'))
            ->groupBy('category')->orderBy('ord')->get();
        foreach ($existing as $i => $row) {
            DB::table('stat_categories')->insert([
                'name' => $row->category, 'display_order' => $i + 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Berita yang dibuat dari agenda yang sudah selesai.
        Schema::table('information_posts', function (Blueprint $table) {
            $table->foreignId('source_event_id')->nullable()->after('organizer')
                ->constrained('information_posts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('information_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_event_id');
        });
        Schema::dropIfExists('stat_categories');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
