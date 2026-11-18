<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Penyimpanan gambar permanen di database (aman untuk Vercel yang filesystem-nya sementara).
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('mime', 100);
            $table->unsignedInteger('size');
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->string('disk_path')->nullable();
            $table->timestamps();
        });
        // LONGBLOB khusus MySQL/MariaDB
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'])) {
            Schema::getConnection()->statement('ALTER TABLE media ADD data LONGBLOB NULL');
        } else {
            Schema::table('media', fn (Blueprint $t) => $t->binary('data')->nullable());
        }

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('village_name');
            $table->string('tagline')->nullable();
            $table->text('short_description')->nullable();
            $table->string('hero_title');
            $table->text('hero_description')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->string('hero_cta_primary')->nullable();
            $table->string('hero_cta_secondary')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('whatsapp', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('service_hours')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('social_links')->nullable();
            $table->string('contribution_cta_title')->nullable();
            $table->text('contribution_cta_text')->nullable();
            $table->text('demo_notice')->nullable();
            $table->boolean('show_income_summary')->default(true);
            $table->timestamps();
        });

        Schema::create('village_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('official_name');
            $table->string('village_code', 30)->nullable();
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('description')->nullable();
            $table->longText('history')->nullable();
            $table->text('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->decimal('area_hectare', 10, 2)->nullable();
            $table->string('border_north')->nullable();
            $table->string('border_east')->nullable();
            $table->string('border_south')->nullable();
            $table->string('border_west')->nullable();
            $table->text('geography_notes')->nullable();
            $table->unsignedSmallInteger('rw_count')->nullable();
            $table->unsignedSmallInteger('rt_count')->nullable();
            $table->string('image_url')->nullable();
            $table->string('structure_image_url')->nullable();
            $table->timestamps();
        });

        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('unit')->default('Pemerintah Desa');
            $table->string('employment_status')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('photo_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('status', 20)->default('published')->index();
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 40)->index();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('status', 20)->default('published')->index();
            $table->timestamps();
        });

        Schema::create('village_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('category', 80)->index();
            $table->string('indicator');
            $table->decimal('value', 14, 2);
            $table->string('unit', 30)->nullable();
            $table->unsignedSmallInteger('year')->index();
            $table->string('chart_type', 20)->default('bar');
            $table->string('source')->nullable();
            $table->boolean('is_highlight')->default(false);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
            $table->unique(['category', 'indicator', 'year']);
        });

        Schema::create('information_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 20)->index(); // news, announcement, event, service
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->date('event_date')->nullable()->index();
            $table->time('event_time')->nullable();
            $table->time('event_end_time')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('organizer')->nullable();
            $table->unsignedTinyInteger('priority')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('potentials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 40)->index();
            $table->string('summary', 300)->nullable();
            $table->longText('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('owner_or_manager')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('contact', 60)->nullable();
            $table->string('whatsapp', 40)->nullable();
            $table->string('operating_hours')->nullable();
            $table->string('price_range')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->string('alt_text');
            $table->string('category', 40)->index();
            $table->date('event_date')->nullable();
            $table->string('location')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote');
            $table->string('photo_url')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('unread')->index();
            $table->timestamps();
        });

        Schema::create('citizen_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submitter_name');
            $table->string('contact')->nullable();
            $table->string('category', 40)->index();
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('published_post_id')->nullable()->constrained('information_posts')->nullOnDelete();
            $table->timestamps();
        });

        // Fitur kelas penghasilan (khusus admin)
        Schema::create('income_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('min_per_capita')->default(0);
            $table->unsignedBigInteger('max_per_capita')->nullable();
            $table->unsignedTinyInteger('aid_priority')->default(0); // 1 = paling diprioritaskan, 0 = tidak
            $table->string('color', 20)->default('#2f6b3a');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('household_code', 30)->unique();
            $table->string('head_name');
            $table->unsignedTinyInteger('rw');
            $table->unsignedTinyInteger('rt');
            $table->unsignedTinyInteger('members_count');
            $table->unsignedTinyInteger('dependents_count')->default(0);
            $table->string('main_occupation', 60)->nullable();
            $table->unsignedBigInteger('monthly_income');
            $table->string('house_condition', 30)->nullable();
            $table->boolean('receives_aid')->default(false)->index();
            $table->string('aid_program')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['rw', 'rt']);
        });
    }

    public function down(): void
    {
        foreach (['households', 'income_classes', 'citizen_submissions', 'contact_messages', 'stories', 'gallery_items',
            'potentials', 'information_posts', 'village_statistics', 'facilities', 'officials', 'village_profiles',
            'site_settings', 'media'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
