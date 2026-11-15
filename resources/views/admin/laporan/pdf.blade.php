<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pendaftaran PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header-sekolah { border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
        .no-print { display: none; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            table { margin: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-sekolah text-center mb-4">
        <h4 class="fw-bold text-uppercase mb-1">Panitia Penerimaan Peserta Didik Baru (PPDB)</h4>
        <h3 class="fw-bold text-uppercase text-primary mb-1">SMA Negeri 1</h3>
        <p class="text-muted small">Tahun Ajaran {{ $tahunAjaran ?? date('Y') }}</p>
    </div>

    <h5 class="fw-bold text-uppercase text-primary mb-3">LAPORAN REKAPITULASI PENDAFTAR PPDB</h5>

    <div class="mb-3">
        <strong>Jumlah Total Pendaftar:</strong> {{ $pendaftaran->count() }} orang
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>NISN</th>
                <th>Program 1</th>
                <th>Program 2</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendaftaran as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_pendaftaran ?? '-' }}</td>
                    <td>{{ $item->calonSiswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $item->calonSiswa->nisn ?? '-' }}</td>
                    <td>{{ $item->programPilihan1?->nama ?? '-' }}</td>
                    <td>{{ $item->programPilihan2?->nama ?? '-' }}</td>
                    <td>{{ $item->status?->label() ?? '-' }}</td>
                    <td>{{ $item->tanggal_pendaftaran?->format('d F Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-5 text-center">
        <p class="small text-muted">Dikirim oleh Sistem PPDB SMAKNIS | {{ date('d F Y H:i') }}</p>
    </div>
</div>

</body>
</html>