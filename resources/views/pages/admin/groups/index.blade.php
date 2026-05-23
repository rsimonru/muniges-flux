<?php

use App\Exports\GroupsExport;
use App\Models\Role;
use App\Models\Select;
use App\Traits\WithFilters;
use App\Traits\WithSorting;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithFilters;
    use WithSorting;

    public array $selected = [];
    public $showDeleteModal = false;
    public $deleteId = null;

    public function mount() {
        $this->filter_name = 'admin-groups';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'name';
        $this->sortDirection = $this->filter['order'] ?? 'asc';
    }

    /**
     * Get the groups list
     */
    public function with(): array
    {
        $this->getFilters();
        return [
            'groups' => $this->getGroups(),
        ];
    }

    /**
     * Get filtered and sorted groups
     */
    public function getGroups()
    {
        $groups = Role::emtGet(
            records_in_page: 20,
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['level'],
            withCount: ['users']
        );

        return $groups;
    }

    #[Computed]
    public function levels()
    {
        return Select::emtGet('levels');
    }

    /**
     * Reset search
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function downloadExcel()
    {
        $file_name = 'Grupos.xlsx';
        $export = new GroupsExport($this->getMergeFilters());
        $export->fileName = $file_name;
        return $export->download();
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $group = Role::find($this->deleteId);
            if ($group) {
                $group->delete();
                Flux::toast(__('groups.group_deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('generic.groups', 2)"
        :search="'filter.search'"
        :search-placeholder="__('generic.name')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => null],
            ['label' => trans_choice('generic.groups',2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="green" icon="file-excel" class="cursor-pointer" wire:click="downloadExcel">
                <span class="hidden md:inline">Excel</span>
            </flux:button>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="user-round-plus" href="{{ route('admin.groups.create') }}">
                {{ __('generic.button_new') }}
            </flux:button>
        </x-slot:buttons>
        <!-- Groups Table (Flux component) -->
        <flux:checkbox.group>
            <flux:table container:class="h-[calc(100vh-16rem)] md:h-[calc(100vh-12rem)]" :paginate="$groups">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">{{ __('generic.name') }}</flux:table.column>
                    <flux:table.column align="center" >{{ __('generic.type') }}</flux:table.column>
                    <flux:table.column align="center" >{{ __('generic.components') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">{{ __('generic.created') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($groups as $group)
                        <flux:table.row :key="$group->id">
                            <flux:table.cell>
                                <div class="flex items-center justify-center">
                                    <flux:checkbox wire:model="selected" value="{{ $group->id }}" />
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold cursor-pointer" :href="route('admin.groups.edit', $group->id)" wire:navigate>
                                <div class="flex items-center gap-3">
                                    <flux:avatar
                                        size="sm"
                                        :src="'https://ui-avatars.com/api/?name=' . urlencode($group->description) . '&background=random'"
                                    />
                                    <div>
                                        {{ $group->description }}
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>{{ $group->level->name }}</flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge color="blue" size="sm">
                                    {{ $group->users_count }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:tooltip :content="$group->created_at ? $group->created_at->format('Y-m-d H:i') : ''">
                                    {{ $group->created_at ? $group->created_at->format('d-m-Y H:i') : '' }}
                                </flux:tooltip>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-2 justify-center">
                                    <flux:button size="sm" variant="filled" icon="pencil" :href="route('admin.groups.edit', $group->id)" wire:navigate />
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId={{ $group->id }};"
                                        tooltip="{{ __('generic.button_delete') }}"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <div class="flex flex-col items-center gap-2 py-8">
                                    <flux:icon.users class="size-12" variant="outline" />
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('generic.no_records_found') }}
                                    </p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:checkbox.group>
        <x-slot:modals>
            <flux:modal name="filter-records" flyout>
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('generic.filter') }}</flux:heading>
                    </div>

                    <flux:pillbox size="sm" wire:model="filter.level_id" variant="combobox" multiple
                        label="{{ __('generic.type') }}"
                        placeholder="{{ __('generic.choose') }}">
                        @foreach ($this->levels as $level)
                            <flux:pillbox.option :value="$level['value']">{{ $level['option'] }}</flux:pillbox.option>
                        @endforeach
                    </flux:pillbox>

                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button size="sm" variant="ghost" wire:click="deleteFilter">{{ __('generic.button_delete_filter') }}</flux:button>
                        </flux:modal.close>
                        <flux:button size="sm" variant="primary" wire:click="searchRecords(true)">
                            {{ __('generic.filter') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
            <x-delete-modal wire:model="showDeleteModal" />
        </x-slot:modals>
    </x-documents.layout>
</section>
