@props([
    'href' => null,
    'variant' => 'primary',
    'outline' => false,
    'size' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $classes = collect([
        'btn',
        $outline ? 'btn-outline-'.$variant : 'btn-'.$variant,
        $size ? 'btn-'.$size : null,
        'fw-semibold',
    ])->filter()->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<i class="bi bi-{{ $icon }} me-1"></i>@endif{{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)<i class="bi bi-{{ $icon }} me-1"></i>@endif{{ $slot }}
    </button>
@endif
