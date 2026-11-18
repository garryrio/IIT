<x-layouts.admin title="Kontribusi Warga">
    <p class="text-stone-600">Alur moderasi: <strong>warga mengirim → menunggu → admin memeriksa → disetujui/ditolak → dijadikan draf berita → diterbitkan</strong>.</p>
    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4" role="search">
        <div class="min-w-48 flex-1"><label for="q" class="label">Cari</label><input id="q" name="q" type="search" value="{{ request('q') }}" class="input"></div>
        <div><label for="status" class="label">Status</label><select id="status" name="status" class="input"><option value="">Semua</option>@foreach (\App\Models\CitizenSubmission::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select></div>
        <div><label for="category" class="label">Kategori</label><select id="category" name="category" class="input"><option value="">Semua</option>@foreach (\App\Models\CitizenSubmission::CATEGORIES as $k => $l)<option value="{{ $k }}" @selected(request('category') === $k)>{{ $l }}</option>@endforeach</select></div>
        <button class="btn btn-outline">Terapkan</button>
    </form>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="table-clean">
            <thead class="bg-stone-50"><tr><th scope="col">Judul</th><th scope="col">Kategori</th><th scope="col">Pengirim</th><th scope="col">Status</th><th scope="col">Dikirim</th></tr></thead>
            <tbody>
                @forelse ($items as $s)
                    <tr>
                        <td><a href="{{ route('admin.submissions.show', $s) }}" class="font-semibold hover:underline">{{ $s->title }}</a></td>
                        <td>{{ $s->categoryLabel() }}</td>
                        <td>{{ $s->submitter_name }}</td>
                        <td>@include('admin.partials.status', ['status' => $s->status])</td>
                        <td class="whitespace-nowrap text-stone-500">{{ $s->created_at->translatedFormat('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-stone-500">Belum ada kontribusi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links('partials.pagination') }}
</x-layouts.admin>
