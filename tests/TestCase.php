<?php

namespace Mortezamasumi\FbPasswd\Tests;

use Mortezamasumi\FbPasswd\FbPasswdPlugin;
use Mortezamasumi\FbPasswd\FbPasswdServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function defineEnvironment($app)
    {
        \Illuminate\Support\Facades\Schema::create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('force_change_password')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        \Filament\Facades\Filament::registerPanel(
            \Filament\Panel::make()
                ->id('admin')
                ->path('/')
                ->login()
                ->default()
                ->pages([
                    \Filament\Pages\Dashboard::class,
                ])
                ->middleware([
                    \Illuminate\Cookie\Middleware\EncryptCookies::class,
                    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                    \Illuminate\Session\Middleware\StartSession::class,
                    \Filament\Http\Middleware\AuthenticateSession::class,
                    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                    \Illuminate\Routing\Middleware\SubstituteBindings::class,
                    \Filament\Http\Middleware\DisableBladeIconComponents::class,
                    \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
                ])
                ->authMiddleware([
                    \Filament\Http\Middleware\Authenticate::class,
                ])
                ->plugins([
                    FbPasswdPlugin::make(),
                ])
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            \BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
            \BladeUI\Icons\BladeIconsServiceProvider::class,
            \Filament\FilamentServiceProvider::class,
            \Filament\Actions\ActionsServiceProvider::class,
            \Filament\Forms\FormsServiceProvider::class,
            \Filament\Infolists\InfolistsServiceProvider::class,
            \Filament\Notifications\NotificationsServiceProvider::class,
            \Filament\Schemas\SchemasServiceProvider::class,
            \Filament\Support\SupportServiceProvider::class,
            \Filament\Tables\TablesServiceProvider::class,
            \Filament\Widgets\WidgetsServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,
            \RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class,
            \Orchestra\Workbench\WorkbenchServiceProvider::class,
            FbPasswdServiceProvider::class,
        ];
    }
}
