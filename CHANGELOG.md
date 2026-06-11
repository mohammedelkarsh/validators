# Changelog

All notable changes to this project are documented here.

## [1.0.1] - 2026-06-11

### Changed

- Prepare packages for Packagist: stable `^1.0` dependencies instead of `@dev`
- Remove publishable `validators/monorepo` root package name
- Add `homepage` and `support` metadata to all packages

## [1.0.0] - 2026-06-11

### Added

- `validators/core` — normalization, validation results, Luhn, IBAN helpers
- `validators/sa` — Saudi national ID, mobile, IBAN
- `validators/eg` — Egyptian national ID
- `validators/ae` — Emirates ID, UAE mobile, IBAN
- `validators/laravel` — Laravel validation rules and translations
- `validators/codeigniter` — CodeIgniter 4 rule callables
- Error keys with English fallback
- `::fake()` generators for test data
