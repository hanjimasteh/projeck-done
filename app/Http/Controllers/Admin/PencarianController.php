<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PencarianController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $pendaftar = Pendaftaran::with(['calonSiswa', 'programPilihan1', 'ppdb'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_pendaftaran', 'like', '%'.$keyword.'%')
                        ->orWhereHas('calonSiswa', function ($inner) use ($keyword) {
                            $inner->where('nama_lengkap', 'like', '%'.$keyword.'%');
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pencarian.index', [
            'keyword' => $keyword,
            'pendaftar' => $pendaftar,
        ]);
    }
}
