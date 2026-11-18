<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PotentialController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Halaman publik (9 route halaman — di bawah batas 10 halaman kompetisi):
| /, /profil, /data-desa, /potensi, /potensi/{slug}, /informasi, /informasi/{slug}, /galeri, /kontak
| Endpoint non-halaman: /api/cari (JSON), /media/{id} (gambar), /informasi/{slug}/kalender.ics, sitemap, robots.
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/profil', [PageController::class, 'profile'])->name('profile');
Route::get('/data-desa', [PageController::class, 'data'])->name('data');
Route::get('/potensi', [PotentialController::class, 'index'])->name('potentials.index');
Route::get('/potensi/{slug}', [PotentialController::class, 'show'])->name('potentials.show');
Route::get('/informasi', [InformationController::class, 'index'])->name('information.index');
Route::get('/informasi/{slug}', [InformationController::class, 'show'])->name('information.show');
Route::get('/informasi/{slug}/kalender.ics', [InformationController::class, 'ics'])->name('information.ics');
Route::get('/galeri', [PageController::class, 'gallery'])->name('gallery');
Route::get('/kontak', [ContactController::class, 'show'])->name('contact');
Route::post('/kontak/pesan', [ContactController::class, 'message'])->name('contact.message');
Route::post('/kontak/kontribusi', [ContactController::class, 'contribute'])->name('contact.contribute');

Route::get('/api/cari', SearchController::class)->middleware('throttle:60,1')->name('search');
Route::get('/media/{id}/{name?}', MediaController::class)->whereNumber('id')->name('media');
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/pesan', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('/pesan/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::patch('/pesan/{message}', [Admin\MessageController::class, 'update'])->name('messages.update');
        Route::delete('/pesan/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('/kontribusi', [Admin\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/kontribusi/{submission}', [Admin\SubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('/kontribusi/{submission}', [Admin\SubmissionController::class, 'review'])->name('submissions.review');
        Route::post('/kontribusi/{submission}/jadikan-berita', [Admin\SubmissionController::class, 'convert'])->name('submissions.convert');
        Route::delete('/kontribusi/{submission}', [Admin\SubmissionController::class, 'destroy'])->name('submissions.destroy');

        Route::get('/kesejahteraan', [Admin\IncomeController::class, 'index'])->name('income.index');
        Route::get('/kesejahteraan/ekspor', [Admin\IncomeController::class, 'export'])->name('income.export');

        // CMS generik untuk seluruh modul konten
        Route::get('/kelola/{resource}', [Admin\ResourceController::class, 'index'])->name('resource.index');
        Route::get('/kelola/{resource}/ubah', [Admin\ResourceController::class, 'singleton'])->name('resource.singleton');
        Route::put('/kelola/{resource}/ubah', [Admin\ResourceController::class, 'saveSingleton'])->name('resource.singleton.save');
        Route::get('/kelola/{resource}/baru', [Admin\ResourceController::class, 'create'])->name('resource.create');
        Route::post('/kelola/{resource}', [Admin\ResourceController::class, 'store'])->name('resource.store');
        Route::get('/kelola/{resource}/{id}', [Admin\ResourceController::class, 'edit'])->whereNumber('id')->name('resource.edit');
        Route::put('/kelola/{resource}/{id}', [Admin\ResourceController::class, 'update'])->whereNumber('id')->name('resource.update');
        Route::delete('/kelola/{resource}/{id}', [Admin\ResourceController::class, 'destroy'])->whereNumber('id')->name('resource.destroy');
        Route::post('/kelola/{resource}/{id}/status', [Admin\ResourceController::class, 'toggleStatus'])->whereNumber('id')->name('resource.status');
    });
});
