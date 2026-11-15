@extends('layouts.backend')

@section('title', 'Jadwal PPDB')
@section('subtitle', 'Atur tahapan dan tanggal setiap kegiatan PPDB.')

@section('actions')
    <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Tahap
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Tahapan PPDB</h2>
            <span class="text-muted-soft small">Urutan menentukan urutan tahapan pada halaman publik.</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">Urutan</th>
                        <th>Tahapan</th>
                        <th style="width: 170px;">Mulai</th>
                        <th style="width: 170px;">Selesai</th>
                        <th style="width: 160px;">Status</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $item)
                        <tr>
                            <td><span class="badge text-bg-light border">{{ $item->urutan }}</span></td>
                            <td class="fw-semibold">{{ $item->tahapan }}</td>
                            <td>{{ $item->tanggal_mulai->translatedFormat('j M Y') }}</td>
                            <td>{{ $item->tanggal_selesai->translatedFormat('j M Y') }}</td>
                            <td>
                                <span class="badge {{ $item->statusBadgeClass() }}">{{ $item->statusLabel() }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.jadwal.edit', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus tahap ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty">
                                    <i class="bi bi-calendar-event fs-3 d-block mb-2"></i>
                                    Belum ada tahapan PPDB yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
