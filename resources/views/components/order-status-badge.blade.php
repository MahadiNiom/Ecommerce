@props(['status'])

@php
    $classes = match ($status instanceof \App\Enums\OrderStatus ? $status->value : '') {
        'pending' => 'badge-amber',
        'processing' => 'badge-blue',
        'shipped' => 'badge-green',
        'delivered' => 'badge-green',
        'cancelled' => 'badge-red',
        default => 'badge-stone',
    };
@endphp

<span class="{{ $classes }}">{{ $status->label() }}</span>