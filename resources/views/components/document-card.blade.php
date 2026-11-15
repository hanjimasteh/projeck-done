@props([
    'jenis',
    'dokumen' => null,
    'editable' => false,
])

@php
    $service = app(\App\Services\DokumenService::class);
    $isApproved = $dokumen?->status === \App\Enums\StatusDokumen::APPROVED;
    $isRejected = $dokumen?->status === \App\Enums\StatusDokumen::REJECTED;
    $canManage = $editable && $dokumen?->status !== \App\Enums\StatusDokumen::APPROVED;
    $extensions = $service->allowedExtensions($jenis);
    $inputId = 'dokumen-file-'.$jenis->id;

    $imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $isFoto = collect($extensions)->intersect($imageExts)->isNotEmpty()
        || str_contains(strtolower($jenis->nama), 'foto')
        || str_contains(strtolower($jenis->kategori ?? ''), 'foto');

    $dokumenExt = $dokumen ? strtolower(pathinfo($dokumen->file, PATHINFO_EXTENSION)) : null;
    $isDokumenFoto = $dokumen && in_array($dokumenExt, $imageExts, true);
@endphp

<div class="card border-0 shadow-sm rounded-4 h-100 {{ $isFoto ? 'border border-2 border-primary border-opacity-25' : '' }}">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
            <div class="min-w-0">
                <div class="section-eyebrow">{{ $jenis->kategori }}</div>
                <h3 class="h6 fw-bold mt-1 mb-0">{{ $jenis->nama }}</h3>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                @if ($isFoto)
                    <span class="badge text-bg-primary"><i class="bi bi-camera-fill me-1"></i> Foto</span>
                @endif
                <span class="badge {{ $jenis->wajib ? 'text-bg-danger' : 'text-bg-secondary' }}">
                    {{ $jenis->wajib ? 'Wajib' : 'Opsional' }}
                </span>
            </div>
        </div>

        <div class="small text-muted-soft mb-3">
            <div><i class="bi bi-file-earmark-text me-1"></i> Format: {{ $service->formatLabel($jenis) }}</div>
            <div><i class="bi bi-hdd me-1"></i> Maksimal: {{ $service->sizeLabel($jenis->ukuran_maks_kb) }}</div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            @if ($dokumen)
                <span class="badge text-bg-success">Sudah diunggah</span>
                <x-status-badge :status="$dokumen->status" />
            @else
                <span class="badge text-bg-secondary">Belum diunggah</span>
            @endif
        </div>

        @if ($isDokumenFoto)
            <div class="mb-3">
                <img src="{{ route('calon.dokumen.preview', $dokumen) }}" alt="Pratinjau {{ $jenis->nama }}"
                     class="img-fluid rounded-3 border w-100" style="max-height: 180px; object-fit: cover;">
            </div>
        @endif

        @if ($isRejected && $dokumen?->keterangan)
            <div class="alert alert-danger border-0 small py-2 px-3 mb-3">
                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                {{ $dokumen->keterangan }}
            </div>
        @endif

        @if ($dokumen)
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('calon.dokumen.preview', $dokumen) }}"
                   target="_blank"
                   rel="noopener"
                   class="btn btn-sm btn-outline-primary fw-semibold">
                    <i class="bi bi-eye me-1"></i> Lihat File
                </a>

                @if ($canManage)
                    <form action="{{ route('calon.dokumen.destroy', $dokumen) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus dokumen ini? File yang tersimpan akan dihapus permanen.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                @endif
            </div>
        @endif

        @if ($canManage)
            <form action="{{ route('calon.dokumen.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="mt-auto d-grid gap-2">
                @csrf
                <input type="hidden" name="jenis_dokumen_id" value="{{ $jenis->id }}">

                <label for="{{ $inputId }}" class="form-label small fw-semibold mb-0">
                    {{ $isRejected ? ($isFoto ? 'Unggah Ulang Foto' : 'Unggah Ulang Dokumen') : ($dokumen ? ($isFoto ? 'Ganti Foto' : 'Ganti Dokumen') : ($isFoto ? 'Unggah Foto' : 'Unggah Dokumen')) }}
                </label>
                <input type="file"
                       id="{{ $inputId }}"
                       name="file"
                       accept="{{ collect($extensions)->map(fn ($ext) => '.'.$ext)->join(',') }}"
                       class="form-control form-control-sm @if ($errors->has('file') && (string) old('jenis_dokumen_id') === (string) $jenis->id) is-invalid @endif"
                       required>
                @if ($errors->has('file') && (string) old('jenis_dokumen_id') === (string) $jenis->id)
                    <div class="invalid-feedback">{{ $errors->first('file') }}</div>
                @endif

                <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                    <i class="bi bi-cloud-arrow-up me-1"></i>
                    {{ $isRejected ? 'Simpan Perbaikan' : 'Simpan Dokumen' }}
                </button>
            </form>
        @elseif ($isApproved)
            <div class="small text-success mt-auto">
                <i class="bi bi-lock-fill me-1"></i> Dokumen sudah disetujui dan terkunci.
            </div>
        @elseif (! $editable)
            <div class="small text-muted-soft mt-auto">
                <i class="bi bi-lock-fill me-1"></i> Dokumen hanya dapat dilihat.
            </div>
        @endif
    </div>
</div>
