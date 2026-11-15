@extends('layouts.calon')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="mb-4">
            <h3 class="fw-bold text-dark"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Unggah Dokumen Persyaratan</h3>
            <p class="text-muted">Silakan unggah dokumen persyaratan pendaftaran PPDB sesuai dengan ketentuan yang berlaku.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card Form Upload -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Form Upload Berkas</h5>
                <form action="{{ route('calon.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Dokumen Persyaratan</label>
                        <select name="master_dokumen_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            @foreach($jenisDokumenList as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_dokumen ?? $item->nama_persyaratan ?? $item->jenis_dokumen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">File Dokumen (Format: PDF, JPG, JPEG, PNG | Maksimal: 2MB)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-upload me-2"></i> Unggah Dokumen
                    </button>
                </form>
            </div>
        </div>

        <!-- Card Daftar Dokumen Terunggah -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Daftar Dokumen yang Telah Diunggah</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Dokumen</th>
                                <th>Status Berkas</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenisDokumenList as $index => $item)
                                @php
                                    $uploaded = $dokumenUploaded[$item->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $item->nama_dokumen ?? $item->nama_persyaratan ?? $item->jenis_dokumen }}</td>
                                    <td>
                                        @if($uploaded)
                                            @if($uploaded->status === \App\Enums\StatusDokumen::APPROVED)
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($uploaded->status === \App\Enums\StatusDokumen::REJECTED)
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Belum Diunggah</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $uploaded->keterangan ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($uploaded && $uploaded->file_path)
                                            <a href="{{ asset('storage/' . $uploaded->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                                <i class="bi bi-eye me-1"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada jenis dokumen persyaratan yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ url('/calon/dashboard') }}" class="btn btn-secondary fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection