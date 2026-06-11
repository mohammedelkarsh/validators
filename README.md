# Validators

Country-specific PHP validators. Install only the packages you need.

## Packages

| Package | Description |
|---------|-------------|
| `validators/core` | Shared primitives |
| `validators/sa` | Saudi Arabia: national ID, mobile, IBAN |
| `validators/eg` | Egypt: national ID |
| `validators/ae` | UAE: Emirates ID, mobile, IBAN |
| `validators/laravel-sa` | Laravel rules for Saudi Arabia |
| `validators/laravel-eg` | Laravel rules for Egypt |
| `validators/laravel-ae` | Laravel rules for UAE |
| `validators/codeigniter-sa` | CodeIgniter rules for Saudi Arabia |
| `validators/codeigniter-eg` | CodeIgniter rules for Egypt |
| `validators/codeigniter-ae` | CodeIgniter rules for UAE |

## Installation

### Saudi Laravel project

```bash
composer require validators/laravel-sa
```

Installs only `validators/laravel-sa`, `validators/sa`, and `validators/core`.

```php
$request->validate([
    'national_id' => 'required|saudi_national_id',
    'mobile' => 'required|saudi_mobile',
    'iban' => 'required|saudi_iban',
]);
```

### Plain PHP (Saudi only)

```bash
composer require validators/sa
```

### Egypt / UAE

```bash
composer require validators/laravel-eg
composer require validators/laravel-ae
```

## Plain PHP API

```php
use Validators\Sa\SaudiNationalId;

$result = SaudiNationalId::check('1001244080');
$result->errorKey();
$result->firstError();

SaudiNationalId::fake();
```

## Laravel rule objects

```php
use Validators\LaravelSa\Rules\SaudiNationalId;

'saudi_id' => ['required', new SaudiNationalId()],
```

## CodeIgniter 4

```php
// Saudi project only
public array $ruleSets = [
    \Validators\CodeIgniterSa\SaRules::class,
];
```

## Packagist

Submit each split repository (not the monorepo):

| Composer package | GitHub repo |
|------------------|-------------|
| `validators/core` | [validators-core](https://github.com/mohammedelkarsh/validators-core) |
| `validators/sa` | [validators-sa](https://github.com/mohammedelkarsh/validators-sa) |
| `validators/eg` | [validators-eg](https://github.com/mohammedelkarsh/validators-eg) |
| `validators/ae` | [validators-ae](https://github.com/mohammedelkarsh/validators-ae) |
| `validators/laravel-sa` | [validators-laravel-sa](https://github.com/mohammedelkarsh/validators-laravel-sa) |
| `validators/laravel-eg` | [validators-laravel-eg](https://github.com/mohammedelkarsh/validators-laravel-eg) |
| `validators/laravel-ae` | [validators-laravel-ae](https://github.com/mohammedelkarsh/validators-laravel-ae) |
| `validators/codeigniter-sa` | [validators-codeigniter-sa](https://github.com/mohammedelkarsh/validators-codeigniter-sa) |
| `validators/codeigniter-eg` | [validators-codeigniter-eg](https://github.com/mohammedelkarsh/validators-codeigniter-eg) |
| `validators/codeigniter-ae` | [validators-codeigniter-ae](https://github.com/mohammedelkarsh/validators-codeigniter-ae) |

**Removed:** `validators/laravel` and `validators/codeigniter` (installed all countries). Delete them from Packagist if you added them.

Re-publish after a release:

```powershell
powershell -File scripts/publish-splits.ps1 -Version v1.0.2
```

## Development

```bash
cd validators
composer install
composer test:all
```

## Disclaimer

Format and checksum validation only — not government verification.

## License

MIT
