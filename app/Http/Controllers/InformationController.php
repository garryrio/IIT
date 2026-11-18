<?php

namespace App\Http\Controllers;

use App\Models\InformationPost;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InformationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'jenis' => ['nullable', 'in:'.implode(',', array_keys(InformationPost::TYPES))],
            'q' => ['nullable', 'string', 'max:100'],
            'dari' => ['nullable', 'date_format:Y-m-d'],
            'sampai' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:aktif,arsip'],
        ]);
        $type = $filters['jenis'] ?? null;

        $query = InformationPost::published()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($filters['q'] ?? null, function ($q, $term) {
                foreach (preg_split('/\s+/', trim($term)) as $word) {
                    $like = '%'.addcslashes($word, '%_\\').'%';
                    $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('content', 'like', $like)->orWhere('location', 'like', $like));
                }
            })
            ->when($filters['dari'] ?? null, fn ($q, $d) => $q->whereRaw('DATE(COALESCE(event_date, published_at)) >= ?', [$d]))
            ->when($filters['sampai'] ?? null, fn ($q, $d) => $q->whereRaw('DATE(COALESCE(event_date, published_at)) <= ?', [$d]));

        $today = Carbon::today()->toDateString();
        if (($filters['status'] ?? null) === 'aktif') {
            $query->where(fn ($w) => $w
                ->whereIn('type', ['news', 'service'])
                ->orWhere(fn ($a) => $a->where('type', 'announcement')->where(fn ($e) => $e->whereNull('end_date')->orWhere('end_date', '>=', $today)))
                ->orWhere(fn ($e) => $e->where('type', 'event')->where('event_date', '>=', $today)));
        } elseif (($filters['status'] ?? null) === 'arsip') {
            $query->where(fn ($w) => $w
                ->where(fn ($a) => $a->where('type', 'announcement')->where('end_date', '<', $today))
                ->orWhere(fn ($e) => $e->where('type', 'event')->where('event_date', '<', $today)));
        }

        $posts = $query->orderByRaw('COALESCE(event_date, DATE(published_at)) DESC')->orderByDesc('id')->paginate(9)->withQueryString();

        $calendarEvents = InformationPost::published()->where('type', 'event')->whereNotNull('event_date')
            ->orderBy('event_date')->get()->map(fn (InformationPost $p) => [
                'date' => $p->event_date->toDateString(),
                'title' => $p->title,
                'time' => $p->timeRange(),
                'location' => $p->location,
                'url' => $p->url(),
            ])->values();

        return view('public.information.index', [
            'posts' => $posts,
            'filters' => $filters,
            'type' => $type,
            'priority' => InformationPost::activeAnnouncements()->where('priority', '>', 0)->limit(2)->get(),
            'calendarEvents' => $calendarEvents,
            'upcoming' => InformationPost::upcomingEvents()->limit(5)->get(),
            'view' => $request->query('tampilan') === 'kalender' ? 'kalender' : 'daftar',
        ]);
    }

    public function show(string $slug): View
    {
        $post = InformationPost::published()->where('slug', $slug)->firstOrFail();

        return view('public.information.show', [
            'post' => $post,
            'related' => InformationPost::published()->where('type', $post->type)->whereKeyNot($post->id)->latest('published_at')->limit(3)->get(),
            'calendarUrl' => $post->type === 'event' && $post->event_date ? $this->googleCalendarUrl($post) : null,
        ]);
    }

    /** Unduh agenda sebagai file .ics (bisa dimasukkan ke kalender ponsel). */
    public function ics(string $slug): Response
    {
        $post = InformationPost::published()->where('type', 'event')->where('slug', $slug)->whereNotNull('event_date')->firstOrFail();
        [$start, $end, $allDay] = $this->eventRange($post);
        $esc = fn ($s) => str_replace(['\\', ';', ',', "\n"], ['\\\\', '\;', '\,', '\n'], (string) $s);
        $fmt = $allDay ? 'Ymd' : 'Ymd\THis';
        $lines = [
            'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Marga Mulya Hub//ID', 'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:agenda-'.$post->id.'@'.parse_url(config('app.url'), PHP_URL_HOST),
            'DTSTAMP:'.now('UTC')->format('Ymd\THis\Z'),
            ($allDay ? 'DTSTART;VALUE=DATE:' : 'DTSTART;TZID=Asia/Jakarta:').$start->format($fmt),
            ($allDay ? 'DTEND;VALUE=DATE:' : 'DTEND;TZID=Asia/Jakarta:').$end->format($fmt),
            'SUMMARY:'.$esc($post->title),
            'DESCRIPTION:'.$esc(Str::limit(strip_tags($post->excerpt ?: $post->content), 500).' '.$post->url()),
            'LOCATION:'.$esc($post->location),
            'URL:'.$post->url(),
            'END:VEVENT', 'END:VCALENDAR',
        ];

        return response(implode("\r\n", $lines)."\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="agenda-'.$post->slug.'.ics"',
        ]);
    }

    private function googleCalendarUrl(InformationPost $post): string
    {
        [$start, $end, $allDay] = $this->eventRange($post);
        $fmt = $allDay ? 'Ymd' : 'Ymd\THis';

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $post->title,
            'dates' => $start->format($fmt).'/'.$end->format($fmt),
            'ctz' => 'Asia/Jakarta',
            'details' => Str::limit(strip_tags($post->excerpt ?: $post->content), 300)."\n".$post->url(),
            'location' => $post->location ?: SiteSetting::current()->village_name,
        ]);
    }

    /** @return array{0:Carbon,1:Carbon,2:bool} */
    private function eventRange(InformationPost $post): array
    {
        $date = $post->event_date->toDateString();
        if (! $post->event_time) {
            return [Carbon::parse($date), Carbon::parse($date)->addDay(), true];
        }
        $start = Carbon::parse($date.' '.$post->event_time, 'Asia/Jakarta');
        $end = $post->event_end_time ? Carbon::parse($date.' '.$post->event_end_time, 'Asia/Jakarta') : $start->copy()->addHours(2);

        return [$start, $end, false];
    }
}
