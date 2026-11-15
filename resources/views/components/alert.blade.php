@props(['fluid' => false])

@php
    $messages = [
        'success' => ['class' => 'success', 'icon' => 'check-circle-fill'],
        'error' => ['class' => 'danger', 'icon' => 'exclamation-octagon-fill'],
        'warning' => ['class' => 'warning', 'icon' => 'exclamation-triangle-fill'],
        'info' => ['class' => 'info', 'icon' => 'info-circle-fill'],
    ];
    $hasSessionMessage = collect(array_keys($messages))->contains(fn ($key) => session()->has($key));
    $hasErrors = isset($errors) && $errors->any();
@endphp

@if ($hasSessionMessage || $hasErrors)
    <div class="{{ $fluid ? '' : 'container' }} pt-3">
        @foreach ($messages as $key => $meta)
            @if (session()->has($key))
                <div class="alert alert-{{ $meta['class'] }} alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="d-flex gap-2">
                        <i class="bi bi-{{ $meta['icon'] }} mt-1"></i>
                        <div class="flex-grow-1">{{ session($key) }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
        @endforeach

        @if ($hasErrors)
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex gap-2">
                    <i class="bi bi-exclamation-octagon-fill mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>Periksa kembali data berikut:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
    </div>
@endif
