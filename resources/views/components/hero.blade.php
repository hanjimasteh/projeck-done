@props([
    'badge' => null,
    'title' => null,
    'accent' => null,
    'description' => null,
    'compact' => false,
])

<section class="hero-ppdb {{ $compact ? 'hero-ppdb-compact' : '' }}">
    <div class="container hero-content py-5">
        <div class="text-center mx-auto" style="max-width: 54rem;">
            @if ($badge)
                <span class="badge hero-badge rounded-pill px-3 py-2 mb-4">
                    <span class="hero-badge-dot"></span>
                    {{ $badge }}
                </span>
            @endif

            <h1 class="hero-title mb-3">
                {{ $title }}
                @if ($accent)
                    <span class="hero-title-accent d-block">{{ $accent }}</span>
                @endif
            </h1>

            @if ($description)
                <p class="hero-lead mx-auto mb-4" style="max-width: 42rem;">{{ $description }}</p>
            @endif

            @isset($actions)
                <div class="hero-actions mb-4">{{ $actions }}</div>
            @endisset

            @isset($stats)
                <div class="hero-stats mt-5">{{ $stats }}</div>
            @endisset
        </div>
    </div>
</section>
