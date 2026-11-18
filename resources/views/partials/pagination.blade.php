@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="mt-10 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-ink-soft">Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</p>
        <ul class="flex flex-wrap items-center gap-1.5">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="btn btn-outline btn-sm opacity-50" aria-disabled="true">‹ Sebelumnya</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline btn-sm">‹ Sebelumnya</a>
                @endif
            </li>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-2 text-ink-soft">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex size-10 items-center justify-center rounded-full bg-forest-800 font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex size-10 items-center justify-center rounded-full font-semibold hover:bg-forest-50" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline btn-sm">Berikutnya ›</a>
                @else
                    <span class="btn btn-outline btn-sm opacity-50" aria-disabled="true">Berikutnya ›</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
