@php $query = request()->only(array_merge(['q'], array_keys($r::filters()))); @endphp
<x-layouts.admin :title="$heading">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-stone-600">{{ $items->total() }} data</p>
        <a href="{{ route('admin.resource.create', [$r::$slug] + $context) }}" class="btn btn-primary btn-sm"><x-lucide-plus class="size-4" aria-hidden="true" /> Tambah {{ isset($context['type']) ? \App\Models\InformationPost::TYPES[$context['type']] : $r::$label }}</a>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4" role="search">
        @foreach ($context as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
        <div class="min-w-48 flex-1">
            <label for="q" class="label">Cari</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" class="input" placeholder="Kata kunci…">
        </div>
        @foreach ($r::filters() as $key => $f)
            @continue(isset($context[$key]))
            <div>
                <label for="f-{{ $key }}" class="label">{{ $f['label'] }}</label>
                <select id="f-{{ $key }}" name="{{ $key }}" class="input">
                    <option value="">Semua</option>
                    @foreach ($f['options'] as $val => $lab)<option value="{{ $val }}" @selected((string) request($key) === (string) $val)>{{ $lab }}</option>@endforeach
                </select>
            </div>
        @endforeach
        <button class="btn btn-outline"><x-lucide-filter class="size-4" aria-hidden="true" /> Terapkan</button>
        @if (array_filter($query))<a href="{{ route('admin.resource.index', [$r::$slug] + $context) }}" class="btn btn-ghost">Reset</a>@endif
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-stone-200 bg-white">
        @if ($items->isEmpty())
            <div class="p-10 text-center text-stone-500">
                <p class="font-semibold">Belum ada data{{ array_filter($query) ? ' yang cocok dengan filter' : '' }}.</p>
                <a href="{{ route('admin.resource.create', [$r::$slug] + $context) }}" class="btn btn-primary btn-sm mt-4">Tambah data pertama</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table-clean">
                    <thead class="bg-stone-50"><tr>
                        @foreach ($r::columns() as $col => $label)<th scope="col">{{ $label }}</th>@endforeach
                        <th scope="col" class="text-right">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="hover:bg-stone-50/60">
                                @foreach (array_keys($r::columns()) as $i => $col)
                                    <td class="{{ $i === 0 ? 'font-semibold' : '' }}">
                                        @if ($i === 0 || ($col === 'title' || $col === 'name'))<a href="{{ route('admin.resource.edit', [$r::$slug, $item->id] + $context) }}" class="hover:underline">{{ $r::cell($item, $col) }}</a>@else{{ $r::cell($item, $col) }}@endif
                                    </td>
                                @endforeach
                                <td class="text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        @if ($r::hasStatus())
                                            <form method="POST" action="{{ route('admin.resource.status', [$r::$slug, $item->id]) }}">@csrf
                                                <button class="btn btn-ghost btn-sm" title="{{ $item->status === 'published' ? 'Jadikan draf' : 'Terbitkan' }}">{{ $item->status === 'published' ? 'Jadikan draf' : 'Terbitkan' }}</button>
                                            </form>
                                        @endif
                                        @if ($url = $r::publicUrl($item))<a href="{{ $url }}" target="_blank" class="inline-flex size-9 items-center justify-center rounded-full hover:bg-stone-100" aria-label="Lihat di website: {{ $r::title($item) }}"><x-lucide-external-link class="size-4" aria-hidden="true" /></a>@endif
                                        <a href="{{ route('admin.resource.edit', [$r::$slug, $item->id] + $context) }}" class="inline-flex size-9 items-center justify-center rounded-full hover:bg-stone-100" aria-label="Ubah {{ $r::title($item) }}"><x-lucide-pencil class="size-4" aria-hidden="true" /></a>
                                        <form method="POST" action="{{ route('admin.resource.destroy', [$r::$slug, $item->id]) }}" data-confirm="Hapus &quot;{{ $r::title($item) }}&quot;? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')
                                            <button class="inline-flex size-9 items-center justify-center rounded-full text-red-700 hover:bg-red-50" aria-label="Hapus {{ $r::title($item) }}"><x-lucide-trash-2 class="size-4" aria-hidden="true" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    {{ $items->links('partials.pagination') }}
</x-layouts.admin>
