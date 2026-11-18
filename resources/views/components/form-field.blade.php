@props(['name', 'label', 'help' => null, 'required' => false, 'id' => null, 'show' => true])
@php $id = $id ?? 'f-'.str_replace(['[', ']', '.'], '-', $name); @endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">{{ $label }} @if ($required)<span class="text-red-700" aria-hidden="true">*</span><span class="sr-only">(wajib)</span>@else<span class="font-normal text-ink-soft">(opsional)</span>@endif</label>
    {{ $slot }}
    @if ($help)<p id="{{ $id }}-help" class="field-help">{{ $help }}</p>@endif
    @if ($show) @error($name)
        <p id="{{ $id }}-error" class="field-error"><x-lucide-circle-alert class="mt-0.5 size-4 shrink-0" aria-hidden="true" />{{ $message }}</p>
    @enderror @endif
</div>
