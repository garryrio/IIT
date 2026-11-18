<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\Household;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

class HouseholdResource extends Resource
{
    public static string $model = Household::class;

    public static string $slug = 'data-kk';

    public static string $label = 'Data KK';

    public static string $plural = 'Data Ekonomi KK';

    public static string $icon = 'house';

    public static string $titleField = 'household_code';

    public static function fields(): array
    {
        return [
            Field::make('household_code', 'Kode KK internal')->required()->rules(['alpha_dash', 'max:30'])
                ->help('Gunakan kode internal, BUKAN nomor KK/NIK asli.')->section('Identitas rumah tangga'),
            Field::make('head_name', 'Nama kepala keluarga')->required(),
            Field::make('rw', 'RW', 'number')->required()->rules(['integer', 'between:1,99']),
            Field::make('rt', 'RT', 'number')->required()->rules(['integer', 'between:1,99']),
            Field::make('members_count', 'Jumlah anggota keluarga', 'number')->required()->rules(['integer', 'between:1,30']),
            Field::make('dependents_count', 'Jumlah tanggungan (anak/lansia/disabilitas)', 'number')->default(0)->rules(['integer', 'between:0,30']),
            Field::make('main_occupation', 'Mata pencaharian utama', 'select')->options([
                'Petani' => 'Petani', 'Buruh tani' => 'Buruh tani', 'Nelayan' => 'Nelayan', 'Pedagang/UMKM' => 'Pedagang/UMKM', 'Pengrajin' => 'Pengrajin',
                'Karyawan swasta' => 'Karyawan swasta', 'PNS' => 'PNS', 'Guru' => 'Guru', 'TNI/POLRI' => 'TNI/POLRI', 'Buruh harian' => 'Buruh harian',
                'Tidak bekerja' => 'Tidak bekerja', 'Lainnya' => 'Lainnya',
            ])->section('Kondisi ekonomi'),
            Field::make('monthly_income', 'Total penghasilan keluarga per bulan (Rp)', 'number')->required()->rules(['integer', 'min:0', 'max:1000000000']),
            Field::make('house_condition', 'Kondisi rumah', 'select')->options(Household::HOUSE_CONDITIONS),
            Field::make('receives_aid', 'Saat ini sudah menerima bantuan', 'boolean'),
            Field::make('aid_program', 'Program bantuan yang diterima')->placeholder('mis. Bantuan pangan, BLT Dana Desa'),
            Field::make('notes', 'Catatan petugas', 'textarea'),
        ];
    }

    public static function columns(): array
    {
        return ['household_code' => 'Kode', 'head_name' => 'Kepala keluarga', 'rtrw' => 'RT/RW', 'percapita' => 'Per kapita', 'class' => 'Kelas', 'receives_aid' => 'Menerima bantuan'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        /** @var Household $m */
        return match ($key) {
            'rtrw' => sprintf('%03d/%03d', $m->rt, $m->rw),
            'percapita' => 'Rp'.number_format($m->perCapita(), 0, ',', '.'),
            'class' => ($c = $m->incomeClass())
                ? new HtmlString('<span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full" style="background:'.e($c->color).'"></span>'.e($c->name).'</span>')
                : '—',
            default => parent::cell($m, $key),
        };
    }

    public static function searchable(): array
    {
        return ['household_code', 'head_name', 'aid_program'];
    }

    public static function filters(): array
    {
        return [
            'rw' => ['label' => 'RW', 'options' => Household::query()->distinct()->orderBy('rw')->pluck('rw')->mapWithKeys(fn ($v) => [$v => 'RW '.sprintf('%03d', $v)])->all()],
            'receives_aid' => ['label' => 'Bantuan', 'options' => ['1' => 'Sudah menerima', '0' => 'Belum menerima']],
        ];
    }

    public static function sort(): array
    {
        return ['household_code', 'asc'];
    }

    public static function extraRules(?int $ignoreId): array
    {
        return ['household_code' => [Rule::unique('households', 'household_code')->ignore($ignoreId)]];
    }
}
