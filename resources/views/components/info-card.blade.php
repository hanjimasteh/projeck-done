@props([
    'icon' => 'info-circle',
    'title' => null,
    'href' => null,
    'variant' => 'primary',
])

@if ($href)
    <a href="{{ $href }}" class="info-card d-block p-4 text-reset">
        <div class="info-icon mb-3 text-{{ $variant }}"><i class="bi bi-{{ $icon }}"></i></div>
        @if ($title)
            <h3 class="h5 fw-bold">{{ $title }}</h3>
        @endif
        <div class="text-muted-soft">{{ $slot }}</div>
    </a>
@else
    <div class="info-card p-4">
        <div class="info-icon mb-3 text-{{ $variant }}"><i class="bi bi-{{ $icon }}"></i></div>
        @if ($title)
            <h3 class="h5 fw-bold">{{ $title }}</h3>
        @endif
        <div class="text-muted-soft">{{ $slot }}</div>
    </div>
@endif
