@props(['name'])
@switch($name)
    @case('instagram') <x-lucide-instagram {{ $attributes }} aria-hidden="true" /> @break
    @case('facebook') <x-lucide-facebook {{ $attributes }} aria-hidden="true" /> @break
    @case('youtube') <x-lucide-youtube {{ $attributes }} aria-hidden="true" /> @break
    @case('tiktok') <x-lucide-music-2 {{ $attributes }} aria-hidden="true" /> @break
    @case('marketplace') <x-lucide-shopping-bag {{ $attributes }} aria-hidden="true" /> @break
    @default <x-lucide-globe {{ $attributes }} aria-hidden="true" />
@endswitch
