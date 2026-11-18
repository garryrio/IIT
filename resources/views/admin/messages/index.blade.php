<x-layouts.admin title="Pesan Kontak">
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4" role="search">
        <div class="min-w-48 flex-1"><label for="q" class="label">Cari</label><input id="q" name="q" type="search" value="{{ request('q') }}" class="input"></div>
        <div><label for="status" class="label">Status</label>
            <select id="status" name="status" class="input"><option value="">Semua</option>@foreach (\App\Models\ContactMessage::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
        </div>
        <button class="btn btn-outline">Terapkan</button>
    </form>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="table-clean">
            <thead class="bg-stone-50"><tr><th scope="col">Pengirim</th><th scope="col">Subjek</th><th scope="col">Status</th><th scope="col">Diterima</th></tr></thead>
            <tbody>
                @forelse ($items as $m)
                    <tr class="{{ $m->status === 'unread' ? 'font-semibold' : '' }}">
                        <td>{{ $m->name }}</td>
                        <td><a href="{{ route('admin.messages.show', $m) }}" class="hover:underline">{{ $m->subject }}</a></td>
                        <td>@include('admin.partials.status', ['status' => $m->status])</td>
                        <td class="whitespace-nowrap text-stone-500">{{ $m->created_at->translatedFormat('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-stone-500">Belum ada pesan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links('partials.pagination') }}
</x-layouts.admin>
