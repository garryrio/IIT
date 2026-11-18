<x-layouts.public title="Halaman tidak ditemukan">
    <section class="container-page flex min-h-[60vh] flex-col items-center justify-center py-20 text-center">
        <p class="font-display text-7xl font-semibold text-forest-800">404</p>
        <h1 class="mt-4 font-display text-3xl font-semibold">Halaman tidak ditemukan</h1>
        <p class="mt-3 max-w-md text-ink-soft">Halaman yang Anda cari mungkin sudah dipindahkan, belum diterbitkan, atau alamatnya keliru.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke beranda</a>
            <button type="button" class="btn btn-outline" @click="$dispatch('open-search')"><x-lucide-search class="size-5" aria-hidden="true" /> Cari informasi</button>
        </div>
    </section>
</x-layouts.public>
