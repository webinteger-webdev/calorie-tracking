@props([
    'message' => '',
    'messageDescription' => '',
    'type' => 'info',
    'duration' => 3000,
    'id' => 'toast-' . uniqid(),
    'icon' => null,
    'iconClass' => 'h-6 w-6 mr-2',
    'alertClass' => '',
    'toastClass' => '',
])

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ $duration }})"
    x-show="show"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    id="{{ $id }}"
    class="toast toast-top toast-end z-50 {{ $toastClass }}"
>
    <div class="alert alert-{{ $type }} {{ $alertClass }}">
        @if($icon)
            <x-daisyui.icon name="{{ $icon }}" class="{{ $iconClass }}" />
        @endif
        <div>
            @if($message)
                <h3 class="font-bold">{{ $message }}</h3>
            @endif
            @if($messageDescription)
                <div class="text-xs">{{ $messageDescription }}</div>
            @endif
        </div>
        <x-daisyui.button @click="show = false" class="btn btn-sm btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </x-daisyui.button>
    </div>
</div>
