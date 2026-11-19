<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitizenSubmission;
use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\Household;
use App\Models\InformationPost;
use App\Models\Potential;
use App\Models\VillageStatistic;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $counts = [
            ['label' => 'Berita', 'value' => InformationPost::where('type', 'news')->count(), 'icon' => 'newspaper', 'url' => route('admin.resource.index', ['informasi', 'type' => 'news'])],
            ['label' => 'Pengumuman aktif', 'value' => InformationPost::activeAnnouncements()->count(), 'icon' => 'megaphone', 'url' => route('admin.resource.index', ['informasi', 'type' => 'announcement'])],
            ['label' => 'Agenda mendatang', 'value' => InformationPost::upcomingEvents()->count(), 'icon' => 'calendar-days', 'url' => route('admin.resource.index', ['informasi', 'type' => 'event'])],
            ['label' => 'Potensi desa', 'value' => Potential::count(), 'icon' => 'sprout', 'url' => route('admin.resource.index', 'potensi')],
            ['label' => 'Foto galeri', 'value' => GalleryItem::count(), 'icon' => 'images', 'url' => route('admin.resource.index', 'galeri')],
            ['label' => 'Indikator statistik', 'value' => VillageStatistic::count(), 'icon' => 'bar-chart-3', 'url' => route('admin.resource.index', 'statistik')],
            ['label' => 'Kontribusi menunggu', 'value' => CitizenSubmission::where('status', 'pending')->count(), 'icon' => 'hand-heart', 'url' => route('admin.submissions.index', ['status' => 'pending']), 'alert' => true],
            ['label' => 'Pesan belum dibaca', 'value' => ContactMessage::where('status', 'unread')->count(), 'icon' => 'mail', 'url' => route('admin.messages.index', ['status' => 'unread']), 'alert' => true],
        ];

        $recent = collect()
            ->merge(InformationPost::latest('updated_at')->limit(6)->get()->map(fn ($p) => ['type' => $p->typeLabel(), 'title' => $p->title, 'status' => $p->status, 'at' => $p->updated_at, 'url' => route('admin.resource.edit', ['informasi', $p->id])]))
            ->merge(Potential::latest('updated_at')->limit(4)->get()->map(fn ($p) => ['type' => 'Potensi', 'title' => $p->name, 'status' => $p->status, 'at' => $p->updated_at, 'url' => route('admin.resource.edit', ['potensi', $p->id])]))
            ->merge(GalleryItem::latest('updated_at')->limit(4)->get()->map(fn ($p) => ['type' => 'Galeri', 'title' => $p->title, 'status' => $p->status, 'at' => $p->updated_at, 'url' => route('admin.resource.edit', ['galeri', $p->id])]))
            ->merge(CitizenSubmission::latest('updated_at')->limit(4)->get()->map(fn ($s) => ['type' => 'Kontribusi', 'title' => $s->title, 'status' => $s->status, 'at' => $s->updated_at, 'url' => route('admin.submissions.show', $s)]))
            ->sortByDesc('at')->take(10)->values();

        return view('admin.dashboard', [
            'counts' => $counts,
            'announcements' => InformationPost::activeAnnouncements()->limit(5)->get(),
            'events' => InformationPost::upcomingEvents()->limit(5)->get(),
            'pending' => CitizenSubmission::where('status', 'pending')->latest()->limit(5)->get(),
            // Agenda yang sudah lewat tetapi belum dijadikan berita laporan kegiatan.
            'pastEvents' => InformationPost::where('type', 'event')->whereDate('event_date', '<', today())
                ->doesntHave('report')->orderByDesc('event_date')->limit(5)->get(),
            'recent' => $recent,
            'households' => Household::count(),
        ]);
    }
}
