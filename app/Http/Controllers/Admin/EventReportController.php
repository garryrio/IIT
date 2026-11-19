<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/** Menaikkan agenda yang sudah selesai menjadi draf berita (laporan kegiatan). */
class EventReportController extends Controller
{
    public function __invoke(InformationPost $event): RedirectResponse
    {
        abort_unless($event->type === 'event', 404);
        if (! $event->canBecomeNews()) {
            return back()->withErrors(['hapus' => 'Agenda "'.$event->title.'" belum selesai, jadi belum dapat dijadikan berita.']);
        }

        // Sudah pernah dibuat: langsung buka beritanya, jangan membuat duplikat.
        if ($existing = $event->report) {
            return redirect()->route('admin.resource.edit', ['informasi', $existing->id, 'type' => 'news'])
                ->with('success', 'Agenda ini sudah pernah dijadikan berita. Silakan lanjutkan penyuntingan.');
        }

        $date = $event->event_date->translatedFormat('l, d F Y');
        $details = collect([
            'pada '.$date,
            $event->timeRange() ? 'pukul '.$event->timeRange() : null,
            $event->location ? 'di '.$event->location : null,
        ])->filter()->implode(' ');

        $base = Str::slug($event->title) ?: 'laporan-kegiatan';
        $slug = $base;
        $i = 2;
        while (InformationPost::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $post = InformationPost::create([
            'type' => 'news',
            'status' => 'draft',
            'source_event_id' => $event->id,
            'title' => $event->title,
            'slug' => $slug,
            'excerpt' => Str::limit('Kegiatan '.$event->title.' telah dilaksanakan '.$details.'.', 400),
            'content' => '<p>Kegiatan <strong>'.e($event->title).'</strong> telah dilaksanakan '.e($details)
                .($event->organizer ? ' dan diselenggarakan oleh '.e($event->organizer) : '').'.</p>'
                .'<p>'.InformationPost::REPORT_PLACEHOLDER.'</p>',
            'cover_image_url' => null, // admin wajib mengunggah foto dokumentasi sebelum terbit
            'location' => $event->location,
            'organizer' => $event->organizer,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
        ]);

        return redirect()->route('admin.resource.edit', ['informasi', $post->id, 'type' => 'news'])
            ->with('success', 'Draf berita dibuat dari agenda. Unggah foto dokumentasi dan lengkapi isinya, lalu terbitkan.');
    }
}
