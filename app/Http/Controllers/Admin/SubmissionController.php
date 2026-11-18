<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitizenSubmission;
use App\Models\InformationPost;
use App\Support\Html;
use App\Support\MediaStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $items = CitizenSubmission::query()
            ->when(array_key_exists($request->query('status'), CitizenSubmission::STATUSES), fn ($q) => $q->where('status', $request->query('status')))
            ->when(array_key_exists($request->query('category'), CitizenSubmission::CATEGORIES), fn ($q) => $q->where('category', $request->query('category')))
            ->when($request->query('q'), function ($q, $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like)->orWhere('submitter_name', 'like', $like));
            })
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")->latest()->paginate(15)->withQueryString();

        return view('admin.submissions.index', compact('items'));
    }

    public function show(CitizenSubmission $submission): View
    {
        return view('admin.submissions.show', compact('submission'));
    }

    /** Moderasi: setujui atau tolak, dengan catatan admin. */
    public function review(Request $request, CitizenSubmission $submission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'pending'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $submission->update($data + ['reviewed_at' => $data['status'] === 'pending' ? null : now()]);

        return back()->with('success', 'Kontribusi ditandai "'.CitizenSubmission::STATUSES[$data['status']].'".');
    }

    /** Ubah kontribusi yang disetujui menjadi draf berita agar dapat disunting lalu diterbitkan admin. */
    public function convert(CitizenSubmission $submission): RedirectResponse
    {
        abort_unless($submission->status === 'approved', 422, 'Kontribusi harus disetujui terlebih dahulu.');
        if ($submission->publishedPost) {
            return redirect()->route('admin.resource.edit', ['informasi', $submission->published_post_id]);
        }

        $base = Str::slug($submission->title) ?: 'kontribusi-warga';
        $slug = $base;
        $i = 2;
        while (InformationPost::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $post = InformationPost::create([
            'type' => 'news',
            'status' => 'draft',
            'title' => $submission->title,
            'slug' => $slug,
            'excerpt' => Str::limit($submission->description, 300),
            // Nama/kontak pengirim sengaja TIDAK disertakan untuk melindungi privasi warga.
            'content' => Html::paragraphs($submission->description).'<p><em>Informasi ini berasal dari kontribusi warga dan telah diverifikasi admin desa.</em></p>',
            'cover_image_url' => $submission->image_url,
            'location' => $submission->location,
            'latitude' => $submission->latitude,
            'longitude' => $submission->longitude,
        ]);
        $submission->update(['published_post_id' => $post->id]);

        return redirect()->route('admin.resource.edit', ['informasi', $post->id])
            ->with('success', 'Draf berita dibuat dari kontribusi warga. Periksa lalu terbitkan.');
    }

    public function destroy(CitizenSubmission $submission): RedirectResponse
    {
        if (! $submission->published_post_id) {
            MediaStore::delete($submission->image_url);
        }
        $submission->delete();

        return redirect()->route('admin.submissions.index')->with('success', 'Kontribusi telah dihapus.');
    }
}
