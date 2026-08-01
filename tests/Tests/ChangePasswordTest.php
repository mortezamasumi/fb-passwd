<?php

use Filament\Facades\Filament;
use Filament\Livewire\SimpleUserMenu;
use Filament\Pages\Dashboard;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Mortezamasumi\FbPasswd\Tests\Services\User;

it('can render dashboard without force to change password', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->create())
        ->get(Dashboard::getUrl())
        ->assertSuccessful();
});

it('can redirect to change password page if forced to change password', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->get(Dashboard::getUrl())
        ->assertRedirect('/change-password');
});

it('redirects the current user when forced even if another user is not', function () {
    /** @var Pest $this */
    User::factory()->create();

    $forced = User::factory()->forceChangePassword()->create();

    $this
        ->actingAs($forced)
        ->get(Dashboard::getUrl())
        ->assertRedirect('/change-password');
});

it('can see change password in user menu', function () {
    /** @var Pest $this */
    Filament::setCurrentPanel(Filament::getDefaultPanel());

    $this
        ->actingAs(User::factory()->create())
        ->Livewire(SimpleUserMenu::class)
        ->assertSee('Change password');
});

it('can change the password and set flag force_change_password to false', function () {
    /** @var Pest $this */
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

it('cannot change the password with the wrong current password', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->livewire(ChangePassword::class)
        ->fillForm([
            'current_password' => 'wrong-password',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ])
        ->call('save')
        ->assertHasFormErrors(['current_password']);
});

it('cannot change the password when the confirmation does not match', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->livewire(ChangePassword::class)
        ->fillForm([
            'current_password' => 'password',
            'password' => '123456789',
            'password_confirmation' => '987654321',
        ])
        ->call('save')
        ->assertHasFormErrors(['password']);
});

it('cannot change the password to the current password', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->livewire(ChangePassword::class)
        ->fillForm([
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->call('save')
        ->assertHasFormErrors(['password']);
});

it('is rate limited after two failed attempts', function () {
    /** @var Pest $this */
    $this
        ->actingAs(User::factory()->forceChangePassword()->create())
        ->livewire(ChangePassword::class)
        ->fillForm([
            'current_password' => 'wrong-password',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ])
        ->call('save')
        ->assertHasFormErrors(['current_password'])
        ->call('save')
        ->assertHasFormErrors(['current_password'])
        ->call('save')
        ->assertNotified();
});
