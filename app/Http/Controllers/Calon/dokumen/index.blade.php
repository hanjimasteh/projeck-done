@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-bold">Unggah Dokumen Persyaratan PPDB</div>
            <div class="card-body p-4">
                <p class="text-muted">Silakan unggah dokumen persyaratan dalam format PDF, JPG, atau PNG dengan ukuran maksimal 2MB.</p>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Status File</th>
                                <th>Aksi / Upload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($masterDokumen as $master)
                            @php
                                $uploaded = $uploadedDocs[$master->id] ?? null;
                            @endphp
                            <tr>
                                <td class="fw-bold">
                                    {{ $master->nama_dokumen }}
                                    @if($master->is_wajib)
                                        <span class="text-danger">*Wajib</span>
                                    @endif
                                </td>
                                <td>
                                    @if($uploaded)
                                        <span class="badge bg-success">Sudah Diunggah</span>
                                        <br><a href="{{ asset('storage/' . $uploaded->file_path) }}" target="_blank" class="small text-decoration-none">Lihat File</a>
                                    @else
                                        <span class="badge bg-secondary">Belum Diunggah</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ url('/calon/dokumen') }}" method="POST" enctype="multipart/form-data" class="row g-2">
                                        @csrf
                                        <input type="hidden" name="master_dokumen_id" value="{{ $master->id }}">
                                        <div class="col-auto">
                                            <input type="file" name="file_dokumen" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ url('/calon/dashboard') }}" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection