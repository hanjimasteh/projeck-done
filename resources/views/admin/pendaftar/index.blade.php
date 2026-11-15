@extends('layouts.backend')

@section('title', 'Semua Pendaftar')
@section('subtitle', 'Daftar seluruh pendaftar beserta status dan pilihan programnya.')

@section('content')
    <div class="admin-card mb-3">
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label fw-semibold small mb-1">Status</label>
                    <select id="status" name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach (\App\Enums\StatusPendaftaran::cases() as $item)
                            <option value="{{ $item->value }}" @selected($status === $item->value)>{{ $item->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="program_id" class="form-label fw-semibold small mb-1">Program</label>
                    <select id="program_id" name="program_id" class="form-select form-select-sm">
                        <option value="">Semua Program</option>
                        @foreach (\App\Models\Program::orderBy('kode')->get() as $item)
                            <option value="{{ $item->id }}" @selected((string) $programId === (string) $item->id)>{{ $item->kode }} - {{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-sm btn-outline-dark">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Program Pilihan 1</th>
                        <th style="width: 190px;">Status</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftar as $item)
                        <tr>
                            <td class="fw-semibold text-primary">{{ $item->nomor_pendaftaran ?? '-' }}</td>
                            <td>{{ $item->calonSiswa?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->programPilihan1?->nama ?? '-' }}</td>
                            <td><x-status-badge :status="$item->status" /></td>
                            <td>
                                <a href="{{ route('admin.pendaftar.show', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                <a href="{{ route('admin.verifikasi.show', $item->id) }}" class="btn btn-sm btn-outline-dark fw-semibold">
                                    <i class="bi bi-file-earmark-check me-1"></i> Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty">
                                    <i class="bi bi-people fs-3 d-block mb-2"></i>
                                    Belum ada pendaftar yang sesuai filter.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pendaftar->hasPages())
            <div class="p-3 border-top">{{ $pendaftar->links() }}</div>
        @endif
    </div>
@endsection
