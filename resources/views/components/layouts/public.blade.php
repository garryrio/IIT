@props(['title' => null, 'description' => null, 'image' => null])
@php
    $site = \App\Models\SiteSetting::current();
    $pageTitle = $title ? $title.' · '.$site->village_name : $site->village_name.' — '.($site->tagline ?? 'Website Desa');
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($description ?? $site->short_description ?? ''), 160);
    $ogImage = $image ?? $site->hero_image_url;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Marga Mulya Hub">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    @if ($ogImage)<meta property="og:image" content="{{ url($ogImage) }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#1f4d2b">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">
    <script>
        // Animasi hanya aktif bila JS berjalan & pengguna tidak memilih "kurangi gerak"; fail-safe 2,5 dtk.
        if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('js-motion');
            setTimeout(function () { if (!document.documentElement.classList.contains('motion-ready')) document.documentElement.classList.remove('js-motion'); }, 2500);
        }
    </script>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="min-h-screen">
    <a href="#konten" class="fixed top-3 left-3 z-[60] -translate-y-24 rounded-full bg-forest-900 px-5 py-3 font-semibold text-white transition focus:translate-y-0">
        Lewati ke konten utama
    </a>

    @include('partials.header')

    <main id="konten" tabindex="-1" class="focus:outline-none">
        {{ $slot }}
    </main>

    @include('partials.footer', ['site' => $site])
    @include('partials.search-palette')
</body>
</html>
