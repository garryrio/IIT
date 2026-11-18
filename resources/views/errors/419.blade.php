<x-layouts.public title="Sesi berakhir">
    <section class="container-page flex min-h-[50vh] flex-col items-center justify-center py-20 text-center">
        <h1 class="font-display text-3xl font-semibold">Sesi formulir telah berakhir</h1>
        <p class="mt-3 max-w-md text-ink-soft">Demi keamanan, formulir kedaluwarsa setelah beberapa waktu. Silakan muat ulang halaman lalu kirim kembali.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-8">Kembali</a>
    </section>
</x-layouts.public>
