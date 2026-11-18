@php
    $allOfficials = $officials->flatten();
    $byGender = $allOfficials->groupBy('gender')->map->count();
    $byEducation = $allOfficials->groupBy('education')->map->count();
@endphp
<x-layouts.public title="Profil Desa" :description="$profile?->description" :image="$profile?->image_url">
    <x-page-hero :title="$profile?->official_name ?? 'Profil Desa'" eyebrow="Profil desa" :lead="$profile?->description" :crumbs="['Profil Desa' => null]" />

    @if (! $profile)
        <div class="container-page section"><x-empty-state icon="landmark" title="Profil desa belum diisi" text="Admin dapat mengisi profil melalui CMS." /></div>
    @else
        {{-- Navigasi bagian --}}
        <nav aria-label="Bagian profil" class="sticky top-[4.5rem] z-30 border-b border-line bg-cream/95 backdrop-blur">
            <ul class="container-page flex gap-1 overflow-x-auto py-2 [scrollbar-width:none]">
                @foreach (['identitas' => 'Identitas', 'sejarah' => 'Sejarah', 'visi-misi' => 'Visi & Misi', 'geografis' => 'Geografis', 'pemerintahan' => 'Pemerintahan', 'fasilitas' => 'Fasilitas'] as $id => $l)
                    <li><a href="#{{ $id }}" class="inline-flex min-h-11 items-center rounded-full px-4 text-sm font-semibold whitespace-nowrap text-ink-soft hover:bg-forest-50 hover:text-forest-900">{{ $l }}</a></li>
                @endforeach
            </ul>
        </nav>

        <section id="identitas" class="section" aria-labelledby="identitas-judul">
            <div class="container-page grid grid-cols-1 items-start gap-10 lg:grid-cols-2">
                @if ($profile->image_url)
                    <img src="{{ $profile->image_url }}" alt="Gambar {{ $profile->official_name }}" class="aspect-[4/3] w-full rounded-[2rem] object-cover shadow-[var(--shadow-lift)]" width="1200" height="900" data-reveal>
                @endif
                <div data-reveal>
                    <h2 id="identitas-judul" class="section-title">Identitas desa</h2>
                    <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        @foreach ([['Nama resmi', $profile->official_name], ['Kode desa', $profile->village_code], ['Kecamatan', $profile->district], ['Kabupaten/Kota', $profile->regency], ['Provinsi', $profile->province], ['Kode pos', $profile->postal_code], ['Luas wilayah', $profile->area_hectare ? number_format($profile->area_hectare, 1, ',', '.').' ha' : null], ['Wilayah administratif', $profile->rw_count ? $profile->rw_count.' RW · '.$profile->rt_count.' RT' : null]] as [$k, $v])
                            @if (filled($v))
                                <div class="rounded-2xl border border-line bg-paper p-4"><dt class="text-sm font-semibold text-ink-soft">{{ $k }}</dt><dd class="mt-1 text-lg font-bold">{{ $v }}</dd></div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            </div>
        </section>

        @if ($profile->history)
            <section id="sejarah" class="section bg-paper" aria-labelledby="sejarah-judul">
                <div class="container-page grid grid-cols-1 gap-8 lg:grid-cols-12">
                    <h2 id="sejarah-judul" class="section-title lg:col-span-4">Sejarah desa</h2>
                    <div class="prose-village text-lg lg:col-span-8" data-reveal>{!! \App\Support\Html::clean($profile->history) !!}</div>
                </div>
            </section>
        @endif

        <section id="visi-misi" class="section" aria-labelledby="visi-judul">
            <div class="container-page">
                <h2 id="visi-judul" class="section-title">Visi & misi</h2>
                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-5">
                    @if ($profile->vision)
                        <div class="rounded-[2rem] bg-forest-800 p-8 text-white lg:col-span-2" data-reveal>
                            <p class="eyebrow text-sun-300">Visi</p>
                            <p class="mt-3 font-display text-2xl leading-snug font-medium">“{{ $profile->vision }}”</p>
                        </div>
                    @endif
                    @if ($missions = $profile->missionList())
                        <ol class="grid gap-3 lg:col-span-3" data-stagger>
                            @foreach ($missions as $i => $m)
                                <li class="card flex gap-4 p-5"><span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-sun-200 font-bold text-ink">{{ $i + 1 }}</span><span class="pt-1.5">{{ $m }}</span></li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </section>

        <section id="geografis" class="section bg-leaf-100/60" aria-labelledby="geo-judul">
            <div class="container-page">
                <h2 id="geo-judul" class="section-title">Kondisi geografis</h2>
                @if ($profile->geography_notes)<p class="section-lead">{{ $profile->geography_notes }}</p>@endif
                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
                    @foreach (['Utara' => $profile->border_north, 'Timur' => $profile->border_east, 'Selatan' => $profile->border_south, 'Barat' => $profile->border_west] as $dir => $b)
                        @if ($b)
                            <div class="card p-5"><p class="text-sm font-bold tracking-wider text-forest-700 uppercase">Batas {{ $dir }}</p><p class="mt-2 font-semibold">{{ $b }}</p></div>
                        @endif
                    @endforeach
                </div>
                <p class="mt-6"><a href="{{ route('data', ['kategori' => 'Penggunaan Lahan']) }}" class="link">Lihat data penggunaan lahan →</a></p>
            </div>
        </section>

        <section id="pemerintahan" class="section" aria-labelledby="pemerintahan-judul">
            <div class="container-page">
                <h2 id="pemerintahan-judul" class="section-title">Pemerintahan desa</h2>
                <p class="section-lead">Struktur organisasi dan aparat yang melayani warga.</p>

                @if ($allOfficials->isNotEmpty())
                    <div class="mt-6 flex flex-wrap gap-2 text-sm">
                        <span class="badge bg-forest-100 px-3 py-1 text-forest-900">{{ $allOfficials->count() }} aparat</span>
                        @foreach ($byGender as $g => $c)<span class="badge bg-paper px-3 py-1 ring-1 ring-line">{{ $g ?: 'Lainnya' }}: {{ $c }}</span>@endforeach
                        @foreach ($byEducation as $e => $c)<span class="badge bg-paper px-3 py-1 ring-1 ring-line">{{ $e ?: '—' }}: {{ $c }}</span>@endforeach
                    </div>
                @endif

                @forelse ($officials as $unit => $people)
                    <h3 class="mt-10 text-xl font-bold">{{ $unit }}</h3>
                    <ul class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
                        @foreach ($people as $o)
                            <li class="card flex items-center gap-4 p-4">
                                @if ($o->photo_url)
                                    <img src="{{ $o->photo_url }}" alt="Foto {{ $o->name }}" class="size-16 rounded-2xl object-cover" loading="lazy">
                                @else
                                    <span class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-forest-100 font-display text-2xl font-semibold text-forest-800" aria-hidden="true">{{ collect(explode(' ', $o->name))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
                                @endif
                                <div><p class="font-bold">{{ $o->name }}</p><p class="text-sm text-ink-soft">{{ $o->position }}</p></div>
                            </li>
                        @endforeach
                    </ul>
                @empty
                    <x-empty-state class="mt-6" icon="users" title="Data aparat belum tersedia" />
                @endforelse

                @if ($profile->structure_image_url)
                    <figure class="mt-10"><img src="{{ $profile->structure_image_url }}" alt="Bagan struktur organisasi pemerintah desa" class="w-full rounded-2xl border border-line" loading="lazy"><figcaption class="mt-2 text-sm text-ink-soft">Bagan struktur organisasi</figcaption></figure>
                @endif

                @if ($governance->isNotEmpty())
                    <h3 class="mt-12 text-xl font-bold">Keamanan & wilayah</h3>
                    <dl class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-5">
                        @foreach ($governance as $g)
                            <div class="rounded-2xl border border-line bg-paper p-4"><dt class="text-sm text-ink-soft">{{ $g->indicator }}</dt><dd class="mt-1 font-display text-3xl font-semibold text-forest-900">{{ $g->formattedValue() }}</dd></div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </section>

        <section id="fasilitas" class="section bg-paper" aria-labelledby="fasilitas-judul">
            <div class="container-page">
                <h2 id="fasilitas-judul" class="section-title">Fasilitas umum</h2>
                @if ($facilities->isEmpty())
                    <x-empty-state class="mt-6" icon="building-2" title="Data fasilitas belum tersedia" />
                @else
                    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-5">
                        <ul class="grid content-start gap-3 lg:col-span-2">
                            @foreach ($facilities as $f)
                                <li class="flex gap-3 rounded-2xl border border-line bg-cream p-4">
                                    <span class="mt-1 size-3 shrink-0 rounded-full" style="background: {{ \App\Models\Facility::COLORS[$f->type] ?? '#1f4d2b' }}"></span>
                                    <div><p class="font-bold">{{ $f->name }}</p><p class="text-sm font-semibold text-forest-700">{{ $f->typeLabel() }}</p>@if ($f->description)<p class="mt-1 text-sm text-ink-soft">{{ $f->description }}</p>@endif</div>
                                </li>
                            @endforeach
                        </ul>
                        <x-village-map class="lg:col-span-3" :markers="$markers" :layers="['fasilitas' => 'Fasilitas']" height="h-[460px]" label="Peta fasilitas umum desa" :list="false" />
                    </div>
                @endif
            </div>
        </section>
    @endif
</x-layouts.public>
