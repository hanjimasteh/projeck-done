@extends('layouts.calon')

@section('title', 'Pilih Program Keahlian')
@section('subtitle', 'Pilih program keahlian dan kurikulum yang Anda minati. Kuota menentukan penerimaan.')

@section('content')
    <div class="alert alert-info border-0 shadow-sm d-flex gap-2 small mb-4">
        <i class="bi bi-pie-chart mt-1"></i>
        <div>
            <strong>Pilihan 1 = program utama (wajib).</strong> Kuota dicek untuk program ini.
            Pilihan 2 hanya cadangan, dipakai otomatis <strong>bila kuota Pilihan 1 habis</strong> saat verifikasi.
            Program yang sudah penuh tidak bisa dipilih.
        </div>
    </div>

    <form action="{{ route('calon.pendaftaran.program.store') }}" method="POST" class="d-grid gap-4">
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="program_pilihan_1_id" class="form-label fw-semibold">Program Utama · Pilihan 1 <span class="text-danger">*</span></label>
                <select id="program_pilihan_1_id" name="program_pilihan_1_id"
                        class="form-select" required>
                    <option value="">-- Pilih program --</option>
                    @foreach ($programList as $program)
                        @php($sisa = $sisaKuota[$program->id] ?? 0)
                        <option value="{{ $program->id }}"
                                @selected((string) old('program_pilihan_1_id') === (string) $program->id)
                                @disabled($sisa <= 0)>
                            {{ $program->kode }} - {{ $program->nama }}@if ($sisa > 0) (sisa {{ $sisa }})@else (PENUH)@endif
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Program yang kuotanya sudah penuh tidak dapat dipilih.</div>
            </div>

            <div class="col-12 col-md-6">
                <label for="program_pilihan_2_id" class="form-label fw-semibold">Program Cadangan · Pilihan 2 (Opsional)</label>
                <select id="program_pilihan_2_id" name="program_pilihan_2_id"
                        class="form-select">
                    <option value="">-- Tidak memilih --</option>
                    @foreach ($programList as $program)
                        @php($sisa = $sisaKuota[$program->id] ?? 0)
                        <option value="{{ $program->id }}" data-program-id="{{ $program->id }}"
                                @selected((string) old('program_pilihan_2_id') === (string) $program->id)
                                @disabled($sisa <= 0)>
                            {{ $program->kode }} - {{ $program->nama }}@if ($sisa > 0) (sisa {{ $sisa }})@else (PENUH)@endif
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Opsional, boleh dikosongkan. Cadangan dipakai otomatis hanya bila kuota Pilihan 1 habis saat verifikasi. Tidak boleh sama dengan Pilihan 1.</div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
            <a href="{{ route('calon.pendaftaran.orang-tua') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left me-1"></i> Sebelumnya: Orang Tua
            </a>

            <button type="submit" class="btn btn-primary fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Simpan Program
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const p1 = document.getElementById('program_pilihan_1_id');
                const p2 = document.getElementById('program_pilihan_2_id');

                if (!p1 || !p2) {
                    return;
                }

                // Sembunyikan opsi di Pilihan 2 yang sama dengan Pilihan 1
                const sync = () => {
                    Array.from(p2.options).forEach((opt) => {
                        if (!opt.value) return;
                        const same = opt.value === p1.value;
                        opt.disabled = same;
                    });

                    if (p2.value && p2.value === p1.value) {
                        p2.value = '';
                    }
                };

                p1.addEventListener('change', sync);
                sync();
            });
        </script>
    @endpush
@endsection
