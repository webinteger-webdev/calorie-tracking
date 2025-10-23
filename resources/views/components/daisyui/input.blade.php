@props([
    'type' => 'text',
    'id' => '',
    'name' => '',
    'label' => '',
    'description' => '',
    'variant' => '',
    'size' => '',
    'errors' => [],
    'icon' => null, // Проп для имени иконки
])

@push('tailwindcss-safelist')
<div class="
    input
    input-ghost
    input-neutral
    input-primary
    input-secondary
    input-accent
    input-info
    input-success
    input-warning
    input-error
    input-xs
    input-sm
    input-md
    input-lg
    input-xl
    ">
</div>
@endpush

@if(!empty($label) || !empty($legend))
<fieldset class="fieldset">
@endif

@if(!empty($label))
    <legend class="fieldset-legend {{ (!empty($errors) && $errors->has($name)) ? ' text-error' : '' }}">{{ $label }}</legend>
@endif

<div class="relative">
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'input w-full' . (!empty($icon) ? ' pl-10' : '') .
                (!empty($variant) ? " input-{$variant}" : '') .
                (!empty($size) ? " input-{$size}" : '') .
                (!empty($errors) && $errors->has($name) ? ' input-error' : '')
        ]) }}
        {{ $attributes->whereDoesntStartWith('class') }}
    />
    @if($icon)
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-daisyui.icon name="{{ $icon }}" class="h-5 w-5 text-gray-400" />
        </div>
    @endif
</div>

@if(!empty($description))
    <p class="fieldset-label">{{ $description }}</p>
@endif

@if(!empty($errors) && $errors->has($name))
    <span class="fieldset-label text-error">
        <ul class="list-disc list-inside">
        @foreach($errors->get($name) as $message)
            <li>{{ $message }}</li>
        @endforeach
        </ul>
    </span>
@endif

@if(!empty($label) || !empty($legend))
</fieldset>
@endif
