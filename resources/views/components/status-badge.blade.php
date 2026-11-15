@props(['status'])

@php
    $label = null;
    $class = 'text-bg-secondary';

    if ($status instanceof \App\Enums\StatusPendaftaran) {
        $label = $status->label();
        $class = $status->badgeClass();
    } elseif ($status instanceof \App\Enums\StatusDokumen) {
        $label = $status->label();
        $class = $status->badgeClass();
    } elseif ($status instanceof \BackedEnum) {
        $label = (string) $status->value;
    } elseif (filled($status)) {
        $pendaftaran = \App\Enums\StatusPendaftaran::tryFrom((string) $status);
        $dokumen = $pendaftaran ? null : \App\Enums\StatusDokumen::tryFrom((string) $status);

        if ($pendaftaran) {
            $label = $pendaftaran->label();
            $class = $pendaftaran->badgeClass();
        } elseif ($dokumen) {
            $label = $dokumen->label();
            $class = $dokumen->badgeClass();
        } else {
            $label = (string) $status;
        }
    }
@endphp

@if ($label)
    <span {{ $attributes->class(['badge', $class]) }}>{{ $label }}</span>
@else
    <span {{ $attributes->class(['badge', 'text-bg-secondary']) }}>Belum ada status</span>
@endif
