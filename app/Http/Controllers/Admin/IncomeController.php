<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\IncomeClass;
use App\Support\IncomeAnalytics;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'rw' => ['nullable', 'integer'],
            'class' => ['nullable', 'integer'],
            'aid' => ['nullable', 'in:0,1'],
        ]);
        $analytics = new IncomeAnalytics(Household::query()->get());

        return view('admin.income.index', [
            'a' => $analytics,
            'classes' => IncomeClass::ordered(),
            'priority' => $analytics->priorityList($filters),
            'review' => $analytics->needsReview(),
            'filters' => $filters,
        ]);
    }

    /** Ekspor daftar prioritas (CSV) untuk musyawarah desa / verifikasi lapangan. */
    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate(['rw' => ['nullable', 'integer'], 'class' => ['nullable', 'integer'], 'aid' => ['nullable', 'in:0,1']]);
        $rows = (new IncomeAnalytics(Household::query()->get()))->priorityList($filters, null);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Peringkat', 'Kode KK', 'Kepala keluarga', 'RT', 'RW', 'Anggota', 'Tanggungan', 'Penghasilan/bulan', 'Per kapita', 'Kelas', 'Kondisi rumah', 'Menerima bantuan', 'Program', 'Skor prioritas'], ';');
            foreach ($rows as $i => $h) {
                fputcsv($out, [$i + 1, $h->household_code, $h->head_name, $h->rt, $h->rw, $h->members_count, $h->dependents_count,
                    $h->monthly_income, round($h->perCapita()), $h->incomeClass()?->name, Household::HOUSE_CONDITIONS[$h->house_condition] ?? '',
                    $h->receives_aid ? 'Ya' : 'Tidak', $h->aid_program, $h->priorityScore()], ';');
            }
            fclose($out);
        }, 'prioritas-bantuan-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
