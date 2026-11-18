<?php

namespace App\Admin;

use App\Support\MediaStore;

/** Definisi satu field formulir CMS (dipakai untuk render, validasi, dan penyimpanan). */
class Field
{
    public string $type = 'text';

    public bool $required = false;

    /** @var array<int, mixed> */
    public array $rules = [];

    /** @var array<string|int, string> */
    public array $options = [];

    public ?string $help = null;

    public ?string $placeholder = null;

    public bool $full = false;

    public mixed $default = null;

    public ?string $lngField = null;

    public ?string $section = null;

    public function __construct(public string $name, public string $label) {}

    public static function make(string $name, string $label, string $type = 'text'): static
    {
        $f = new static($name, $label);
        $f->type = $type;
        if (in_array($type, ['textarea', 'richtext', 'image', 'latlng', 'keyvalue'], true)) {
            $f->full = true;
        }

        return $f;
    }

    public function required(bool $r = true): static
    {
        $this->required = $r;

        return $this;
    }

    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function help(string $help): static
    {
        $this->help = $help;

        return $this;
    }

    public function placeholder(string $p): static
    {
        $this->placeholder = $p;

        return $this;
    }

    public function full(bool $full = true): static
    {
        $this->full = $full;

        return $this;
    }

    public function default(mixed $v): static
    {
        $this->default = $v;

        return $this;
    }

    public function section(string $title): static
    {
        $this->section = $title;

        return $this;
    }

    /** Untuk tipe latlng: name = kolom latitude, lngField = kolom longitude. */
    public static function latlng(string $lat = 'latitude', string $lng = 'longitude', string $label = 'Lokasi pada peta'): static
    {
        $f = static::make($lat, $label, 'latlng');
        $f->lngField = $lng;

        return $f;
    }

    /** @return array<string, array<int, mixed>> */
    public function validationRules(): array
    {
        $base = $this->required ? ['required'] : ['nullable'];

        return match ($this->type) {
            'image' => [$this->name.'_file' => MediaStore::rules(false), $this->name.'_remove' => ['nullable', 'boolean']],
            'latlng' => [
                $this->name => ['nullable', 'numeric', 'between:-90,90', 'required_with:'.$this->lngField],
                $this->lngField => ['nullable', 'numeric', 'between:-180,180', 'required_with:'.$this->name],
            ],
            'keyvalue' => [$this->name => ['nullable', 'array'], $this->name.'.*' => ['nullable', 'string', 'max:255']],
            'boolean' => [$this->name => ['nullable', 'boolean']],
            default => [$this->name => array_merge($base, $this->typeRules(), $this->rules)],
        };
    }

    /** @return array<int, mixed> */
    private function typeRules(): array
    {
        return match ($this->type) {
            'text', 'slug' => ['string', 'max:255'],
            'textarea' => ['string', 'max:5000'],
            'richtext' => ['string', 'max:100000'],
            'email' => ['email', 'max:255'],
            'url' => ['url', 'max:255'],
            'number' => ['numeric'],
            'date' => ['date'],
            'time' => ['date_format:H:i'],
            'datetime' => ['date'],
            'select', 'status' => ['in:'.implode(',', array_keys($this->options))],
            'color' => ['regex:/^#[0-9a-fA-F]{6}$/'],
            default => [],
        };
    }
}
