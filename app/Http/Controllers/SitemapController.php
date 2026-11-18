<?php

namespace App\Http\Controllers;

use App\Models\InformationPost;
use App\Models\Potential;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect(['home', 'profile', 'data', 'potentials.index', 'information.index', 'gallery', 'contact'])
            ->map(fn ($r) => ['loc' => route($r), 'lastmod' => null]);
        $urls = $urls->merge(Potential::published()->get(['slug', 'updated_at'])->map(fn ($p) => ['loc' => route('potentials.show', $p->slug), 'lastmod' => $p->updated_at]));
        $urls = $urls->merge(InformationPost::published()->get(['slug', 'updated_at'])->map(fn ($p) => ['loc' => route('information.show', $p->slug), 'lastmod' => $p->updated_at]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>'.e($u['loc']).'</loc>'.($u['lastmod'] ? '<lastmod>'.$u['lastmod']->toAtomString().'</lastmod>' : '').'</url>';
        }

        return response($xml.'</urlset>', 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /admin\nDisallow: /cari.json\n\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
