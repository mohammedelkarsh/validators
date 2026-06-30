# validators/laravel-sa

[![Latest Version](https://img.shields.io/packagist/v/validators/laravel-sa)](https://packagist.org/packages/validators/laravel-sa)
[![Total Downloads](https://img.shields.io/packagist/dt/validators/laravel-sa)](https://packagist.org/packages/validators/laravel-sa)
[![License](https://img.shields.io/packagist/l/validators/laravel-sa)](https://packagist.org/packages/validators/laravel-sa)
[![PHP Version](https://img.shields.io/packagist/php-v/validators/laravel-sa)](https://packagist.org/packages/validators/laravel-sa)

Laravel validation rules for Saudi Arabia. Includes Arabic translations (`lang/ar`).

```bash
composer require validators/laravel-sa
```

```php
'national_id' => 'required|saudi_national_id',
'mobile' => 'required|saudi_mobile',
'iban' => 'required|saudi_iban',
```

Monorepo: https://github.com/mohammedelkarsh/validators
