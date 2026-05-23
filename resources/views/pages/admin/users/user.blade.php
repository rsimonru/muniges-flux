<?php

use App\Models\Permission;
use App\Models\User;
use App\Models\Company;
use App\Models\Level;
use App\Models\Menu;
use App\Models\ModelHasPermission;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\Schedule;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\Livewire;

new class extends Component {
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $active = true;
    public bool $isEditing = false;
    public string $activeTab = 'menus';
    public $levels;
    public $groups;
    public $menus;
    public $permissions;
    public $schedules;
    public array $user_permissions = [];
    public array $user_menus = [];
    public array $user_fmenus = [];
    public array $user_groups = [];
    public array $user_schedules = [];
    public int $level_number = 0;
    public ?int $level_id = null;
    public string $created_at = '';

    /**
     * Mount the component
     */
    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->user = User::emtGet($id);
            $this->isEditing = true;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->active = $this->user->active;
            $this->user_permissions = ($this->user) ? $this->user->permissions->where('id', '<', 1000)->pluck('id')->toArray() : [];
            $this->user_menus = ($this->user) ? $this->user->menus->pluck('id')->toArray() : [];
            $this->user_fmenus = ($this->user) ? $this->user->fsubmenus->pluck('id')->toArray() : [];
            $this->user_groups = ($this->user) ? $this->user->roles->pluck('id')->toArray() : [];
            $this->user_schedules = ($this->user) ? $this->user->schedules->pluck('id')->toArray() : [];
            $this->level_number = $this->user->level_number;
            $this->level_id = $this->user->level_id;
            $this->created_at = $this->user->created_at?->format('Y-m-d H:i:s') ?? '';
        }
    }

    /**
     * Get component state
     */
    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('generic.edit_user')
                : __('generic.create_user'),
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => null],
                ['label' => trans_choice('generic.users', 2), 'url' => route('admin.users.index')],
                ['label' => $this->isEditing ? __('generic.edit') : __('generic.new'), 'url' => null],
            ],
            'levels' => $this->levels(),
            'groups' => $this->groups(),
            'menus' => $this->menus(),
            'permissions' => $this->permissions(),
            'schedules' => $this->schedules(),
        ];
    }
    public function levels()
    {
        return Level::all();
    }
    public function groups()
    {
        return Role::all();
    }
    public function permissions()
    {
        return Permission::where('class', 'permission')->get();
    }
    public function menus()
    {
        return Menu::emtGetUser(0, 0, session('townhall_id', null), $this->level_number);
    }
    public function schedules()
    {
        return Schedule::emtGet(records_in_page: -1, filters: ['townhalls_id' => session('townhall_id', null)]);
    }

    private function validateRules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user?->id),
            ],
            'level_id' => ['required', 'exists:levels,id'],
            'active' => ['boolean'],
        ];

        // Password is required for new users, optional for editing
        if (!$this->isEditing) {
            $rules['password'] = ['required', 'string', Password::defaults(), 'confirmed'];
        } elseif ($this->password) {
            $rules['password'] = ['string', Password::defaults(), 'confirmed'];
        }

        return $this->validate($rules);
    }
    public function checkUserLevel()
    {
        $validated = $this->validateRules();

        if ($this->isEditing) {
            $levels = Level::all()->keyBy('id');
            if ($this->level_number > $levels[$validated['level_id']]->level) {
                Flux::modal('confirm-downgrade')->show();
            } else {
                $this->save($validated);
            }
        } else {
            $this->save($validated);
        }
    }
    /**
     * Save user (create or update)
     */
    public function save(?array $validated = null): void
    {
        if (empty($validated)) {
            $validated = $this->validateRules();
        }

        if ($this->isEditing) {
            // Update existing user
            $this->user->name = $validated['name'];
            $this->user->email = $validated['email'];
            $this->user->active = $validated['active'];

            if (!empty($validated['password'])) {
                $this->user->password = Hash::make($validated['password']);
            }

            $this->user->save();
            $this->user->townhalls()->updateExistingPivot(session('townhall_id', null), ['level_id' => $this->level_id, 'updated_at' => now()]);
        } else {
            // Create new user
            $this->user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'active' => true,
            ]);
            $this->user->townhalls()->sync([session('townhall_id', null) => ['level_id' => $this->level_id, 'created_at' => now(), 'updated_at' => now()]]);
        }

        $levels = Level::all()->keyBy('id');
        $this->level_number = $levels[$this->level_id]->level;

        $user_menus = $this->user_permissions;
        foreach ($this->user_fmenus as $user_fmenu) {
            $user_fmenu = $user_fmenu * 1;
            $user_menus[$user_fmenu] = ['favorite' => 1];
        }
        foreach ($this->user_menus as $user_menu) {
            $user_menu = $user_menu * 1;
            if (in_array($user_menu, $this->user_fmenus)) {
                $user_menus[$user_menu] = ['favorite' => 1];
            } else {
                $user_menus[$user_menu] = ['favorite' => 0];
            }
        }
        $this->user->permissions()->sync($user_menus);
        $this->user->roles()->sync($this->user_groups);
        $this->user->schedules()->sync($this->user_schedules);

        $roles_ids = Role::select('roles.*')
            ->join('levels as l', 'l.id', 'roles.level_id')
            ->where('townhalls_id', session('townhall_id', null))
            ->where('l.level', '>', $this->level_number)
            ->get()->keyBy('id')->keys()->all();
        ModelHasRole::whereIn('role_id', $roles_ids)->where('model_type', User::class)->where('model_id', $this->user->id)->delete();

        $permissions_ids = Permission::where('level', '>', $this->level_number)->get()->keyBy('id')->keys()->all();
        ModelHasPermission::whereIn('permission_id', $permissions_ids)->where('model_type', User::class)->where('model_id', $this->user->id)->delete();

        Flux::toast(
            variant: 'success',
            text: $this->isEditing ? __('admin.user_updated') : __('admin.user_created')
        );

        // Force a full Livewire remount so mount() runs again and all state is refreshed.
        $this->redirectRoute('admin.users.edit', ['id' => $this->user->id], navigate: true);
    }

    /**
     * Cancel and return to users list
     */
    public function cancel(): void
    {
        $this->redirect(route('admin.users.index'), navigate: true);
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout
        :title="$pageTitle"
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="filled" href="{{ route('admin.users.index') }}">
                {{ __('generic.button_cancel') }}
            </flux:button>

            <flux:button type="button" icon="save" size="sm" variant="primary" color="blue" wire:click="checkUserLevel">
                {{ __('generic.button_save') }}
            </flux:button>
        </x-slot:buttons>
        <!-- Two-column layout: Form on left, Tabs on right (desktop), stacked on mobile -->
        <div class="flex flex-col gap-6 lg:flex-row h-full">
            <!-- Left Column: Main Form -->
            <div class="w-full lg:w-1/2">
                <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name Field -->
                    <flux:field class="grid col-span-1 lg:col-span-2">
                        <flux:label>{{ __('generic.name') }}</flux:label>
                        <flux:input
                            wire:model="name"
                            type="text"
                            autofocus
                            autocomplete="name"
                            placeholder="{{ __('admin.name_placeholder') }}"
                        />
                        <flux:error name="name" />
                    </flux:field>
                    <!-- Email Field -->
                    <flux:field>
                        <flux:label>E-mail</flux:label>
                        <flux:input
                            wire:model="email"
                            type="email"
                            autocomplete="email"
                            placeholder="{{ __('admin.email_placeholder') }}"
                        />
                        <flux:error name="email" />
                    </flux:field>
                    <flux:select wire:model="level_id" variant="combobox"
                        label="{{ __('generic.type') }} {{ $this->level_number }}"
                        placeholder="{{ __('generic.choose') }}">
                        @foreach ($levels as $level)
                            <flux:select.option :value="$level->id">{{ $level->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <!-- Password Field -->
                    <flux:field>
                        <flux:label>
                            {{ __('auth.password') }}
                            @if($isEditing)
                                <span class="text-xs font-normal text-zinc-500 ml-2">({{ __('admin.leave_blank_keep_current') }})</span>
                            @endif
                        </flux:label>
                        <flux:input
                            wire:model="password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="{{ __('auth.password_placeholder') }}"
                        />
                        <flux:error name="password" />
                    </flux:field>
                    <!-- Confirm Password Field -->
                    <flux:field>
                        <flux:label>{{ __('auth.password_confirmation') }}</flux:label>
                        <flux:input
                            wire:model="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            placeholder="{{ __('auth.confirm_password_placeholder') }}"
                        />
                        <flux:error name="password_confirmation" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('generic.active') }}</flux:label>
                        <flux:switch wire:model="active" />
                        <flux:error name="active" />
                    </flux:field>
                </form>
            </div>

            <!-- Right Column: Tabs (desktop), Below on mobile -->
            <div class="w-full lg:w-1/2 h-full">
                <flux:tab.group class="space-y-6">
                    <flux:tabs>
                        <flux:tab name="groups-tab" icon="user">{{ trans_choice('generic.groups', 2) }} <flux:badge>{{ count($user_groups) }}</flux:badge></flux:tab>
                        <flux:tab name="menus-tab" icon="bars-3">{{ trans_choice('generic.menus', 2) }}<flux:badge>{{ count($user_menus) }}</flux:badge></flux:tab>
                        <flux:tab name="permissions-tab" icon="key">{{ trans_choice('generic.permissions', 2) }}<flux:badge>{{ count($user_permissions) }}</flux:badge></flux:tab>
                        <flux:tab name="schedules-tab" icon="calendar">{{ trans_choice('generic.schedules', 2) }}<flux:badge>{{ count($user_schedules) }}</flux:badge></flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="groups-tab" class="!pt-1 max-h-[calc(100vh-12rem)] overflow-y-auto">
                        <!-- Groups list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_groups">
                            @foreach ($groups as $group)
                                <flux:checkbox value="{{ $group['id'] }}" label="{{ $group['description'] }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                    <flux:tab.panel name="menus-tab" class="!pt-1 max-h-[calc(100vh-14rem)] overflow-y-auto">
                        <!-- Menus list with vertical scroll -->
                        <div>
                            @foreach ($menus as $id => $menu)
                                @php
                                    $menu_desc = json_decode($menu['description'], true);
                                    $menu_desc = $menu_desc[app()->getLocale()] ?? $menu_desc[config('app.fallback_locale')];
                                @endphp
                                <div class="h-full mt-2">
                                    <flux:brand name="{{ $menu_desc }}" href="#">
                                        <x-slot name="logo">
                                            <flux:icon name="{{ str_replace('heroicon-o-', '',$menu['hero_icon']) }}" />
                                        </x-slot>
                                    </flux:brand>
                                </div>
                                @if (length($menu['submenu'])>0)
                                    <div class="w-full grid grid-cols-3">
                                        @foreach ($menu['submenu'] as $id2 => $submenu)
                                            @php
                                                $submenu_desc = json_decode($submenu['description'], true);
                                                $submenu_desc = $submenu_desc[app()->getLocale()] ?? $submenu_desc[config('app.fallback_locale')];
                                            @endphp
                                                <div class="col-span-2 mt-1">
                                                    <flux:checkbox label="{{ $submenu_desc }}" value="{{ $submenu['permission']['id'] }}" wire:model="user_menus" />
                                                </div>
                                                <div class="col-span-1 mt-1 flex flex-nowrap">
                                                    <flux:icon name="star" variant="solid" class="size-4 text-yellow-500" />
                                                    <flux:checkbox value="{{ $submenu['permission']['id'] }}" wire:model="user_fmenus"/>
                                                </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </flux:tab.panel>
                    <flux:tab.panel name="permissions-tab" class="!pt-1 max-h-[calc(100vh-12rem)] overflow-y-auto">
                        <!-- Permissions list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_permissions">
                            @foreach ($permissions as $permission)
                                <flux:checkbox value="{{ $permission['id'] }}" label="{{ $permission['description'] }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                    <flux:tab.panel name="schedules-tab" class="!pt-1 max-h-[calc(100vh-12rem)] overflow-y-auto">
                        <!-- Schedules list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_schedules">
                            @foreach ($schedules as $schedule)
                                <flux:checkbox value="{{ $schedule['id'] }}" label="{{ $schedule['description'] }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </div>

        <x-slot:modals>
            <flux:modal name="confirm-downgrade">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('admin.downgrade_user') }}</flux:heading>
                    </div>
                    <div>
                        <p>{{ __('admin.downgrade_user_info') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button size="sm" variant="ghost">{{ __('generic.button_cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:modal.close>
                            <flux:button size="sm" variant="primary" icon="save" wire:click="save()">
                                {{ __('generic.button_save') }}
                            </flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
        </x-slot:modals>
    </x-document.layout>
</section>
