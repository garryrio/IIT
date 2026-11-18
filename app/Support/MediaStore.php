<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Menyimpan gambar unggahan secara permanen.
 * - driver "database" (default): biner disimpan di tabel media (cocok untuk Vercel, filesystem-nya sementara).
 * - driver "disk": disimpan ke disk Laravel (mis. public lokal atau s3 / Supabase Storage / R2).
 */
class MediaStore
{
    public const MIMES = ['jpg', 'jpeg', 'png', 'webp'];

    public static function rules(bool $required = false): array
    {
        return [$required ? 'required' : 'nullable', 'file', 'image', 'mimes:'.implode(',', self::MIMES),
            'max:'.(int) config('village.media_max_kb', 2048), 'dimensions:max_width=6000,max_height=6000'];
    }

    public static function store(UploadedFile $file): string
    {
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'gambar';
        $name = Str::limit($name, 60, '').'.'.$ext;
        [$w, $h] = @getimagesize($file->getRealPath()) ?: [null, null];

        if (config('village.media_driver') === 'disk') {
            $disk = config('village.media_disk', 'public');
            $path = $file->storeAs('uploads/'.date('Y/m'), Str::random(10).'-'.$name, $disk);

            return Storage::disk($disk)->url($path);
        }

        $media = Media::create([
            'filename' => $name,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $w,
            'height' => $h,
        ]);
        Media::whereKey($media->id)->update(['data' => file_get_contents($file->getRealPath())]);

        return '/media/'.$media->id.'/'.$name;
    }

    /** Hapus file lama jika berasal dari media store (bukan aset seed). */
    public static function delete(?string $url): void
    {
        if (! $url) {
            return;
        }
        if (preg_match('#^/media/(\d+)/#', $url, $m)) {
            Media::whereKey((int) $m[1])->delete();
        }
    }
}
