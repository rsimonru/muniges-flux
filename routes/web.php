<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

Route::get('/', function () {
    return response()->redirectTo(route('login'));
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/test', [TestController::class, 'test'])->name('test');

    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');

    // Admin Users
    Route::livewire('admin/users', 'pages::admin.users.index')->name('admin.users.index');
    Route::livewire('admin/users/create', 'pages::admin.users.user')->name('admin.users.create');
    Route::livewire('admin/users/{id}/edit', 'pages::admin.users.user')->name('admin.users.edit');

    // Admin Groups
    Route::livewire('admin/groups', 'pages::admin.groups.index')->name('admin.groups.index');
    Route::livewire('admin/groups/create', 'pages::admin.groups.group')->name('admin.groups.create');
    Route::livewire('admin/groups/{id}/edit', 'pages::admin.groups.group')->name('admin.groups.edit');
});

require __DIR__.'/settings.php';
