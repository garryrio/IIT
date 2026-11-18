<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function __invoke(int $id): Response
    {
        $media = Media::findOrFail($id);
        $data = DB::table('media')->where('id', $id)->value('data');
        abort_if($data === null, 404);
        $etag = '"m'.$media->id.'-'.$media->updated_at?->timestamp.'"';
        if (request()->header('If-None-Match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }

        return response($data, 200, [
            'Content-Type' => $media->mime,
            'Content-Length' => (string) strlen($data),
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
