@extends('layouts.backend')

@section('title', 'Program Keahlian')
@section('subtitle', 'Kelola daftar program keahlian beserta kode dan kuota awalnya.')

@section('actions')
    <a href="{{ route('admin.program.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Program
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">Kode</th>
                        <th>Nama Program / Jurusan</th>
                        <th style="width: 140px;">Kuota Awal</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                        <tr>
                            <td><span class="badge text-bg-light border">{{ $program->kode }}</span></td>
                            <td class="fw-semibold">{{ $program->nama }}</td>
                            <td>{{ number_format($program->kuota, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('admin.program.edit', $program->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.program.destroy', $program->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus program ini?')">
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
                                    <i class="bi bi-book fs-3 d-block mb-2"></i>
                                    Belum ada program keahlian yang ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
