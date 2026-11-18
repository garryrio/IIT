<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    public const HOUSE_CONDITIONS = [
        'layak' => 'Layak huni',
        'kurang_layak' => 'Kurang layak',
        'tidak_layak' => 'Tidak layak huni',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['receives_aid' => 'boolean', 'monthly_income' => 'integer', 'members_count' => 'integer', 'dependents_count' => 'integer'];
    }

    public function perCapita(): float
    {
        return $this->monthly_income / max(1, $this->members_count);
    }

    public function incomeClass(): ?IncomeClass
    {
        return IncomeClass::classify($this->perCapita());
    }

    /**
     * Skor prioritas bantuan 0–100 (semakin tinggi semakin diprioritaskan).
     * Aturan sederhana dan transparan: kelas penghasilan, tanggungan, kondisi rumah, status bantuan.
     */
    public function priorityScore(): int
    {
        $class = $this->incomeClass();
        $score = 0;
        if ($class && $class->aid_priority > 0) {
            $score += (5 - min(4, $class->aid_priority)) * 15; // prioritas 1 → 60
        }
        $score += min(4, $this->dependents_count) * 5;
        $score += match ($this->house_condition) {
            'tidak_layak' => 15, 'kurang_layak' => 8, default => 0,
        };
        if (! $this->receives_aid && $class && $class->aid_priority > 0) {
            $score += 5;
        }

        return min(100, $score);
    }
}
