<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @php
                $menus = session('menus');
            @endphp
            <flux:navlist variant="outline" class="h-[calc(100vh-9rem)] overflow-y-auto overflow-x-hidden">
            @foreach ($menus as $menu)
                @php
                    $menu_desc = json_decode($menu['description'],true);
                    $menu_desc = $menu_desc[app()->getLocale()] ?? $menu_desc[config('app.fallback_locale')];
                    $group = '';
                    $last_group = '';
                    $opened_group = false;
                @endphp
                <flux:navlist.group :heading="$menu_desc" class="grid" expandable :expanded="request()->is($menu['route']) ? true : false">
                @foreach ($menu['submenu'] as $submenu)
                    @php
                        $group_description = ($submenu['group_description']) ? json_decode($submenu['group_description'],true) : json_decode($menu['description'],true);
                        $group_description = $group_description[app()->getLocale()] ?? $group_description[config('app.fallback_locale')];
                    @endphp

                    @if (!empty($submenu['group']) && $submenu['group'] != $menu_desc && ($loop->first || (!empty($submenu['group']) && $submenu['group'] != $group)))
                        @php
                            $last_group = $group;
                            $group = $submenu['group'];
                            $opened_group = true;
                        @endphp
                        <flux:navlist.item icon:trailing="bars-2"> {{ $group_description }}</flux:navlist.item>
                    @endif

                    @php
                        $submenu_desc = json_decode($submenu['description'],true);
                        $submenu_desc = $submenu_desc[app()->getLocale()] ?? $submenu_desc[config('app.fallback_locale')];
                        $icon = ($submenu['hero_icon']) ? $submenu['hero_icon'] : $menu['hero_icon'];
                        $icon = str_replace('heroicon-o-', '', $icon);
                    @endphp
                    <flux:navlist.item :icon="$icon" :href="$submenu['route']" :current="request()->is($submenu['route'])" wire:navigate>{{ $submenu_desc }}</flux:navlist.item>

                    @if ($opened_group && $last_group != $group)
                        @php
                            $opened_group = false;
                        @endphp
                    @endif
                @endforeach
                </flux:navlist.group>
            @endforeach
            </flux:navlist>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                        <flux:radio value="light" icon="sun"></flux:radio>
                        <flux:radio value="dark" icon="moon"></flux:radio>
                        <flux:radio value="system" icon="computer-desktop"></flux:radio>
                    </flux:radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
