<?php

use App\Exports\UsersExport;
use App\Models\Select;
use App\Models\User;
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
        $this->filter_name = 'admin-users';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'name';
        $this->sortDirection = $this->filter['order'] ?? 'asc';
    }

    /**
     * Get the users list
     */
    public function with(): array
    {
        $this->getFilters();
        return [
            'users' => $this->getUsers(),
        ];
    }

    /**
     * Get filtered and sorted users
     */
    public function getUsers()
    {
        $users = User::emtGet(
            records_in_page: 20,
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['level'],
        );
        return $users;
    }

    #[Computed]
    public function roles()
    {
        return Select::emtGet(
            vcSelect: 'roles',
        );
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
        $file_name = 'Usuarios.xlsx';
        $export = new UsersExport($this->getMergeFilters());
        $export->fileName = $file_name;
        return $export->download();
    }
    public function delete(): void
    {
        if ($this->deleteId) {
            $user = User::find($this->deleteId);
            // if ($user) {
            //     $user->delete();
            //     Flux::toast(__('users.user_deleted'));
            //     $this->dispatch('$refresh'); // Refresh computed property
            // }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('admin.users', 2)"
        :search="'filter.search'"
        :search-placeholder="__('admin.name_email')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => null],
            ['label' => trans_choice('generic.users',2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="green" icon="file-excel" class="cursor-pointer" wire:click="downloadExcel">
                <span class="hidden md:inline">Excel</span>
            </flux:button>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="user-round-plus" href="{{ route('admin.users.create') }}">
                {{ __('generic.button_new') }}
            </flux:button>
        </x-slot:buttons>
        <!-- Users Table (Flux component) -->
        <flux:checkbox.group>
            <flux:table container:class="h-[calc(100vh-16rem)] md:h-[calc(100vh-12rem)]" :paginate="$users">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">{{ __('generic.name') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'email'" :direction="$sortDirection" wire:click="sortBy('email')">E-mail</flux:table.column>
                    <flux:table.column align="center" >{{ __('generic.active') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">{{ __('generic.created') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'last_login'" :direction="$sortDirection" wire:click="sortBy('last_login')">{{ __('admin.last_login') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell>
                                <div class="flex items-center justify-center">
                                    <flux:checkbox wire:model="selected" value="{{ $user->id }}" />
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold cursor-pointer" :href="route('admin.users.edit', $user->id)" wire:navigate>
                                <div class="flex items-center gap-3">
                                    <flux:avatar
                                        size="sm"
                                        :src="'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random'"
                                    />
                                    <div>
                                        {{ $user->name }}
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->level ? $user->level->name : '' }}</p>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>{{ $user->email }}</flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge :color="$user->active ? 'green' : 'red'" size="sm">
                                    {{ $user->active ? __('generic.true') : __('generic.false') }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:tooltip :content="$user->created_at ? $user->created_at->format('Y-m-d H:i') : ''">
                                    {{ $user->created_at ? $user->created_at->format('d-m-Y H:i') : '' }}
                                </flux:tooltip>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:tooltip :content="$user->last_login ? $user->last_login->format('Y-m-d H:i') : ''">
                                    {{ $user->last_login ? $user->last_login->format('d-m-Y H:i') : '' }}
                                </flux:tooltip>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-2 justify-center">
                                    <flux:button size="sm" variant="filled" icon="pencil" :href="route('admin.users.edit', $user->id)" wire:navigate />

                                    @if($user->id !== auth()->id())
                                        <flux:button
                                            size="sm"
                                            variant="danger"
                                            icon="trash"
                                            x-on:click="$wire.showDeleteModal = true; $wire.deleteId={{ $user->id }};"
                                            tooltip="{{ __('generic.button_delete') }}"
                                        />
                                    @endif
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

                    <flux:input size="sm" wire:model="filter.search" label="{{ __('generic.search') }}" placeholder="{{ __('admin.name_email') }}" clearable />

                    <flux:pillbox size="sm" wire:model="filter.group_id" variant="combobox" multiple
                        label="{{ trans_choice('generic.groups', 2) }}"
                        placeholder="{{ __('generic.choose') }}">
                        @foreach ($this->roles as $role)
                            <flux:pillbox.option :value="$role['value']">{{ $role['option'] }}</flux:pillbox.option>
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
