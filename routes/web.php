<?php

use App\Http\Controllers\Collections\DeleteCollectionController;
use App\Livewire\Collections\CreateCollection;
use App\Livewire\Collections\ListCollections;
use App\Livewire\Collections\EditCollection;
use App\Livewire\Collections\ShowCollection;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/collections', ListCollections::class)
        ->name('collections.index');

    Route::get('/collections/create', CreateCollection::class)
        ->name('collections.create');

    Route::get('/collections/{collection}', ShowCollection::class)
        ->name('collections.show');

    Route::get('/collections/{collection}/edit', EditCollection::class)
        ->name('collections.edit');

    Route::delete('/collections/{collection}', DeleteCollectionController::class)
        ->name('collections.delete');

});
