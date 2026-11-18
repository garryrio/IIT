<x-layouts.admin title="Pesan dari {{ $message->name }}">
    <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline"><x-lucide-arrow-left class="size-4" aria-hidden="true" /> Semua pesan</a>
    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-stone-200 bg-white p-6 lg:col-span-2">
            <h2 class="text-xl font-bold">{{ $message->subject }}</h2>
            <p class="mt-1 text-sm text-stone-500">{{ $message->created_at->translatedFormat('l, d F Y H:i') }}</p>
            <div class="mt-5 whitespace-pre-line">{{ $message->message }}</div>
        </article>
        <aside class="space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <dl class="space-y-2 text-sm"><div><dt class="text-stone-500">Nama</dt><dd class="font-semibold">{{ $message->name }}</dd></div><div><dt class="text-stone-500">Kontak</dt><dd class="font-semibold break-all">{{ $message->contact }}</dd></div><div><dt class="text-stone-500">Status</dt><dd>@include('admin.partials.status', ['status' => $message->status])</dd></div></dl>
                @if (filter_var($message->contact, FILTER_VALIDATE_EMAIL))
                    <a href="mailto:{{ $message->contact }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="btn btn-primary btn-sm mt-4 w-full">Balas via email</a>
                @elseif ($wa = \App\Models\SiteSetting::waLink($message->contact))
                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm mt-4 w-full">Balas via WhatsApp</a>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="rounded-2xl border border-stone-200 bg-white p-5">@csrf @method('PATCH')
                <label for="status" class="label">Ubah status</label>
                <select id="status" name="status" class="input">@foreach (\App\Models\ContactMessage::STATUSES as $k => $l)<option value="{{ $k }}" @selected($message->status === $k)>{{ $l }}</option>@endforeach</select>
                <button class="btn btn-outline btn-sm mt-3 w-full">Simpan status</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Hapus pesan ini secara permanen?">@csrf @method('DELETE')
                <button class="btn btn-ghost btn-sm w-full text-red-700"><x-lucide-trash-2 class="size-4" aria-hidden="true" /> Hapus pesan</button>
            </form>
        </aside>
    </div>
</x-layouts.admin>
