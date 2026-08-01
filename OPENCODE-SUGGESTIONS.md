# OPENCODE-SUGGESTIONS — fb-passwd

Status: 10 tests passing (41 assertions). 17 items open, 0 done → **ALL 17 COMPLETE** (12 fixed by batch application, 5 verified already satisfied).

## Bugs

1. ~~`src/Middleware/ForcePasswordChangeMiddleware.php:15` — `Auth::user()->value('force_change_password')` calls `Builder::value()` (Model has no `value()`; `__call` forwards to a fresh query) → runs `select force_change_password from users limit 1` with no `where`, so it reads the **first** user's flag, not the current user's. Masked by tests that only ever create one user. Fix: `Auth::user()->force_change_password`. Add a two-user test that fails before the fix.~~ **FIXED** — replaced with `Auth::user()?->getAttribute('force_change_password')` plus a `Filament::getCurrentPanel()` null-guard and `: Response` return type. Covered by `it('redirects the current user when forced even if another user is not')` in `tests/Tests/ChangePasswordTest.php`.

2. ~~`composer.json:66` — `extra.laravel.aliases.FbPasswd` points to `Mortezamasumi\FbPasswd\Facades\FbPasswd`, but `src/Facades/` does not exist. Resolving the alias would 500. Fix: create the Facade + main `FbPasswd` class, or drop the alias block.~~ **FIXED** — dropped the alias block. Verified no consumer uses the `FbPasswd` facade.

3. ~~`README.md:49-51` — Usage shows `new Mortezamasumi\FbPasswd()` / `echoPhrase()`, but `src/FbPasswd.php` does not exist. Dead API reference.~~ **FIXED** — README rewritten with the real plugin API.

## API cleanliness / typos

4. ~~`composer.json:3` — description is the boilerplate "This is my package fb-passwd". Rewrite: "Force users to change their password in Filament panels."~~ **FIXED**.

5. ~~`composer.json:4-8` — keywords missing `filament`; standard order starts `["mortezamasumi", "laravel", "filament", "fb-passwd", ...]`.~~ **FIXED**.

6. ~~`composer.json:48-52` — scripts missing `pint` (`vendor/bin/pint`) and `analyse` (`vendor/bin/phpstan analyse --no-progress`).~~ **FIXED**.

7. ~~`composer.json:56-58` — `config.allow-plugins` lists `phpstan/extension-installer`; standard allows only `pestphp/pest-plugin`.~~ **FIXED**.

8. ~~`composer.json:40` — autoload references `database/factories/`, but no `database/` dir exists. Dead entry.~~ **FIXED**.

## Meta / release-readiness

9. ~~Missing files required by standard: `pint.json`, `phpstan.neon.dist`, `config/fb-passwd.php`, `.github/CONTRIBUTING.md`, `.github/SECURITY.md`. Add from the fb-sms canonical versions.~~ **FIXED** — added `pint.json`, `phpstan.neon.dist`, `.github/CONTRIBUTING.md`, `.github/SECURITY.md`. `config/fb-passwd.php` deliberately NOT shipped: the package has no configurable options (plugin reads `force_change_password` straight off the user; README documents that no config file ships).

10. ~~`require-dev` missing `laravel/pint`, `phpstan/phpstan`, `larastan/larastan` (standard tooling; `vendor/bin/phpstan` currently only appears transitively).~~ **FIXED** — added `larastan/larastan ^3.10`, `laravel/pint ^1.30`, `phpstan/phpstan ^2.2`.

11. ~~`CHANGELOG.md:6` — placeholder date `202X-XX-XX`; needs a real entry.~~ **FIXED** — header rewritten; `5.0.0 - 2026-07-09` entry derived from `git log` of commit `0316945`.

12. ~~`README.md` — full boilerplate rewrite per standard: badges (Packagist + tests + downloads + license), tagline, Features, Installation, Configuration, Usage (real API), Testing, Contributing, Security, Support policy table, Changelog, License. Badge URLs currently point to non-existent workflows `run-tests.yml` / `fix-php-code-style-issues.yml` — actual workflow is `ci.yml`.~~ **FIXED** — rewritten per standard with correct `ci.yml` badge URLs.

13. ~~`README.md:23-44` — publishes `fb-passwd-migrations`, `fb-passwd-config`, `fb-passwd-views`, but the provider only registers translations (`hasTranslations()`). Either remove these sections or register the publish tags.~~ **FIXED** — sections removed; README documents that only `hasTranslations()` is registered and no publish tags ship.

## CI

14. ~~`.github/workflows/ci.yml` — missing the standard quality gates: `composer validate --strict`, `composer audit`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --no-progress`; matrix lacks `prefer-lowest`; PHP matrix `[8.3,8.4,8.5]` differs from the standard `[8.3]`. Align with fb-sms `ci.yml`.~~ **FIXED** — aligned with fb-sms: PHP matrix `[8.3]`, stability `[prefer-stable, prefer-lowest]`, added the four quality-gate steps, checkout bumped to `@v5` in both jobs.

## Security

15. ~~`composer audit` — 4 medium advisories (GHSA-h95v-h523-3mw8, GHSA-wm3w-8rrp-j577, GHSA-f283-ghqc-fg79, GHSA-94pj-82f3-465w) all in `guzzlehttp/guzzle 7.14.0` (< 7.15.1). Blocker per standard. Fix: `composer update guzzlehttp/guzzle`.~~ **FIXED** — `composer update guzzlehttp/guzzle guzzlehttp/psr7 -W`; `composer audit` now reports no advisories.

## Tests

16. ~~Coverage driver unavailable locally (`composer test-coverage` fails: "No code coverage driver"); CI runs with `coverage: none`. Enable coverage reporting or note the gap — standard requires ≥ 90% and CI must not regress.~~ **RESOLVED** — CI (`ci.yml`, aligned with fb-sms) does not run coverage; the `test-coverage` script remains for environments with a driver installed. Noted as an environment limitation, not a code gap.

17. ~~Missing failure-branch tests: wrong current password, mismatched confirmation, password identical to current, rate-limit after 2 attempts. Add per the existing `ChangePasswordTest.php` pattern.~~ **FIXED** — added four tests: `it('cannot change the password with the wrong current password')`, `it('cannot change the password when the confirmation does not match')`, `it('cannot change the password to the current password')`, `it('is rate limited after two failed attempts')`. Suite: 10 passed (41 assertions).
