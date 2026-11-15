<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\KuotaService;
use Illuminate\Http\Request;

class KuotaController extends Controller
{
    public function __construct(private readonly KuotaService $kuota) {}

    public function index()
    {
        $programs = Program::orderBy('kode')->get()->map(function (Program $program) {
            $terpakai = $this->kuota->terpakai($program);
            $sisa = $this->kuota->sisa($program);

            return [
                'program' => $program,
                'terpakai' => $terpakai,
                'sisa' => $sisa,
                'persentase' => $program->kuota > 0 ? (int) round($terpakai / $program->kuota * 100) : 100,
                'penuh' => $sisa <= 0,
            ];
        });

        $ringkasan = [
            'total_kuota' => (int) $programs->sum(fn ($row) => $row['program']->kuota),
            'total_terpakai' => (int) $programs->sum('terpakai'),
        ];

        $ringkasan['total_sisa'] = $ringkasan['total_kuota'] - $ringkasan['total_terpakai'];

        return view('admin.kuota.index', compact('programs', 'ringkasan'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'kuota' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        $terpakai = $this->kuota->terpakai($program);

        if ($validated['kuota'] < $terpakai) {
            return back()->with(
                'error',
                'Kuota tidak boleh lebih kecil dari jumlah kursi yang sudah terisi ('.$terpakai.').'
            );
        }

        $program->update(['kuota' => $validated['kuota']]);

        return back()->with('success', 'Kuota '.$program->nama.' berhasil diperbarui.');
    }
}
