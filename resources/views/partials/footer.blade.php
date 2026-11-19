@php $profile = \App\Models\VillageProfile::current(); @endphp
<footer class="mt-8 bg-forest-950 text-forest-100">
    <div class="container-page grid grid-cols-1 gap-10 py-14 md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <x-brand-mark class="size-11" />
                <span class="font-display text-2xl font-semibold text-white">{{ $site->village_name }}</span>
            </a>
            @if ($site->short_description)
                <p class="mt-4 max-w-sm text-forest-200">{{ $site->short_description }}</p>
            @endif
            @if ($site->socials())
                <ul class="mt-5 flex flex-wrap gap-2" aria-label="Media sosial desa">
                    @foreach ($site->socials() as $key => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20">
                                <x-social-icon :name="$key" class="size-5" />
                                <span class="sr-only">{{ ucfirst($key) }} (tab baru)</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <nav aria-label="Tautan footer" class="lg:col-span-3">
            <h2 class="text-sm font-bold tracking-wider text-sun-300 uppercase">Jelajahi</h2>
            <ul class="mt-4 grid grid-cols-2 gap-x-4 gap-y-1 lg:grid-cols-1">
                @foreach ([['profile', 'Profil Desa'], ['data', 'Data Desa'], ['potentials.index', 'Potensi Desa'], ['information.index', 'Informasi & Agenda'], ['gallery', 'Galeri'], ['contact', 'Kontak & Kontribusi']] as [$r, $l])
                    <li><a href="{{ route($r) }}" class="inline-flex min-h-10 items-center text-forest-100 hover:text-white hover:underline">{{ $l }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="lg:col-span-5">
            <h2 class="text-sm font-bold tracking-wider text-sun-300 uppercase">Kantor Desa</h2>
            <ul class="mt-4 space-y-3 text-forest-100">
                @if ($site->address)<li class="flex gap-3"><x-lucide-map-pin class="mt-1 size-5 shrink-0 text-sun-300" aria-hidden="true" /><span>{{ $site->address }}</span></li>@endif
                @if ($site->service_hours)<li class="flex gap-3"><x-lucide-clock class="mt-1 size-5 shrink-0 text-sun-300" aria-hidden="true" /><span>{{ $site->service_hours }}</span></li>@endif
                @if ($site->phone)<li class="flex gap-3"><x-lucide-phone class="mt-1 size-5 shrink-0 text-sun-300" aria-hidden="true" /><a href="tel:{{ preg_replace('/[^\d+]/', '', $site->phone) }}" class="hover:underline">{{ $site->phone }}</a></li>@endif
                @if ($site->email)<li class="flex gap-3"><x-lucide-mail class="mt-1 size-5 shrink-0 text-sun-300" aria-hidden="true" /><a href="mailto:{{ $site->email }}" class="break-all hover:underline">{{ $site->email }}</a></li>@endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-page flex flex-col gap-3 py-6 text-sm text-forest-200 md:flex-row md:items-start md:justify-between">
            <div class="max-w-3xl space-y-1">
                <p>&copy; {{ now()->year }} {{ $profile?->official_name ?? $site->village_name }}. Peta &copy; kontributor OpenStreetMap (ODbL).</p>
                @if ($site->demo_notice)
                    <p class="flex gap-2 text-forest-300"><x-lucide-info class="mt-0.5 size-4 shrink-0" aria-hidden="true" />{{ $site->demo_notice }}</p>
                @endif
            </div>
        </div>
    </div>
</footer>
