@props([
    'name' => '',
    'class' => 'h-4 w-4 opacity-50',
])

@php
    $icons = [
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="' . $class . '"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
    ];
@endphp

{!! $icons[$name] ?? '' !!}
