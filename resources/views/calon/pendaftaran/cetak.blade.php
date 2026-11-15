<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran PPDB - {{ $pendaftaran->nomor_pendaftaran }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
        }
        .card-print {
            max-width: 800px;
            margin: 30px auto;
            border: 2px solid #000;
            padding: 30px;
        }
        .header-sekolah {
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .card-print {
                border: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <!-- Tombol Aksi (Otomatis hilang saat dicetak/print) -->
        <div class="text-center my-4 no-print">
            <button onclick="window.print()" class="btn btn-primary fw-bold px-4 py-2 me-2">
                <i class="fa-solid fa-print me-2"></i> Cetak / Simpan PDF
            </button>
            <a href="{{ url('/calon/dashboard') }}" class="btn btn-secondary fw-bold px-4 py-2">
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Kartu Bukti Pendaftaran -->
        <div class="card-print bg-white">
            <div class="header-sekolah text-center mb-4">
                <h4 class="fw-bold text-uppercase mb-1">Panitia Penerimaan Peserta Didik Baru (PPDB)</h4>
                <h3 class="fw-bold text-uppercase text-primary mb-1">SMAKNIS</h3>
                <p class="text-muted small mb-0">Tahun Ajaran {{ $pendaftaran->ppdb->tahun_ajaran ?? '2026/2027' }} | Website Resmi PPDB Online</p>
            </div>

            <h5 class="text-center fw-bold text-decoration-underline mb-4">BUKTI PENDAFTARAN CALON PESERTA DIDIK</h5>

            <table class="table table-bordered align-middle mb-4">
                <tr>
                    <th class="bg-light" style="width: 30%;">Nomor Pendaftaran</th>
                    <td class="fw-bold text-primary fs-5">{{ $pendaftaran->nomor_pendaftaran }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Tanggal Pendaftaran</th>
                    <td>{{ ($pendaftaran->tanggal_pendaftaran ?? $pendaftaran->created_at)?->translatedFormat('d F Y H:i') }}</td>
                </tr>
                {{-- Jalur telah digantikan oleh pilihan program --}}
                <tr>
                    <th class="bg-light">Pilihan Program 1</th>
                    <td class="fw-bold">
                        {{ $pendaftaran->programPilihan1?->nama ?? '-' }}
                        ({{ $pendaftaran->programPilihan1?->kode ?? '-' }})
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Pilihan Program 2</th>
                    <td class="fw-bold">
                        {{ $pendaftaran->programPilihan2?->nama ?? '-' }}
                        ({{ $pendaftaran->programPilihan2?->kode ?? '-' }})
                    </td>
                </tr>
            </table>

            <h6 class="fw-bold mb-3">A. Data Pribadi Calon Siswa</h6>
            <table class="table table-bordered align-middle mb-4">
                <tr>
                    <th class="bg-light" style="width: 30%;">Nama Lengkap</th>
                    <td>{{ $siswa->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th class="bg-light">NISN / NIK</th>
                    <td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Tempat, Tanggal Lahir</th>
                    <td>{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Jenis Kelamin</th>
                    <td>{{ ($siswa->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Asal Sekolah</th>
                    <td>{{ $siswa->asal_sekolah ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">No. WhatsApp / HP</th>
                    <td>{{ $siswa->no_hp ?? '-' }}</td>
                </tr>
            </table>

            <h6 class="fw-bold mb-3">B. Data Orang Tua</h6>
            @php($orangTua = $siswa->orangTua->keyBy('hubungan'))
            <table class="table table-bordered align-middle mb-4">
                <tr>
                    <th class="bg-light" style="width: 30%;">Nama Ayah / Pekerjaan</th>
                    <td>{{ $orangTua->get('ayah')?->nama ?? '-' }} ({{ $orangTua->get('ayah')?->pekerjaan ?? '-' }})</td>
                </tr>
                <tr>
                    <th class="bg-light">Nama Ibu / Pekerjaan</th>
                    <td>{{ $orangTua->get('ibu')?->nama ?? '-' }} ({{ $orangTua->get('ibu')?->pekerjaan ?? '-' }})</td>
                </tr>
                @if ($orangTua->has('wali'))
                    <tr>
                        <th class="bg-light">Nama Wali / Pekerjaan</th>
                        <td>{{ $orangTua->get('wali')?->nama ?? '-' }} ({{ $orangTua->get('wali')?->pekerjaan ?? '-' }})</td>
                    </tr>
                @endif
            </table>

            <div class="row mt-5">
                <div class="col-6 text-center">
                    <p class="mb-5">Calon Peserta Didik,</p>
                    <br><br>
                    <p class="fw-bold text-decoration-underline mb-0">{{ $siswa->nama_lengkap }}</p>
                </div>
                <div class="col-6 text-center">
                    <p class="mb-5">Panitia PPDB SMAKNIS,</p>
                    <br><br>
                    <p class="fw-bold text-decoration-underline mb-0">( Admin / Panitia )</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>