@php
    $site = \App\Models\SiteSetting::current();
    $wa = $site->whatsappLink('Halo admin Desa Marga Mulya');
@endphp
<x-layouts.public title="Kontak & Kontribusi Warga" description="Hubungi kantor Desa Marga Mulya atau kirim kontribusi informasi untuk desa.">
    <x-page-hero title="Kontak & Kontribusi" eyebrow="Hubungi desa" lead="Sampaikan pertanyaan, aspirasi, atau kirim kabar terbaru dari lingkungan Anda. Setiap kontribusi diperiksa admin sebelum dipublikasikan." :crumbs="['Kontak' => null]" />

    <div class="container-page section pt-10">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4" data-stagger>
            @foreach ([
                ['map-pin', 'Alamat kantor', $site->address, null],
                ['phone', 'Telepon', $site->phone, $site->phone ? 'tel:'.preg_replace('/[^\d+]/', '', $site->phone) : null],
                ['mail', 'Email', $site->email, $site->email ? 'mailto:'.$site->email : null],
                ['clock', 'Jam layanan', $site->service_hours, null],
            ] as [$icon, $label, $value, $href])
                @if ($value)
                    <div class="card p-5">
                        <span class="flex size-11 items-center justify-center rounded-2xl bg-forest-50 text-forest-800"><x-dynamic-component :component="'lucide-'.$icon" class="size-5" aria-hidden="true" /></span>
                        <h2 class="mt-3 text-sm font-semibold text-ink-soft">{{ $label }}</h2>
                        @if ($href)<a href="{{ $href }}" class="mt-1 block font-bold break-words hover:underline">{{ $value }}</a>@else<p class="mt-1 font-bold">{{ $value }}</p>@endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            @if ($wa)<a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-primary"><x-lucide-message-circle class="size-5" aria-hidden="true" /> Chat WhatsApp <span class="sr-only">(tab baru)</span></a>@endif
            @if ($site->latitude)
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $site->latitude }},{{ $site->longitude }}" target="_blank" rel="noopener" class="btn btn-outline"><x-lucide-navigation class="size-5" aria-hidden="true" /> Navigasi ke kantor desa <span class="sr-only">(tab baru)</span></a>
            @endif
            @foreach ($site->socials() as $k => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener" class="chip"><x-social-icon :name="$k" class="size-4" /> {{ ucfirst($k) }} <span class="sr-only">(tab baru)</span></a>
            @endforeach
        </div>

        <section class="mt-10" aria-label="Peta lokasi kantor desa">
            <x-village-map :markers="$markers" :layers="['fasilitas' => 'Fasilitas']" :show-center="true" center-label="Kantor Desa Marga Mulya" height="h-[380px]" :list="false" label="Peta lokasi kantor desa dan fasilitas umum" />
        </section>

        <div class="mt-14 grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- FORMULIR KONTAK --}}
            <section id="form-pesan" class="card scroll-mt-28 p-6 sm:p-8" aria-labelledby="pesan-judul">
                <h2 id="pesan-judul" class="font-display text-2xl font-semibold">Kirim pesan</h2>
                <p class="mt-1 text-ink-soft">Pertanyaan, aspirasi, atau pengaduan untuk perangkat desa.</p>
                <x-flash key="message_sent" class="mt-5" />
                @php $isMsg = old('_form') === 'pesan'; @endphp
                @if ($isMsg && $errors->any())<div class="mt-4 rounded-2xl border border-red-300 bg-red-50 p-4 text-red-900" role="alert" tabindex="-1" x-data x-init="$el.focus()"><p class="font-bold">Formulir belum dapat dikirim. Periksa {{ $errors->count() }} isian berikut:</p><ul class="mt-1 list-disc pl-5 text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('contact.message') }}" class="mt-6 grid gap-5" x-data="publicForm()" @submit="sending = true" novalidate>
                    @csrf
                    <input type="hidden" name="_form" value="pesan">
                    <input type="hidden" name="_ts" :value="ts">
                    <div class="hidden" aria-hidden="true"><label>Situs web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form-field :show="$isMsg" name="name" label="Nama" :required="true">
                            <input id="f-name" name="name" value="{{ $isMsg ? old('name') : '' }}" required maxlength="100" autocomplete="name" class="input" @if ($isMsg && $errors->has('name')) aria-invalid="true" aria-describedby="f-name-error" @endif>
                        </x-form-field>
                        <x-form-field :show="$isMsg" name="contact" label="Email atau nomor WhatsApp" :required="true">
                            <input id="f-contact" name="contact" value="{{ $isMsg ? old('contact') : '' }}" required maxlength="120" class="input" @if ($isMsg && $errors->has('contact')) aria-invalid="true" aria-describedby="f-contact-error" @endif>
                        </x-form-field>
                    </div>
                    <x-form-field :show="$isMsg" name="subject" label="Subjek" :required="true">
                        <input id="f-subject" name="subject" value="{{ $isMsg ? old('subject') : '' }}" required maxlength="150" class="input" @if ($isMsg && $errors->has('subject')) aria-invalid="true" aria-describedby="f-subject-error" @endif>
                    </x-form-field>
                    <x-form-field :show="$isMsg" name="message" label="Pesan" :required="true" help="Minimal 10 karakter.">
                        <textarea id="f-message" name="message" rows="5" required minlength="10" maxlength="3000" class="input" aria-describedby="f-message-help{{ $isMsg && $errors->has('message') ? ' f-message-error' : '' }}" @if ($isMsg && $errors->has('message')) aria-invalid="true" @endif>{{ $isMsg ? old('message') : '' }}</textarea>
                    </x-form-field>
                    <button class="btn btn-primary justify-self-start" :disabled="sending"><x-lucide-send class="size-5" aria-hidden="true" /> <span x-text="sending ? 'Mengirim…' : 'Kirim pesan'">Kirim pesan</span></button>
                </form>
            </section>

            {{-- KONTRIBUSI WARGA --}}
            <section id="kontribusi" class="scroll-mt-28 rounded-[var(--radius-card)] border border-forest-200 bg-forest-50 p-6 sm:p-8" aria-labelledby="kontribusi-judul">
                <p class="eyebrow"><x-lucide-hand-heart class="size-4" aria-hidden="true" /> Citizen to Village</p>
                <h2 id="kontribusi-judul" class="mt-2 font-display text-2xl font-semibold">Kontribusi warga</h2>
                <ol class="mt-4 flex flex-wrap items-center gap-2 text-sm font-semibold text-forest-900" aria-label="Alur kontribusi">
                    @foreach (['Anda mengirim', 'Admin memeriksa', 'Disetujui / ditolak', 'Diterbitkan'] as $i => $step)
                        <li class="flex items-center gap-2"><span class="flex size-6 items-center justify-center rounded-full bg-forest-800 text-xs text-white">{{ $i + 1 }}</span>{{ $step }}@if (! $loop->last)<x-lucide-chevron-right class="size-4 opacity-50" aria-hidden="true" />@endif</li>
                    @endforeach
                </ol>
                <x-flash key="contribution_sent" class="mt-5" />
                @php $isCon = old('_form') === 'kontribusi'; @endphp
                @if ($isCon && $errors->any())<div class="mt-4 rounded-2xl border border-red-300 bg-red-50 p-4 text-red-900" role="alert" tabindex="-1" x-data x-init="$el.focus()"><p class="font-bold">Formulir belum dapat dikirim. Periksa {{ $errors->count() }} isian berikut:</p><ul class="mt-1 list-disc pl-5 text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('contact.contribute') }}" enctype="multipart/form-data" class="mt-6 grid gap-5" x-data="publicForm()" @submit="sending = true" novalidate>
                    @csrf
                    <input type="hidden" name="_form" value="kontribusi">
                    <input type="hidden" name="_ts" :value="ts">
                    <div class="hidden" aria-hidden="true"><label>Situs web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-form-field :show="$isCon" name="submitter_name" label="Nama pengirim" :required="true">
                            <input id="f-submitter_name" name="submitter_name" value="{{ $isCon ? old('submitter_name') : '' }}" required maxlength="100" autocomplete="name" class="input" @if ($isCon && $errors->has('submitter_name')) aria-invalid="true" aria-describedby="f-submitter_name-error" @endif>
                        </x-form-field>
                        <x-form-field :show="$isCon" name="contact" id="f-con-contact" label="Kontak" help="Tidak ditampilkan ke publik.">
                            <input id="f-con-contact" name="contact" value="{{ $isCon ? old('contact') : '' }}" maxlength="120" class="input" aria-describedby="f-con-contact-help">
                        </x-form-field>
                    </div>
                    <x-form-field :show="$isCon" name="category" label="Kategori kontribusi" :required="true">
                        <select id="f-category" name="category" required class="input" @if ($isCon && $errors->has('category')) aria-invalid="true" aria-describedby="f-category-error" @endif>
                            <option value="">Pilih kategori</option>
                            @foreach (\App\Models\CitizenSubmission::CATEGORIES as $k => $l)<option value="{{ $k }}" @selected($isCon && old('category') === $k)>{{ $l }}</option>@endforeach
                        </select>
                    </x-form-field>
                    <x-form-field :show="$isCon" name="title" label="Judul" :required="true">
                        <input id="f-title" name="title" value="{{ $isCon ? old('title') : '' }}" required maxlength="150" class="input" @if ($isCon && $errors->has('title')) aria-invalid="true" aria-describedby="f-title-error" @endif>
                    </x-form-field>
                    <x-form-field :show="$isCon" name="description" label="Deskripsi" :required="true" help="Ceritakan apa, kapan, dan di mana. Minimal 20 karakter.">
                        <textarea id="f-description" name="description" rows="4" required minlength="20" maxlength="3000" class="input" aria-describedby="f-description-help{{ $isCon && $errors->has('description') ? ' f-description-error' : '' }}" @if ($isCon && $errors->has('description')) aria-invalid="true" @endif>{{ $isCon ? old('description') : '' }}</textarea>
                    </x-form-field>
                    <x-form-field :show="$isCon" name="photo" label="Foto" help="JPG, PNG, atau WebP. Maksimal 2 MB.">
                        <input id="f-photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" @change="onFile($event)" class="input file:mr-3 file:rounded-full file:border-0 file:bg-forest-800 file:px-4 file:py-1.5 file:font-semibold file:text-white" aria-describedby="f-photo-help">
                        <p x-show="fileError" x-text="fileError" class="field-error" role="alert"></p>
                        <img x-show="preview" :src="preview" alt="Pratinjau foto yang dipilih" class="mt-3 max-h-48 rounded-2xl object-cover">
                    </x-form-field>
                    <x-form-field :show="$isCon" name="location" label="Lokasi">
                        <input id="f-location" name="location" value="{{ $isCon ? old('location') : '' }}" maxlength="200" placeholder="mis. Dekat balai desa, RT 02/RW 03" class="input">
                    </x-form-field>
                    <div>
                        <input type="hidden" name="latitude" :value="lat">
                        <input type="hidden" name="longitude" :value="lng">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" class="btn btn-outline btn-sm" @click="locate()" :disabled="locating"><x-lucide-locate-fixed class="size-4" aria-hidden="true" /> <span x-text="locating ? 'Mengambil lokasi…' : 'Tambahkan lokasi saya (opsional)'">Tambahkan lokasi saya (opsional)</span></button>
                            <button type="button" x-show="lat" x-cloak class="btn btn-ghost btn-sm" @click="clearLocation()">Hapus lokasi</button>
                        </div>
                        <p class="field-help" x-show="geoMessage" x-text="geoMessage + (lat ? ' (' + lat + ', ' + lng + ')' : '')" aria-live="polite"></p>
                    </div>
                    <p class="text-sm text-ink-soft">Nama dan kontak Anda tidak ditampilkan di website. Kontribusi yang disetujui dapat diterbitkan admin setelah disunting.</p>
                    <button class="btn btn-primary justify-self-start" :disabled="sending"><x-lucide-send class="size-5" aria-hidden="true" /> <span x-text="sending ? 'Mengirim…' : 'Kirim kontribusi'">Kirim kontribusi</span></button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.public>
