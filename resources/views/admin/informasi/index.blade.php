@extends('layouts.backend')

@section('title', 'Informasi PPDB')
@section('subtitle', 'Kelola pengumuman dan informasi publik yang tampil di beranda website.')

@section('actions')
    <a href="{{ route('admin.informasi.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Informasi
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Daftar Informasi PPDB</h2>
            <span class="text-muted-soft small">Informasi berstatus “Terbit” tampil di beranda website.</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul Informasi</th>
                        <th>Isi Konten</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 140px;">Tanggal</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($informasi as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->judul }}</td>
                            <td class="text-muted-soft">{{ \Illuminate\Support\Str::limit($item->isi_konten, 90) }}</td>
                            <td>
                                <span class="badge {{ $item->statusBadgeClass() }}">{{ $item->statusLabel() }}</span>
                            </td>
                            <td>{{ $item->tanggal?->translatedFormat('j M Y') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.informasi.edit', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.informasi.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus informasi ini?')">
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
                            <td colspan="5">
                                <div class="admin-empty">
                                    <i class="bi bi-info-square fs-3 d-block mb-2"></i>
                                    Belum ada informasi PPDB yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($informasi->hasPages())
            <div class="p-3 border-top">
                {{ $informasi->links() }}
            </div>
        @endif
    </div>
@endsection
