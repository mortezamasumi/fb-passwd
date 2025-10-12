<?php

use Filament\Livewire\SimpleUserMenu;
use Filament\Pages\Dashboard;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Mortezamasumi\FbPasswd\Tests\Services\User;

it('can render dashboard without force to change password', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(Dashboard::getUrl())
        ->assertSuccessful();
});

it('can redirect to change password page if forced to change password', function () {
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->get(Dashboard::getUrl())
        ->assertRedirect('/change-password');
});

it('can see change password in user menu', function () {
    $this
        ->actingAs(User::factory()->create())
        ->Livewire(SimpleUserMenu::class)
        ->assertSee('Change password');
});

it('can change the password and set flag force_change_password to false', function () {
    $this
        ->actingAs($user = User::factory()->forceChangePassword()->create())
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
