@extends('layouts.backend')

@section('title', 'Persyaratan')
@section('subtitle', 'Kelola dokumen yang perlu disiapkan calon pendaftar.')

@section('actions')
    <a href="{{ route('admin.persyaratan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Persyaratan
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Daftar Persyaratan</h2>
            <span class="text-muted-soft small">Persyaratan wajib ditandai dengan badge merah.</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Persyaratan</th>
                        <th>Deskripsi</th>
                        <th style="width: 120px;">Wajib</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($persyaratan as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td class="text-muted-soft">{{ $item->deskripsi ?: '-' }}</td>
                            <td>
                                <span class="badge {{ $item->wajib ? 'text-bg-danger' : 'text-bg-secondary' }}">
                                    {{ $item->wajib ? 'Wajib' : 'Opsional' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.persyaratan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.persyaratan.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus persyaratan ini?')">
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
                            <td colspan="4">
                                <div class="admin-empty">
                                    <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                                    Belum ada persyaratan yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
