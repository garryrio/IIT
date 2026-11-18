@php
    $map = [
        'published' => ['Terbit', 'bg-forest-100 text-forest-900'],
        'draft' => ['Draf', 'bg-stone-200 text-stone-700'],
        'pending' => ['Menunggu', 'bg-sun-200 text-ink'],
        'approved' => ['Disetujui', 'bg-forest-100 text-forest-900'],
        'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        'unread' => ['Belum dibaca', 'bg-sun-200 text-ink'],
        'read' => ['Dibaca', 'bg-stone-200 text-stone-700'],
        'archived' => ['Arsip', 'bg-stone-100 text-stone-500'],
    ];
    [$label, $cls] = $map[$status] ?? [$status, 'bg-stone-100'];
@endphp
<span class="badge {{ $cls }}">{{ $label }}</span>
