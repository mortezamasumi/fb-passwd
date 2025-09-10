<?php

namespace Mortezamasumi\FbPasswd;

use Livewire\Features\SupportTesting\Testable;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Mortezamasumi\FbPasswd\Testing\TestsFbPasswd;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FbPasswdServiceProvider extends PackageServiceProvider
{
    public static string $name = 'fb-passwd';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        config(['filament-shield.pages.exclude' => [
            ...config('filament-shield.pages.exclude') ?? [],
            ChangePassword::class,
        ]]);

        Testable::mixin(new TestsFbPasswd);
    }
}
