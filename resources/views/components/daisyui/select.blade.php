@props([
    'name' => '',
    'id' => '',
    'label' => '',
    'description' => '',
    'options' => [],
    'value' => null,
    'placeholder' => '',
    'variant' => '',
    'size' => '',
    'icon' => null,
    'iconClass' => 'h-5 w-5 opacity-50',
    'selectClass' => '',
    'optionValue' => 'value',
    'optionLabel' => 'label',
    'errors' => [],
    'disabled' => false,
])

<fieldset class="fieldset">
    @if($label)
        <legend class="fieldset-legend {{ (!empty($errors) && $errors->has($name)) ? 'text-error' : '' }}">{{ $label }}</legend>
    @endif

    <div class="relative">
        <select
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'select select-md w-full' .
                    (!empty($variant) ? " select-{$variant}" : '') .
                    (!empty($size) ? " select-{$size}" : '') .
                    (!empty($selectClass) ? " {$selectClass}" : '') .
                    (!empty($errors) && $errors->has($name) ? ' select-error' : '') .
                    (!empty($icon) ? ' pl-10' : '')
            ]) }}
            name="{{ $name }}"
            id="{{ $id }}"
        >
            @if($placeholder)
                <option value="" disabled {{ is_null($value) ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif
            @foreach($options as $option)
                @php
                    $optionVal = is_array($option) ? $option[$optionValue] : $option;
                    $optionTxt = is_array($option) ? $option[$optionLabel] : $option;
                @endphp
                <option value="{{ $optionVal }}" {{ ($value == $optionVal) ? 'selected' : '' }}>
                    {{ $optionTxt }}
                </option>
            @endforeach
        </select>
        @if($icon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <x-daisyui.icon name="{{ $icon }}" class="{{ $iconClass }}" />
            </div>
        @endif
    </div>

    @if($description)
        <span class="label-text-alt">{{ $description }}</span>
    @endif

    @if(!empty($errors) && $errors->has($name))
        <span class="label-text-alt text-error">
            {{ $errors->first($name) }}
        </span>
    @endif
</fieldset>
