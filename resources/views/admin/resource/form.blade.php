@php
    $isSingleton = $r::$singleton;
    $exists = $model->exists;
    $typeLabel = isset($context['type']) ? \App\Models\InformationPost::TYPES[$context['type']] : ($model->type ? (\App\Models\InformationPost::TYPES[$model->type] ?? null) : null);
    $label = ($r::$slug === 'informasi' && $typeLabel) ? $typeLabel : $r::$label;
    $title = $isSingleton ? $r::$label : ($exists ? 'Ubah '.$label : 'Tambah '.$label);
    $action = $isSingleton ? route('admin.resource.singleton.save', $r::$slug) : ($exists ? route('admin.resource.update', [$r::$slug, $model->id] + $context) : route('admin.resource.store', [$r::$slug] + $context));
    $center = \App\Support\MapData::center();
@endphp
<x-layouts.admin :title="$title">
    @unless ($isSingleton)
        <a href="{{ route('admin.resource.index', [$r::$slug] + $context) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline"><x-lucide-arrow-left class="size-4" aria-hidden="true" /> Kembali ke daftar</a>
    @endunless

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-4" novalidate x-data="{ type: @js(old('type', $model->type)) }">
        @csrf
        @if ($isSingleton || $exists) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_18rem]">
            <div class="rounded-2xl border border-stone-200 bg-white p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    @foreach ($r::fields() as $f)
                        @php
                            $value = old($f->name, $model->{$f->name});
                            $id = 'fld-'.$f->name;
                            $err = $errors->has($f->name) || $errors->has($f->name.'_file');
                            $describedBy = trim(($f->help ? $id.'-help ' : '').($err ? $id.'-error' : ''));
                            $sectionFor = in_array($f->section, ['Khusus pengumuman']) ? "type === 'announcement'" : (in_array($f->section, ['Khusus agenda']) ? "type === 'event'" : null);
                        @endphp
                        @if ($f->section)
                            </div>
                            <h2 class="mt-8 mb-4 border-b border-stone-200 pb-2 text-base font-bold text-forest-900 first:mt-0" @if ($sectionFor) x-show="{{ $sectionFor }}" @endif>{{ $f->section }}</h2>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2" @if ($sectionFor) x-show="{{ $sectionFor }}" @endif>
                        @endif

                        <div class="{{ $f->full ? 'md:col-span-2' : '' }}">
                            @if ($f->type === 'boolean')
                                <input type="hidden" name="{{ $f->name }}" value="0">
                                <label class="flex min-h-11 items-start gap-3 rounded-xl border border-stone-200 p-3">
                                    <input id="{{ $id }}" type="checkbox" name="{{ $f->name }}" value="1" @checked((bool) $value) class="mt-0.5 size-5 accent-forest-800">
                                    <span class="font-semibold">{{ $f->label }}</span>
                                </label>
                            @else
                                <label for="{{ $f->type === 'image' ? $id.'-file' : ($f->type === 'richtext' ? $id.'-editor' : ($f->type === 'latlng' ? $id : ($f->type === 'keyvalue' ? '' : $id))) }}" class="label">
                                    {{ $f->label }} @if ($f->required)<span class="text-red-700" aria-hidden="true">*</span><span class="sr-only">(wajib)</span>@endif
                                </label>
                                @switch($f->type)
                                    @case('textarea')
                                        <textarea id="{{ $id }}" name="{{ $f->name }}" rows="4" class="input" @if ($f->placeholder) placeholder="{{ $f->placeholder }}" @endif @if ($err) aria-invalid="true" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>{{ $value }}</textarea>
                                        @break
                                    @case('richtext')
                                        <input id="{{ $id }}" type="hidden" name="{{ $f->name }}" value="{{ $value }}">
                                        <trix-editor id="{{ $id }}-editor" input="{{ $id }}" class="prose-village" aria-label="{{ $f->label }}"></trix-editor>
                                        @break
                                    @case('select')
                                    @case('status')
                                        <select id="{{ $id }}" name="{{ $f->name }}" class="input" @if ($f->name === 'type') x-model="type" @endif @if ($err) aria-invalid="true" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
                                            @unless ($f->required)<option value="">— Pilih —</option>@endunless
                                            @foreach ($f->options as $k => $l)<option value="{{ $k }}" @selected((string) $value === (string) $k)>{{ $l }}</option>@endforeach
                                        </select>
                                        @break
                                    @case('image')
                                        <div x-data="imageField(@js($value), {{ (int) config('village.media_max_kb') }})" class="rounded-xl border border-dashed border-stone-300 p-3">
                                            <div class="flex flex-wrap items-center gap-4">
                                                <template x-if="preview"><img :src="preview" alt="Pratinjau {{ $f->label }}" class="h-28 w-40 rounded-lg bg-stone-100 object-cover"></template>
                                                <template x-if="!preview"><div class="flex h-28 w-40 items-center justify-center rounded-lg bg-stone-100 text-sm text-stone-500">Belum ada gambar</div></template>
                                                <div class="grid gap-2">
                                                    <input id="{{ $id }}-file" x-ref="file" type="file" name="{{ $f->name }}_file" accept="image/jpeg,image/png,image/webp" @change="onChange($event)" class="w-full max-w-full text-sm file:mr-3 file:rounded-full file:border-0 file:bg-forest-800 file:px-4 file:py-2 file:font-semibold file:text-white" @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
                                                    <input type="hidden" name="{{ $f->name }}_remove" :value="removed ? 1 : 0">
                                                    <button type="button" x-show="preview" @click="remove()" class="btn btn-ghost btn-sm justify-self-start text-red-700">Hapus gambar</button>
                                                    <p x-show="busy" class="text-sm text-stone-500" role="status">Memproses gambar…</p>
                                                    <p x-show="error" x-text="error" class="field-error" role="alert"></p>
                                                </div>
                                            </div>
                                        </div>
                                        @break
                                    @case('latlng')
                                        @php $lngValue = old($f->lngField, $model->{$f->lngField}); @endphp
                                        <div x-data="mapPicker(@js($value), @js($lngValue), @js($center))" class="grid gap-3">
                                            <div x-ref="map" class="h-64 rounded-xl border border-stone-200 bg-stone-100" role="application" aria-label="Peta pemilih lokasi: klik untuk menaruh penanda"></div>
                                            <p x-show="failed" class="text-sm text-stone-500">Peta tidak dapat dimuat — isi koordinat secara manual.</p>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div><label for="{{ $id }}" class="text-sm font-semibold">Latitude</label><input id="{{ $id }}" name="{{ $f->name }}" x-model="lat" inputmode="decimal" class="input" @error($f->name) aria-invalid="true" @enderror></div>
                                                <div><label for="{{ $id }}-lng" class="text-sm font-semibold">Longitude</label><input id="{{ $id }}-lng" name="{{ $f->lngField }}" x-model="lng" inputmode="decimal" class="input" @error($f->lngField) aria-invalid="true" @enderror></div>
                                            </div>
                                            <button type="button" @click="clear()" class="btn btn-ghost btn-sm justify-self-start">Kosongkan titik</button>
                                        </div>
                                        @break
                                    @case('keyvalue')
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            @foreach ($f->options as $k => $l)
                                                <div><label for="{{ $id }}-{{ $k }}" class="text-sm font-semibold">{{ $l }}</label><input id="{{ $id }}-{{ $k }}" name="{{ $f->name }}[{{ $k }}]" value="{{ old($f->name.'.'.$k, $value[$k] ?? '') }}" type="url" placeholder="https://" class="input"></div>
                                            @endforeach
                                        </div>
                                        @break
                                    @default
                                        @php
                                            $inputType = ['number' => 'number', 'date' => 'date', 'time' => 'time', 'datetime' => 'datetime-local', 'email' => 'email', 'url' => 'url', 'color' => 'color'][$f->type] ?? 'text';
                                            if ($value instanceof \DateTimeInterface) {
                                                $value = $f->type === 'datetime' ? $value->format('Y-m-d\TH:i') : $value->format('Y-m-d');
                                            } elseif ($f->type === 'time' && $value) {
                                                $value = substr($value, 0, 5);
                                            }
                                        @endphp
                                        <input id="{{ $id }}" name="{{ $f->name }}" type="{{ $inputType }}" value="{{ $value }}" @if ($f->type === 'number') step="any" @endif
                                               @if ($f->placeholder) placeholder="{{ $f->placeholder }}" @endif class="input {{ $f->type === 'color' ? '!h-11 !w-24 !p-1' : '' }}"
                                               @if ($err) aria-invalid="true" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
                                @endswitch
                            @endif
                            @if ($f->help)<p id="{{ $id }}-help" class="field-help">{{ $f->help }}</p>@endif
                            @foreach ([$f->name, $f->name.'_file', $f->lngField] as $ek)
                                @if ($ek && $errors->has($ek))<p id="{{ $id }}-error" class="field-error"><x-lucide-circle-alert class="mt-0.5 size-4 shrink-0" aria-hidden="true" />{{ $errors->first($ek) }}</p>@endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-stone-200 bg-white p-5 xl:sticky xl:top-24">
                    <button class="btn btn-primary w-full"><x-lucide-save class="size-4" aria-hidden="true" /> Simpan</button>
                    @unless ($isSingleton)<a href="{{ route('admin.resource.index', [$r::$slug] + $context) }}" class="btn btn-ghost mt-2 w-full">Batal</a>@endunless
                    @if ($exists)
                        <dl class="mt-4 space-y-1 border-t border-stone-100 pt-4 text-sm text-stone-600">
                            <div class="flex justify-between gap-2"><dt>Dibuat</dt><dd>{{ $model->created_at?->translatedFormat('d M Y H:i') }}</dd></div>
                            <div class="flex justify-between gap-2"><dt>Diperbarui</dt><dd>{{ $model->updated_at?->translatedFormat('d M Y H:i') }}</dd></div>
                        </dl>
                        @if ($url = $r::publicUrl($model))<a href="{{ $url }}" target="_blank" class="btn btn-outline btn-sm mt-4 w-full"><x-lucide-external-link class="size-4" aria-hidden="true" /> Lihat di website</a>@endif
                    @endif
                </div>
                @if ($r::$slug === 'data-kk')
                    <div class="rounded-2xl border border-sun-300 bg-sun-100 p-4 text-sm"><p class="font-bold">Privasi data</p><p class="mt-1">Data ekonomi KK hanya dapat dilihat admin dan tidak pernah ditampilkan per keluarga di website publik.</p></div>
                @endif
            </aside>
        </div>
    </form>

    @if ($exists && ! $isSingleton)
        <form method="POST" action="{{ route('admin.resource.destroy', [$r::$slug, $model->id]) }}" class="mt-6" data-confirm="Hapus &quot;{{ $r::title($model) }}&quot;? Tindakan ini tidak dapat dibatalkan.">
            @csrf @method('DELETE')
            <button class="btn btn-ghost btn-sm text-red-700"><x-lucide-trash-2 class="size-4" aria-hidden="true" /> Hapus {{ strtolower($label) }} ini</button>
        </form>
    @endif
</x-layouts.admin>
