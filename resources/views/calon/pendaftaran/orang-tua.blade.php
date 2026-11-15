@extends('layouts.calon')

@section('title', 'Formulir Pendaftaran')
@section('subtitle', 'Langkah 2 dari 5 - Data orang tua/wali')

@section('content')
    @include('calon.pendaftaran._stepper')

    @if ($locked)
        <div class="alert alert-info d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-lock-fill mt-1"></i>
            <div>Formulir sudah dikirim dan terkunci. Data hanya dapat dilihat.</div>
        </div>
    @elseif (! $editable)
        <div class="alert alert-warning d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-clock-fill mt-1"></i>
            <div>{{ $closedMessage }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-eyebrow">Langkah 2</span>
                    <h2 class="h4 fw-bold mt-1 mb-1">Data Orang Tua / Wali</h2>
                    <p class="text-muted-soft mb-0">Isi minimal salah satu data ayah, ibu, atau wali.</p>
                </div>
                <span class="info-icon d-none d-sm-inline-flex"><i class="bi bi-people"></i></span>
            </div>

            <form action="{{ route('calon.pendaftaran.orang-tua.store') }}" method="POST" class="d-grid gap-4">
                @csrf

                @foreach ([
                    'ayah' => ['label' => 'Ayah', 'icon' => 'person'],
                    'ibu' => ['label' => 'Ibu', 'icon' => 'person'],
                    'wali' => ['label' => 'Wali (Opsional)', 'icon' => 'person-badge'],
                ] as $key => $meta)
                    @php
                        $row = $orangTua[$key] ?? null;
                    @endphp

                    <div class="border rounded-4 p-3 p-lg-4">
                        <h3 class="h6 fw-bold mb-3">
                            <i class="bi bi-{{ $meta['icon'] }} me-1 text-primary"></i> {{ $meta['label'] }}
                        </h3>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="{{ $key }}_nama" class="form-label fw-semibold">Nama</label>
                                <input type="text" id="{{ $key }}_nama" name="{{ $key }}[nama]"
                                       value="{{ old($key.'.nama', $row->nama ?? '') }}"
                                       class="form-control @error($key.'.nama') is-invalid @enderror"
                                       @disabled(! $editable)>
                                @error($key.'.nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="{{ $key }}_pekerjaan" class="form-label fw-semibold">Pekerjaan</label>
                                <input type="text" id="{{ $key }}_pekerjaan" name="{{ $key }}[pekerjaan]"
                                       value="{{ old($key.'.pekerjaan', $row->pekerjaan ?? '') }}"
                                       class="form-control @error($key.'.pekerjaan') is-invalid @enderror"
                                       @disabled(! $editable)>
                                @error($key.'.pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="{{ $key }}_no_hp" class="form-label fw-semibold">No. HP</label>
                                <input type="text" id="{{ $key }}_no_hp" name="{{ $key }}[no_hp]" maxlength="20"
                                       value="{{ old($key.'.no_hp', $row->no_hp ?? '') }}"
                                       class="form-control @error($key.'.no_hp') is-invalid @enderror"
                                       @disabled(! $editable)>
                                @error($key.'.no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="{{ $key }}_alamat" class="form-label fw-semibold">Alamat</label>
                                <textarea id="{{ $key }}_alamat" name="{{ $key }}[alamat]" rows="2"
                                          class="form-control @error($key.'.alamat') is-invalid @enderror"
                                          @disabled(! $editable)>{{ old($key.'.alamat', $row->alamat ?? '') }}</textarea>
                                @error($key.'.alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                    <a href="{{ route('calon.pendaftaran.data-diri') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </a>

                    @if ($editable)
                        <button type="submit" class="btn btn-primary fw-semibold">
                            Simpan &amp; Lanjut <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
