<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\Household;
use App\Models\IncomeClass;
use App\Models\InformationPost;
use App\Models\Official;
use App\Models\Potential;
use App\Models\SiteSetting;
use App\Models\Story;
use App\Models\VillageProfile;
use App\Models\VillageStatistic;
use App\Support\IncomeAnalytics;
use App\Support\MapData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $stats = VillageStatistic::published()->where('is_highlight', true)
            ->orderByDesc('year')->orderBy('display_order')->get()->unique('indicator')->take(4)->values();

        return view('public.home', [
            'stats' => $stats,
            'announcements' => InformationPost::activeAnnouncements()->limit(3)->get(),
            'potentials' => Potential::published()->orderByDesc('is_featured')->latest('updated_at')->limit(5)->get(),
            'events' => InformationPost::upcomingEvents()->limit(4)->get(),
            'news' => InformationPost::published()->where('type', 'news')->latest('published_at')->limit(3)->get(),
            'stories' => Story::published()->orderBy('display_order')->limit(3)->get(),
            'gallery' => GalleryItem::published()->latest('event_date')->limit(5)->get(),
            'markers' => array_merge(MapData::potentials(), MapData::facilities(), MapData::events()),
            'profile' => VillageProfile::current(),
        ]);
    }

    public function profile(): View
    {
        $officials = Official::published()->orderBy('display_order')->get();

        return view('public.profile', [
            'profile' => VillageProfile::current(),
            'officials' => $officials->groupBy('unit'),
            'facilities' => Facility::published()->orderBy('display_order')->get(),
            'markers' => MapData::facilities(),
            'governance' => VillageStatistic::published()->where('category', 'Pemerintahan & Keamanan')->orderByDesc('year')->get()->unique('indicator'),
        ]);
    }

    public function data(Request $request): View
    {
        $all = VillageStatistic::published()->orderBy('display_order')->orderBy('indicator')->get();
        $years = $all->pluck('year')->unique()->sortDesc()->values();
        $categories = $all->pluck('category')->unique()->values();

        $year = (int) $request->query('tahun', $years->first());
        if (! $years->contains($year)) {
            $year = (int) $years->first();
        }
        $category = $request->query('kategori');
        if ($category && ! $categories->contains($category)) {
            $category = null;
        }

        // Kelompok grafik: tren (line) memakai seluruh tahun, selain itu memakai tahun terpilih.
        $groups = $all->groupBy('category')
            ->filter(fn ($rows, $cat) => ! $category || $cat === $category)
            ->map(function ($rows, $cat) use ($year) {
                $type = $rows->sortBy('display_order')->first()->chart_type;
                $selected = $type === 'line' ? $rows->sortBy('year') : $rows->where('year', $year);
                if ($selected->isEmpty()) {
                    return null;
                }

                return [
                    'category' => $cat,
                    'type' => $type,
                    'rows' => $selected->values(),
                    'unit' => $selected->first()->unit,
                    'source' => $selected->pluck('source')->filter()->unique()->implode('; '),
                    'years' => $selected->pluck('year')->unique()->sort()->values(),
                    'total' => $type === 'line' ? null : $selected->sum('value'),
                ];
            })->filter()->values();

        $highlights = $all->where('year', $year)->where('is_highlight', true)->unique('indicator')->values();

        $income = null;
        if (SiteSetting::current()->show_income_summary && IncomeClass::query()->exists()) {
            $analytics = new IncomeAnalytics(Household::query()->get(['id', 'monthly_income', 'members_count', 'receives_aid', 'rw']));
            $income = ['total' => $analytics->total(), 'distribution' => $analytics->distribution()];
        }

        return view('public.data', compact('groups', 'years', 'year', 'categories', 'category', 'highlights', 'income'));
    }

    public function gallery(Request $request): View
    {
        $items = GalleryItem::published()->orderByDesc('event_date')->orderByDesc('id')->get();

        return view('public.gallery', [
            'items' => $items,
            'categories' => $items->pluck('category')->unique()->mapWithKeys(fn ($c) => [$c => GalleryItem::CATEGORIES[$c] ?? $c]),
            'open' => (int) $request->query('foto'),
        ]);
    }
}
