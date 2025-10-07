<?php

use Filament\Facades\Filament;
use Filament\Livewire\SimpleUserMenu;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Mortezamasumi\FbPasswd\Tests\Services\User;

it('can be rendered dashboard by an authenticated user with no force to change password', function () {
    $this
        ->actingAs(User::factory()->create(['force_change_password' => false]))
        ->get(Filament::getUrl())
        ->assertSee('Dashboard')
        ->assertSuccessful();
});

it('can be redirect to change password page if forced to change password', function () {
    $this
        ->actingAs(User::factory()->create(['force_change_password' => true]))
        ->get(Filament::getUrl())
        ->assertRedirect('/change-password');
});

it('can see change password in user menu', function () {
    $this
        ->actingAs(User::factory()->create(['force_change_password' => false]))
        ->Livewire(SimpleUserMenu::class)
        ->assertSee('Change password');
});

it('can change the password and set flag force_change_password to false', function () {
    $this
        ->actingAs($user = User::factory()->create(['force_change_password' => true]))
        ->livewire(ChangePassword::class)
        ->fillForm([
            'current_password' => 'password',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect('/login');

    $user->refresh();

    expect($user->force_change_password)->toBe(0);
});
