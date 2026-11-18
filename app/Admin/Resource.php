<?php

namespace App\Admin;

use App\Support\Html;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/** Basis modul CMS. Setiap modul cukup mendefinisikan model, field, kolom tabel, dan filter. */
abstract class Resource
{
    /** @var class-string<Model> */
    public static string $model;

    public static string $slug;

    public static string $label;

    public static string $plural;

    public static string $icon = 'file-text';

    public static bool $singleton = false;

    public static string $titleField = 'title';

    public const STATUS = ['draft' => 'Draf', 'published' => 'Terbit'];

    /** @return list<Field> */
    abstract public static function fields(): array;

    /** @return array<string, string> kolom => label */
    public static function columns(): array
    {
        return [static::$titleField => 'Judul', 'status' => 'Status', 'updated_at' => 'Diperbarui'];
    }

    /** @return list<string> */
    public static function searchable(): array
    {
        return [static::$titleField];
    }

    /** @return array<string, array{label:string, options:array<string,string>}> */
    public static function filters(): array
    {
        return static::hasStatus() ? ['status' => ['label' => 'Status', 'options' => static::STATUS]] : [];
    }

    public static function hasStatus(): bool
    {
        return collect(static::fields())->contains(fn (Field $f) => $f->name === 'status');
    }

    public static function query(Request $request): Builder
    {
        return static::$model::query();
    }

    /** @return array{0:string,1:string} */
    public static function sort(): array
    {
        return ['updated_at', 'desc'];
    }

    public static function title(Model $m): string
    {
        return (string) ($m->{static::$titleField} ?? '#'.$m->getKey());
    }

    public static function publicUrl(Model $m): ?string
    {
        return null;
    }

    /** Konteks tambahan untuk judul/tautan (mis. jenis informasi). */
    public static function context(Request $request): array
    {
        return [];
    }

    public static function heading(Request $request): string
    {
        return static::$plural;
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        $v = $m->{$key};

        return match (true) {
            $key === 'status' => new HtmlString(view('admin.partials.status', ['status' => $v])->render()),
            $v instanceof \DateTimeInterface => $v->translatedFormat('d M Y H:i'),
            is_bool($v) => $v ? 'Ya' : 'Tidak',
            default => Str::limit(strip_tags((string) $v), 70),
        };
    }

    /** Hook sebelum simpan: slug otomatis, sanitasi rich text, tanggal terbit. */
    public static function mutate(array $data, ?Model $model): array
    {
        foreach (static::fields() as $f) {
            if ($f->type === 'richtext' && array_key_exists($f->name, $data)) {
                $data[$f->name] = Html::clean($data[$f->name]);
            }
            if ($f->type === 'keyvalue' && array_key_exists($f->name, $data)) {
                $data[$f->name] = array_filter((array) $data[$f->name], fn ($v) => filled($v));
            }
            if ($f->type === 'slug') {
                $source = $data[static::$titleField] ?? $model?->{static::$titleField} ?? 'konten';
                $data[$f->name] = static::uniqueSlug($data[$f->name] ?: $source, $model);
            }
        }

        return $data;
    }

    protected static function uniqueSlug(string $value, ?Model $model): string
    {
        $base = Str::slug($value) ?: 'konten';
        $slug = $base;
        $i = 2;
        while (static::$model::query()->where('slug', $slug)
            ->when($model, fn ($q) => $q->whereKeyNot($model->getKey()))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public static function newModel(Request $request): Model
    {
        $m = new static::$model;
        foreach (static::fields() as $f) {
            if ($f->default !== null) {
                $m->{$f->name} = $f->default;
            }
        }

        return $m;
    }
}
