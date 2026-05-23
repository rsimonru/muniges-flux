@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="{{ session('townhall_data')['short_name'] }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <img src="/storage/aytos/{{ session('townhall_id') }}/logo_peq.png" class="mt-1" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ session('townhall_data')['short_name'] }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <img src="/storage/aytos/{{ session('townhall_id') }}/logo_peq.png" width="30" class="mt-1" />
        </x-slot>
    </flux:brand>
@endif
