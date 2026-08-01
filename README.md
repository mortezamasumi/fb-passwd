# FB Passwd — Force Password Change for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mortezamasumi/fb-passwd.svg?style=flat-square)](https://packagist.org/packages/mortezamasumi/fb-passwd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mortezamasumi/fb-passwd/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/mortezamasumi/fb-passwd/actions?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mortezamasumi/fb-passwd.svg?style=flat-square)](https://packagist.org/packages/mortezamasumi/fb-passwd)
[![License](https://img.shields.io/packagist/l/mortezamasumi/fb-passwd.svg?style=flat-square)](LICENSE.md)

A Filament panel plugin that forces users to change their password before they can use the panel. When a user's `force_change_password` flag is set, every panel request redirects to a dedicated change-password page until they update it.

---

## Features

- **Force password change** — middleware redirects forced users to the change-password page on every panel request
- **Locked-down page** — once forced, the topbar is hidden and the only action is updating the password or logging out
- **Rate limiting** — the save action is throttled to 2 attempts per request window
- **Password policy** — enforces Laravel's default password rules (production only) and requires confirmation
- **User menu entry** — a "Change password" action in the panel user menu, always available
- **Localized** — ships English and Persian translations

---

## Installation

```bash
composer require mortezamasumi/fb-passwd
```

Add the plugin to your Filament panel provider:

```php
use Mortezamasumi\FbPasswd\FbPasswdPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FbPasswdPlugin::make(),
        ]);
}
```

---

## Configuration

Add a `force_change_password` boolean column to your user table, and set it to `true` when you want a user to change their password on next login:

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('force_change_password')->default(false);
});
```

```php
$user->force_change_password = true;
$user->save();
```

The package ships no config file of its own; the flag is read straight from the authenticated user.

---

## Usage

Once the plugin is registered, everything is automatic:

- A forced user hitting any panel route is redirected to `change-password`.
- After a successful change, the flag is cleared, the session is regenerated, and the user is sent to the login page to sign back in.
- Non-forced users can still change their password anytime via the user menu.

### Translations

The package ships English (`resources/lang/en`) and Persian (`resources/lang/fa`) translations under the `fb-passwd` namespace, loaded automatically.

---

## Support policy

| PHP | Laravel |
| --- | --- |
| 8.3 | 12 |

---

## Testing

```bash
composer test
```

The test suite covers the redirect behaviour (including with multiple users), the user menu entry, and the password-change flow using an in-memory SQLite database.

---

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover a security vulnerability, please review our [security policy](.github/SECURITY.md) on how to report it.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

---

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
