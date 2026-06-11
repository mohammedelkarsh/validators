<?php

declare(strict_types=1);

namespace Validators\LaravelSa;

use Illuminate\Support\ServiceProvider;
use Validators\Sa\SaudiIban as SaudiIbanValidator;
use Validators\Sa\SaudiMobile as SaudiMobileValidator;
use Validators\Sa\SaudiNationalId as SaudiNationalIdValidator;

class ValidatorsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'validators');

        $validator = $this->app['validator'];

        $validator->extend('saudi_national_id', fn (string $attribute, mixed $value): bool => SaudiNationalIdValidator::isValid($value));
        $validator->extend('saudi_mobile', fn (string $attribute, mixed $value): bool => SaudiMobileValidator::isValid($value));
        $validator->extend('saudi_iban', fn (string $attribute, mixed $value): bool => SaudiIbanValidator::isValid($value));

        $validator->replacer('saudi_national_id', fn (): string => __('validators::sa.national_id.invalid'));
        $validator->replacer('saudi_mobile', fn (): string => __('validators::sa.mobile.invalid'));
        $validator->replacer('saudi_iban', fn (): string => __('validators::sa.iban.invalid'));
    }
}
