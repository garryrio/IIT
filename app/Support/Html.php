<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Str;

class Html
{
    private static ?HTMLPurifier $purifier = null;

    /** Sanitasi HTML rich text (keluaran editor Trix) — whitelist tag aman saja. */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }
        if (! self::$purifier) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.DefinitionImpl', null);
            $config->set('HTML.Allowed', 'p,br,div,strong,b,em,i,u,del,s,a[href|title],ul,ol,li,h2,h3,h4,blockquote,pre,code,hr');
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.Nofollow', true);
            $config->set('AutoFormat.RemoveEmpty', true);
            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier->purify($html);
    }

    /** Ubah teks biasa (dengan baris baru) menjadi paragraf aman. */
    public static function paragraphs(?string $text): string
    {
        if (! $text) {
            return '';
        }
        if (Str::contains($text, ['<p', '<div', '<br', '<ul', '<ol'])) {
            return self::clean($text);
        }

        return collect(preg_split('/\r?\n\s*\r?\n/', trim($text)))
            ->map(fn ($p) => '<p>'.nl2br(e(trim($p))).'</p>')->implode('');
    }

    public static function excerpt(?string $html, int $limit = 160): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $html)))), $limit);
    }
}
