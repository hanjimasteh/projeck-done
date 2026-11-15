@props([
    'variant' => 'secondary',
    'pill' => false,
])

@php($classes = 'badge text-bg-'.$variant.($pill ? ' rounded-pill' : ''))

<span {{ $attributes->class($classes) }}>{{ $slot }}</span>
