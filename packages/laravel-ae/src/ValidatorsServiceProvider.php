<?php

declare(strict_types=1);

namespace Validators\LaravelAe;

use Illuminate\Support\ServiceProvider;
use Validators\Ae\EmiratesId as EmiratesIdValidator;
use Validators\Ae\UaeIban as UaeIbanValidator;
use Validators\Ae\UaeMobile as UaeMobileValidator;

class ValidatorsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'validators');

        $validator = $this->app['validator'];

        $validator->extend('emirates_id', fn (string $attribute, mixed $value): bool => EmiratesIdValidator::isValid($value));
        $validator->extend('uae_mobile', fn (string $attribute, mixed $value): bool => UaeMobileValidator::isValid($value));
        $validator->extend('uae_iban', fn (string $attribute, mixed $value): bool => UaeIbanValidator::isValid($value));

        $validator->replacer('emirates_id', fn (): string => __('validators::ae.emirates_id.invalid'));
        $validator->replacer('uae_mobile', fn (): string => __('validators::ae.mobile.invalid'));
        $validator->replacer('uae_iban', fn (): string => __('validators::ae.iban.invalid'));
    }
}
