@extends('layouts.backend')

@section('title', 'Jenis Dokumen')
@section('subtitle', 'Kelola jenis dokumen, format, dan ukuran maksimum berkas yang diunggah.')

@section('actions')
    <a href="{{ route('admin.jenis-dokumen.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Jenis Dokumen
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 220px;">Nama Dokumen</th>
                        <th style="width: 140px;">Kategori</th>
                        <th style="width: 180px;">Format</th>
                        <th style="width: 140px;">Maks. Ukuran</th>
                        <th style="width: 110px;">Wajib</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jenisDokumen as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td class="text-uppercase">{{ $item->format_file }}</td>
                            <td>
                                {{ $item->ukuran_maks_kb >= 1024
                                    ? round($item->ukuran_maks_kb / 1024).' MB'
                                    : $item->ukuran_maks_kb.' KB' }}
                            </td>
                            <td>
                                <span class="badge {{ $item->wajib ? 'text-bg-danger' : 'text-bg-secondary' }}">
                                    {{ $item->wajib ? 'Wajib' : 'Opsional' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.jenis-dokumen.edit', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.jenis-dokumen.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus jenis dokumen ini?')">
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
                                    <i class="bi bi-folder fs-3 d-block mb-2"></i>
                                    Belum ada jenis dokumen yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
