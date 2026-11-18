<?php

namespace App\Http\Controllers;

use App\Models\CitizenSubmission;
use App\Models\ContactMessage;
use App\Support\MapData;
use App\Support\MediaStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('public.contact', ['markers' => MapData::facilities()]);
    }

    public function message(Request $request): RedirectResponse
    {
        $this->guardSpam($request, 'pesan', '#form-pesan');
        $data = $this->check($request, '#form-pesan', [
            'name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], ['name' => 'Nama', 'contact' => 'Email atau nomor WhatsApp', 'subject' => 'Subjek', 'message' => 'Pesan']);
        ContactMessage::create($data + ['status' => 'unread']);

        return redirect()->to(route('contact').'#form-pesan')->with('message_sent', 'Terima kasih! Pesan Anda sudah kami terima dan akan ditindaklanjuti perangkat desa.');
    }

    public function contribute(Request $request): RedirectResponse
    {
        $this->guardSpam($request, 'kontribusi', '#kontribusi');
        $data = $this->check($request, '#kontribusi', [
            'submitter_name' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(CitizenSubmission::CATEGORIES))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:3000'],
            'location' => ['nullable', 'string', 'max:200'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'photo' => MediaStore::rules(false),
        ], [
            'submitter_name' => 'Nama', 'contact' => 'Kontak', 'category' => 'Kategori', 'title' => 'Judul',
            'description' => 'Deskripsi', 'location' => 'Lokasi', 'photo' => 'Foto',
        ]);

        $image = $request->hasFile('photo') ? MediaStore::store($request->file('photo')) : null;
        unset($data['photo']);
        CitizenSubmission::create($data + ['image_url' => $image, 'status' => 'pending']);

        return redirect()->to(route('contact').'#kontribusi')->with('contribution_sent', 'Terima kasih atas kontribusinya! Informasi Anda akan diperiksa admin desa sebelum dipublikasikan.');
    }

    /** Validasi dan kembalikan pengguna ke formulir yang sama (anchor) bila ada kesalahan. */
    private function check(Request $request, string $anchor, array $rules, array $attributes): array
    {
        try {
            return $request->validate($rules, [], $attributes);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('contact').$anchor);
        }
    }

    /** Perlindungan spam sederhana: honeypot, waktu isi minimum, dan pembatasan frekuensi. */
    private function guardSpam(Request $request, string $form, string $anchor): void
    {
        $tooFast = (int) $request->input('_ts') > 0 && (time() - (int) $request->input('_ts')) < 3;
        if (filled($request->input('website')) || $tooFast) {
            throw ValidationException::withMessages(['form' => 'Pengiriman terdeteksi sebagai spam. Silakan coba lagi.'])->redirectTo(route('contact').$anchor);
        }
        $key = $form.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['form' => 'Terlalu banyak pengiriman. Coba lagi dalam '.ceil(RateLimiter::availableIn($key) / 60).' menit.'])->redirectTo(route('contact').$anchor);
        }
        RateLimiter::hit($key, 600);
    }
}
