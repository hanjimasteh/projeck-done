@extends('layouts.backend')

@section('title', 'Kuota')
@section('subtitle', 'Kuota menentukan pendaftar mana yang diterima pada setiap program keahlian.')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value">{{ number_format($ringkasan['total_kuota'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Kuota</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value text-primary">{{ number_format($ringkasan['total_terpakai'], 0, ',', '.') }}</div>
                <div class="stat-label">Kursi Terisi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value">{{ number_format($ringkasan['total_sisa'], 0, ',', '.') }}</div>
                <div class="stat-label">Sisa Kursi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value">{{ $programs->where('penuh', true)->count() }}</div>
                <div class="stat-label">Program Penuh</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Kuota per Program Keahlian</h2>
            <span class="text-muted-soft small">Kursi terisi bertambah otomatis saat panitia memverifikasi berkas.</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Program Keahlian</th>
                        <th style="width: 130px;">Kuota</th>
                        <th style="width: 130px;">Terisi</th>
                        <th style="width: 130px;">Sisa</th>
                        <th style="width: 220px;">Kapasitas</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $row)
                        <tr>
                            <td><span class="badge text-bg-light border">{{ $row['program']->kode }}</span></td>
                            <td class="fw-semibold">{{ $row['program']->nama }}</td>
                            <td>{{ number_format($row['program']->kuota, 0, ',', '.') }}</td>
                            <td>{{ number_format($row['terpakai'], 0, ',', '.') }}</td>
                            <td>
                                <span class="fw-semibold {{ $row['penuh'] ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($row['sisa'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-quota-bar {{ $row['penuh'] ? 'is-full' : '' }}">
                                    <span style="width: {{ min(100, $row['persentase']) }}%"></span>
                                </div>
                                <small class="text-muted-soft">{{ $row['persentase'] }}% terisi</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#kuota-{{ $row['program']->id }}"
                                        aria-expanded="false" aria-controls="kuota-{{ $row['program']->id }}">
                                    <i class="bi bi-pencil me-1"></i> Ubah
                                </button>
                                <div class="collapse mt-2" id="kuota-{{ $row['program']->id }}">
                                    <form action="{{ route('admin.kuota.update', $row['program']->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="number" name="kuota" min="0" max="10000"
                                               value="{{ $row['program']->kuota }}"
                                               class="form-control form-control-sm" aria-label="Kuota baru">
                                        <button type="submit" class="btn btn-sm btn-primary fw-semibold">Simpan</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-empty">
                                    <i class="bi bi-pie-chart fs-3 d-block mb-2"></i>
                                    Belum ada program keahlian. Tambahkan program terlebih dahulu.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
