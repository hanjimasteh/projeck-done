<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaran;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['programPilihan1', 'programPilihan2', 'ppdb', 'calonSiswa']);

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('ppdb', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('program_1_id')) {
            $query->where('program_pilihan_1_id', $request->program_1_id);
        }

        if ($request->filled('program_2_id')) {
            $query->where('program_pilihan_2_id', $request->program_2_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_pendaftaran', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ]);
        }

        $pendaftaran = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Pendaftaran::count(),
            'submitted' => Pendaftaran::where('status', StatusPendaftaran::SUBMITTED->value)->count(),
            'verified' => Pendaftaran::where('status', StatusPendaftaran::VERIFIED->value)->count(),
            'rejected' => Pendaftaran::where('status', StatusPendaftaran::REJECTED->value)->count(),
            'selected' => Pendaftaran::where('status', StatusPendaftaran::SELECTED->value)->count(),
            'not_selected' => Pendaftaran::where('status', StatusPendaftaran::NOT_SELECTED->value)->count(),
            'registered_ulang' => Pendaftaran::where('status', StatusPendaftaran::REGISTERED_ULANG->value)->count(),
        ];

        $tahunAjaran = DB::table('ppdb')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $statusOptions = array_map(fn ($case) => $case->value, StatusPendaftaran::cases());

        return view('admin.laporan.index', compact(
            'pendaftaran',
            'stats',
            'tahunAjaran',
            'statusOptions'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request);

        $pendaftaran = $query->latest()->get();

        $filename = 'Laporan-Pendaftaran-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Nomor Pendaftaran', 'Nama Lengkap', 'NISN', 'Program 1', 'Program 2', 'Status', 'Tanggal Pendaftaran', 'Tahun Ajaran'];

        $callback = function () use ($pendaftaran, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($pendaftaran as $row) {
                $ppdb = $row->ppdb;
                $calon = $row->calonSiswa;
                $status = $row->status;
                fputcsv($file, [
                    $row->nomor_pendaftaran ?? '-',
                    $calon->nama_lengkap ?? '-',
                    $calon->nisn ?? '-',
                    $row->programPilihan1?->nama ?? '-',
                    $row->programPilihan2?->nama ?? '-',
                    $status?->label() ?? '-',
                    $row->tanggal_pendaftaran?->format('d-m-Y H:i') ?? '-',
                    $ppdb?->tahun_ajaran ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = $this->filteredQuery($request);
        $pendaftaran = $query->latest()->get();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.pdf', compact('pendaftaran'));
            $filename = 'Laporan-Pendaftaran-'.now()->format('Y-m-d').'.pdf';

            return $pdf->download($filename);
        }

        // Fallback: jika dompdf tidak terpasang, kirim CSV
        return $this->exportCsv($request);
    }

    private function filteredQuery(Request $request)
    {
        $query = Pendaftaran::with(['programPilihan1', 'programPilihan2', 'ppdb', 'calonSiswa']);

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('ppdb', function ($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        if ($request->filled('program_1_id')) {
            $query->where('program_pilihan_1_id', $request->program_1_id);
        }

        if ($request->filled('program_2_id')) {
            $query->where('program_pilihan_2_id', $request->program_2_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_pendaftaran', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ]);
        }

        return $query;
    }
}
