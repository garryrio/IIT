<?php

namespace App\Http\Controllers;

use App\Models\Potential;
use App\Support\MapData;
use Illuminate\View\View;

class PotentialController extends Controller
{
    public function index(): View
    {
        $items = Potential::published()->orderByDesc('is_featured')->orderBy('name')->get();

        return view('public.potentials.index', [
            'items' => $items,
            'categories' => collect(Potential::CATEGORIES)->only($items->pluck('category')->unique()->all()),
            'markers' => MapData::potentials(),
        ]);
    }

    public function show(string $slug): View
    {
        $item = Potential::published()->where('slug', $slug)->firstOrFail();

        return view('public.potentials.show', [
            'item' => $item,
            'related' => Potential::published()->where('category', $item->category)->whereKeyNot($item->id)->limit(3)->get(),
            'markers' => MapData::potentials(),
        ]);
    }
}
