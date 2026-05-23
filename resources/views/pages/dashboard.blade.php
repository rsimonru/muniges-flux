<?php

use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{

    public function with(): array
    {
        return [
            'pageTitle' => __('general.dashboard'),
            'breadcrumbs' => [
                ['label' => __('general.dashboard'), 'url' => null],
            ],
        ];
    }

}; ?>

<section class="w-full h-full">
    <x-document.layout :title="$pageTitle" :breadcrumbs="$breadcrumbs">
        <x-slot:buttons>
        </x-slot:buttons>
        <div class="flex flex-col gap-6 lg:flex-row">
            <div class="grid gap-4 grid-cols-1 lg:grid-cols-2 w-full">
                <div class="relative aspect-video rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-accent-foreground">

                </div>
                <div class="relative aspect-video rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-accent-foreground">

                </div>
            </div>
        </div>
    </x-document.layout>
</section>
