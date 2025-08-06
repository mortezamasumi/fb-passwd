<?php

namespace Mortezamasumi\FbPasswd;

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
}
