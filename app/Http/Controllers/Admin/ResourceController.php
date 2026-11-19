<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Field;
use App\Admin\Registry;
use App\Admin\Resource;
use App\Http\Controllers\Controller;
use App\Models\InformationPost;
use App\Support\MediaStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request, string $resource): View|RedirectResponse
    {
        /** @var class-string<resource> $r */
        $r = Registry::find($resource);
        if ($r::$singleton) {
            return redirect()->route('admin.resource.singleton', $resource);
        }

        $query = $r::query($request);
        if ($term = trim((string) $request->query('q'))) {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(function ($w) use ($r, $like) {
                foreach ($r::searchable() as $col) {
                    $w->orWhere($col, 'like', $like);
                }
            });
        }
        foreach ($r::filters() as $key => $filter) {
            $value = $request->query($key);
            if ($value !== null && $value !== '' && array_key_exists($value, $filter['options'])) {
                $query->where($key, $value);
            }
        }
        [$col, $dir] = $r::sort();
        $items = $query->orderBy($col, $dir)->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.resource.index', ['r' => $r, 'items' => $items, 'heading' => $r::heading($request), 'context' => $r::context($request)]);
    }

    public function create(Request $request, string $resource): View
    {
        $r = Registry::find($resource);
        abort_if($r::$singleton, 404);

        return view('admin.resource.form', ['r' => $r, 'model' => $r::newModel($request), 'context' => $r::context($request)]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $r = Registry::find($resource);
        abort_if($r::$singleton, 404);
        $model = new $r::$model;
        $this->save($request, $r, $model);

        return redirect()->route('admin.resource.index', [$resource] + $r::context($request))
            ->with('success', $r::$label.' "'.$r::title($model).'" berhasil disimpan.');
    }

    public function edit(Request $request, string $resource, int $id): View
    {
        $r = Registry::find($resource);

        return view('admin.resource.form', ['r' => $r, 'model' => $r::$model::findOrFail($id), 'context' => $r::context($request)]);
    }

    public function singleton(string $resource): View
    {
        $r = Registry::find($resource);
        abort_unless($r::$singleton, 404);
        $model = $r::$model::query()->first() ?? new $r::$model;

        return view('admin.resource.form', ['r' => $r, 'model' => $model, 'context' => []]);
    }

    public function saveSingleton(Request $request, string $resource): RedirectResponse
    {
        $r = Registry::find($resource);
        abort_unless($r::$singleton, 404);
        $model = $r::$model::query()->first() ?? new $r::$model;
        $this->save($request, $r, $model);

        return back()->with('success', $r::$label.' berhasil diperbarui.');
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $r = Registry::find($resource);
        $model = $r::$model::findOrFail($id);
        $this->save($request, $r, $model);

        return redirect()->route('admin.resource.index', [$resource] + $r::context($request))
            ->with('success', $r::$label.' "'.$r::title($model).'" berhasil diperbarui.');
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        $r = Registry::find($resource);
        abort_if($r::$singleton, 404);
        $model = $r::$model::findOrFail($id);
        $title = $r::title($model);
        if ($reason = $r::deleteBlockedReason($model)) {
            return back()->withErrors(['hapus' => $reason]);
        }
        DB::transaction(function () use ($r, $model) {
            foreach ($r::fields() as $f) {
                if ($f->type === 'image') {
                    MediaStore::delete($model->{$f->name});
                }
            }
            $model->delete();
        });

        return back()->with('success', $r::$label.' "'.$title.'" telah dihapus.');
    }

    /** Ubah status cepat draf ↔ terbit dari tabel. */
    public function toggleStatus(string $resource, int $id): RedirectResponse
    {
        $r = Registry::find($resource);
        abort_unless($r::hasStatus(), 404);
        $model = $r::$model::findOrFail($id);
        $publish = $model->status !== 'published';
        if ($publish && $model instanceof InformationPost && $model->source_event_id
            && (! $model->cover_image_url || str_contains((string) $model->content, InformationPost::REPORT_PLACEHOLDER))) {
            return back()->withErrors(['hapus' => 'Berita "'.$model->title.'" belum lengkap. Buka beritanya, unggah foto dokumentasi dan tulis isi kegiatan sebelum menerbitkan.']);
        }
        $data = ['status' => $publish ? 'published' : 'draft'];
        if ($publish && $model instanceof InformationPost && ! $model->published_at) {
            $data['published_at'] = now();
        }
        $model->update($data);

        return back()->with('success', '"'.$r::title($model).'" '.($publish ? 'diterbitkan.' : 'dikembalikan ke draf.'));
    }

    /** @param class-string<resource> $r */
    private function save(Request $request, string $r, Model $model): void
    {
        $rules = [];
        foreach ($r::fields() as $f) {
            $rules += $f->validationRules();
        }
        if (method_exists($r, 'extraRules')) {
            $rules = array_merge_recursive($rules, $r::extraRules($model->exists ? $model->getKey() : null));
        }
        $data = $request->validate($rules, [], collect($r::fields())->mapWithKeys(fn (Field $f) => [$f->name => $f->label, $f->name.'_file' => $f->label])->all());

        $payload = [];
        $newFiles = [];
        foreach ($r::fields() as $f) {
            switch ($f->type) {
                case 'image':
                    if ($request->hasFile($f->name.'_file')) {
                        $url = MediaStore::store($request->file($f->name.'_file'));
                        $newFiles[] = $url;
                        $payload[$f->name] = $url;
                    } elseif ($request->boolean($f->name.'_remove')) {
                        $payload[$f->name] = null;
                    }
                    if ($f->required && empty($payload[$f->name] ?? $model->{$f->name})) {
                        throw ValidationException::withMessages([$f->name.'_file' => $f->label.' wajib diunggah.']);
                    }
                    break;
                case 'boolean':
                    $payload[$f->name] = $request->boolean($f->name);
                    break;
                case 'latlng':
                    $payload[$f->name] = $data[$f->name] ?? null;
                    $payload[$f->lngField] = $data[$f->lngField] ?? null;
                    break;
                default:
                    $payload[$f->name] = $data[$f->name] ?? null;
            }
        }
        foreach ($r::fields() as $f) {
            if ($f->type === 'number' && $payload[$f->name] === null && $f->default !== null) {
                $payload[$f->name] = $f->default;
            }
        }
        $payload = $r::mutate($payload, $model->exists ? $model : null);

        $old = [];
        foreach ($r::fields() as $f) {
            if ($f->type === 'image' && array_key_exists($f->name, $payload) && $model->{$f->name} !== $payload[$f->name]) {
                $old[] = $model->{$f->name};
            }
        }
        $model->fill($payload)->save();
        foreach ($old as $url) {
            MediaStore::delete($url);
        }
    }
}
