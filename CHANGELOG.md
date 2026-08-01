# Changelog

All notable changes to `fb-passwd` will be documented in this file.

## 5.1.0 - 2026-08-01

- fix: `ForcePasswordChangeMiddleware` now reads the current user's `force_change_password` flag instead of the first user's row
- fix: user-menu "Change password" item renders correctly with Filament v5.7.x
- fix: redirect target after a successful password change falls back to `/login` when no login URL is configured
- tests: add failure-branch coverage (wrong current password, mismatched confirmation, identical password, rate limiting) and a two-user regression test
- tooling: add Pint + PHPStan (level 8) configs and quality gates (`composer validate --strict`, `composer audit`, Pint, PHPStan) to CI; add `prefer-lowest` matrix run
- docs: add `CONTRIBUTING.md` and `SECURITY.md`; rewrite README and CHANGELOG to the workspace standard
- deps: resolve Guzzle security advisories; drop dead facade alias and autoload entry

## 5.0.0 - 2026-07-09

- upgrade to Filament 5
