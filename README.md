# Validators

Country-specific PHP validators. Install only the packages you need.

## Packages

| Package | Description |
|---------|-------------|
| `validators/core` | Shared primitives (normalization, results, checksum helpers) |
| `validators/sa` | Saudi Arabia: national ID, mobile, IBAN |
| `validators/eg` | Egypt: national ID |
| `validators/ae` | UAE: Emirates ID, mobile, IBAN |
| `validators/laravel` | Laravel validation rules |
| `validators/codeigniter` | CodeIgniter 4 rule callables |

## Installation

Published on [Packagist](https://packagist.org) as separate packages (split from this monorepo):

```bash
# Saudi Arabia
composer require validators/core validators/sa

# Egypt
composer require validators/core validators/eg

# UAE
composer require validators/core validators/ae

# Framework adapters (optional)
composer require validators/laravel
```

## Plain PHP

```php
use Validators\Sa\SaudiNationalId;
use Validators\Sa\IdentityType;

$result = SaudiNationalId::check('1001244080');

$result->isValid();      // false
$result->errorKey();     // sa.national_id.invalid_checksum
$result->firstError();   // English fallback for logs or default UI

// Generate valid test data
SaudiNationalId::fake();
SaudiNationalId::fake(IdentityType::Citizen);
SaudiMobile::fake();
SaudiIban::fake();
EgyptianNationalId::fake();
EmiratesId::fake();
UaeMobile::fake();
UaeIban::fake();
```

Translate `errorKey()` in your application in any language you need.

`::fake()` generates values that pass format and checksum validation. Use it in tests, seeders, and local development — not as real identity data.

## Laravel

### String rules (quick setup)

Register the service provider, then use rule aliases:

```php
$request->validate([
    'saudi_id' => 'required|saudi_national_id',
    'egyptian_id' => 'required|egyptian_national_id',
    'emirates_id' => 'required|emirates_id',
    'uae_mobile' => 'required|uae_mobile',
    'uae_iban' => 'required|uae_iban',
]);
```

String rules return a generic translated message per validator (for example `validators::sa.national_id.invalid`).

### Rule objects (specific errors)

For field-specific error messages, use the rule classes:

```php
use Validators\Laravel\Rules\SaudiNationalId;

$request->validate([
    'saudi_id' => ['required', new SaudiNationalId()],
]);
```

Rule objects return the specific error (checksum, length, prefix, etc.) via `ValidationMessage::translate()`.

Publish or override translations under `lang/{locale}/validators.php`. English defaults ship with the package.

## CodeIgniter 4

```php
public array $ruleSets = [
    \Validators\CodeIgniter\SaRules::class,
    \Validators\CodeIgniter\EgRules::class,
    \Validators\CodeIgniter\AeRules::class,
];
```

Failed rules set `$error` to an `errorKey()` string (for example `sa.national_id.invalid_checksum`). Translate that key in your application.

## Disclaimer

These packages perform **format and checksum validation only**. They do not verify identity against government systems.

Emirates ID strict mode uses Luhn checksum. Some real IDs may fail strict mode; use `EmiratesId::check($value, strict: false)` for format-only validation.

## Packagist

Packagist.org does not install packages from monorepo subdirectories. Each package is mirrored to its own repository:

| Composer package | GitHub repo |
|------------------|-------------|
| `validators/core` | [validators-core](https://github.com/mohammedelkarsh/validators-core) |
| `validators/sa` | [validators-sa](https://github.com/mohammedelkarsh/validators-sa) |
| `validators/eg` | [validators-eg](https://github.com/mohammedelkarsh/validators-eg) |
| `validators/ae` | [validators-ae](https://github.com/mohammedelkarsh/validators-ae) |
| `validators/laravel` | [validators-laravel](https://github.com/mohammedelkarsh/validators-laravel) |
| `validators/codeigniter` | [validators-codeigniter](https://github.com/mohammedelkarsh/validators-codeigniter) |

Re-publish after a release:

```bash
powershell -File scripts/publish-splits.ps1 -Version v1.0.2
```

## Development

```bash
cd validators
composer install
composer test:all
```

Individual suites:

```bash
composer test              # PHPUnit
composer test:smoke        # Quick sanity check
composer test:audit        # Error keys, vectors, fake()
composer test:comprehensive
composer test:extra
composer test:exhaustive
```

## License

MIT
