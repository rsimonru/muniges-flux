<?php

use Livewire\Component;

new class extends Component {
    public $showDeleteModal = false;

}; ?>

<flux:modal class="md:w-96" wire:model.self="showDeleteModal">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('generic.button_delete') }}</flux:heading>
            <flux:text class="mt-2">{{ __('generic.confirm_delete') }}</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('generic.button_cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="button" variant="danger" wire:click="doDelete">{{ __('generic.button_delete') }}</flux:button>
        </div>
    </div>
</flux:modal>
