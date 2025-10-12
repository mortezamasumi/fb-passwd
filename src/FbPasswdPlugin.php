<?php

namespace Mortezamasumi\FbPasswd;

use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Support\Icons\Heroicon;
use Filament\Panel;
use Mortezamasumi\FbPasswd\Middleware\ForcePasswordChangeMiddleware;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;

class FbPasswdPlugin implements Plugin
{
    public function getId(): string
    {
        return 'fb-passwd';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->authMiddleware([
                ForcePasswordChangeMiddleware::class,
            ])
            ->pages([ChangePassword::class])
            ->userMenuItems([
                Action::make('change-password')
                    ->label(fn (): string => __('fb-passwd::fb-passwd.user_menu'))
                    ->url(ChangePassword::getRoutePath($panel))
                    ->icon(Heroicon::OutlinedKey),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
