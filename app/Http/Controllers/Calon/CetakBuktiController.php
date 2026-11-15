<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Barryvdh\DomPDF\Facade as DomPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CetakBuktiController extends Controller
{
    // Menampilkan halaman cetak bukti pendaftaran (HTML yang bisa di-print/export ke PDF)
    public function index(Pendaftaran $pendaftaran)
    {
        // Pastikan siswa sudah diterima sebelum bisa mencetak bukti
        $diterima = $pendaftaran->status === \App\Enums\StatusPendaftaran::SELECTED
            || $pendaftaran->pengumuman?->hasil === \App\Enums\Pengumuman::HASIL_DITERIMA;

        if (! $diterima) {
            return redirect()->route('calon.hasil')->with('error', 'Anda belum dapat mencetak bukti karena belum dinyatakan diterima.');
        }

        return view('calon.pendaftaran.cetak', compact('pendaftaran'));
    }

    // Menggenerate dan mengunduh file PDF bukti pendaftaran
    public function cetak(Pendaftaran $pendaftaran)
    {
        // Pastikan siswa sudah diterima sebelum bisa mencetak bukti
        $diterima = $pendaftaran->status === \App\Enums\StatusPendaftaran::SELECTED
            || $pendaftaran->pengumuman?->hasil === \App\Enums\Pengumuman::HASIL_DITERIMA;

        if (! $diterima) {
            return response('Anda belum dapat mencetak bukti karena belum dinyatakan diterima.', 403);
        }

        // Generate PDF using Dompdf dari view yang sudah ada
        $pdf = DomPDF::loadView('calon.pendaftaran.cetak', compact('pendaftaran'));

        // Set judul dan informasi PDF
        $pdf->setInfo([
            'Title' => 'Bukti Pendaftaran PPDB - ' . $pendaftaran->nomor_pendaftaran,
            'Author' => 'Sistem PPDB SMAKNIS',
            'Subject' => 'Bukti Pendaftaran Calon Siswa',
        ]);

        // Download filename
        $filename = 'Bukti-Pendaftaran-' . $pendaftaran->nomor_pendaftaran . '.pdf';

        return $pdf->download($filename);
    }
}