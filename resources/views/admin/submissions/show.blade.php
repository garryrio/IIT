<x-layouts.admin title="Moderasi Kontribusi">
    <a href="{{ route('admin.submissions.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline"><x-lucide-arrow-left class="size-4" aria-hidden="true" /> Semua kontribusi</a>
    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-stone-200 bg-white p-6 lg:col-span-2">
            <div class="flex flex-wrap items-center gap-2">@include('admin.partials.status', ['status' => $submission->status])<span class="badge bg-forest-50 text-forest-800">{{ $submission->categoryLabel() }}</span></div>
            <h2 class="mt-3 text-2xl font-bold">{{ $submission->title }}</h2>
            <p class="mt-1 text-sm text-stone-500">Dikirim {{ $submission->created_at->translatedFormat('l, d F Y H:i') }}</p>
            @if ($submission->image_url)<img src="{{ $submission->image_url }}" alt="Foto kontribusi: {{ $submission->title }}" class="mt-5 max-h-96 rounded-xl object-cover">@endif
            <div class="mt-5 whitespace-pre-line">{{ $submission->description }}</div>
            @if ($submission->location || $submission->latitude)
                <p class="mt-5 flex items-center gap-2 text-sm"><x-lucide-map-pin class="size-4" aria-hidden="true" />{{ $submission->location }}
                    @if ($submission->latitude)<a href="https://www.openstreetmap.org/?mlat={{ $submission->latitude }}&mlon={{ $submission->longitude }}#map=18/{{ $submission->latitude }}/{{ $submission->longitude }}" target="_blank" rel="noopener" class="font-semibold text-forest-700 hover:underline">({{ $submission->latitude }}, {{ $submission->longitude }}) buka peta</a>@endif
                </p>
            @endif
        </article>
        <aside class="space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 text-sm">
                <p class="font-bold">Pengirim (privat)</p>
                <p class="mt-1">{{ $submission->submitter_name }}</p>
                <p class="text-stone-600 break-all">{{ $submission->contact ?: 'Tidak mencantumkan kontak' }}</p>
                <p class="mt-2 text-xs text-stone-500">Data pengirim tidak pernah ditampilkan di website publik.</p>
            </div>
            <form method="POST" action="{{ route('admin.submissions.review', $submission) }}" class="rounded-2xl border border-stone-200 bg-white p-5">@csrf @method('PATCH')
                <fieldset>
                    <legend class="label">Keputusan moderasi</legend>
                    @foreach (['approved' => 'Setujui', 'rejected' => 'Tolak', 'pending' => 'Kembalikan ke menunggu'] as $k => $l)
                        <label class="flex min-h-10 items-center gap-2"><input type="radio" name="status" value="{{ $k }}" @checked($submission->status === $k) class="size-4 accent-forest-800"> {{ $l }}</label>
                    @endforeach
                </fieldset>
                <label for="admin_notes" class="label mt-3">Catatan admin</label>
                <textarea id="admin_notes" name="admin_notes" rows="3" class="input">{{ old('admin_notes', $submission->admin_notes) }}</textarea>
                <button class="btn btn-primary btn-sm mt-3 w-full">Simpan keputusan</button>
                @if ($submission->reviewed_at)<p class="mt-2 text-xs text-stone-500">Diperiksa {{ $submission->reviewed_at->translatedFormat('d M Y H:i') }}</p>@endif
            </form>
            @if ($submission->status === 'approved')
                <form method="POST" action="{{ route('admin.submissions.convert', $submission) }}" class="rounded-2xl border border-forest-200 bg-forest-50 p-5">@csrf
                    <p class="text-sm font-semibold">Terbitkan sebagai konten</p>
                    <p class="mt-1 text-sm text-stone-600">Membuat draf berita (tanpa data pribadi pengirim) untuk disunting lalu diterbitkan.</p>
                    <button class="btn btn-primary btn-sm mt-3 w-full">{{ $submission->published_post_id ? 'Buka draf berita' : 'Jadikan draf berita' }}</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" data-confirm="Hapus kontribusi ini secara permanen?">@csrf @method('DELETE')
                <button class="btn btn-ghost btn-sm w-full text-red-700"><x-lucide-trash-2 class="size-4" aria-hidden="true" /> Hapus kontribusi</button>
            </form>
        </aside>
    </div>
</x-layouts.admin>
