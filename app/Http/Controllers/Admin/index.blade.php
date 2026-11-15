@extends('layouts.app')

@section('content')
<h4 class="mb-3 fw-bold"><i class="fa-solid fa-list-check me-2"></i>Seleksi & Pengumuman Hasil PPDB</h4>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th>Status Seleksi</th>
                        <th>Aksi Pengumuman</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->nomor_pendaftaran }}</span></td>
                        <td>{{ $item->calonSiswa->nama_lengkap ?? '-' }}</td>
                        <td>{{ $item->jurusan->kode ?? '-' }}</td>
                        <td>
                            @if($item->pengumuman && $item->pengumuman->count() > 0)
                                @php
                                    $p = $item->pengumuman->first();
                                @endphp
                                @if($p->hasil == 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @else
                                    <span class="badge bg-danger">Tidak Diterima</span>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark">Belum Diumumkan</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ url('/admin/seleksi/' . $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="hasil" value="diterima">
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Nyatakan siswa ini DITERIMA?')"><i class="fa-solid fa-check"></i> Diterima</button>
                            </form>
                            <form action="{{ url('/admin/seleksi/' . $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="hasil" value="tidak_diterima">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Nyatakan siswa ini TIDAK DITERIMA?')"><i class="fa-solid fa-times"></i> Tidak Diterima</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data siswa terverifikasi yang siap diseleksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($pendaftar, 'links'))
            <div class="mt-3">
                {{ $pendaftar->links() }}
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Dashboard Admin</a>
</div>
@endsection