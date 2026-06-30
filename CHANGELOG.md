# Changelog

All notable changes to this project are documented here.

## [1.0.3] - 2026-07-03

### Added

- Arabic Laravel validation messages (`lang/ar`) for `laravel-sa`, `laravel-eg`, and `laravel-ae`
- GitHub Actions CI workflow (PHP 8.2, 8.3, 8.4)
- Packagist badges on package README files
- Release and topic automation scripts (`create-github-releases.ps1`, `set-github-topics.ps1`)

## [1.0.2] - 2026-06-11

### Changed

- **Breaking:** Replace `validators/laravel` with `validators/laravel-sa`, `validators/laravel-eg`, `validators/laravel-ae`
- **Breaking:** Replace `validators/codeigniter` with country-specific CodeIgniter packages
- Saudi Laravel apps now install only SA dependencies via `composer require validators/laravel-sa`

### Removed

- `validators/laravel`
- `validators/codeigniter`

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
