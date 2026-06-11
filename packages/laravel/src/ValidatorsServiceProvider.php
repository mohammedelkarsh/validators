<?php

declare(strict_types=1);

namespace Validators\Laravel;

use Illuminate\Support\ServiceProvider;
use Validators\Ae\EmiratesId as EmiratesIdValidator;
use Validators\Ae\UaeIban as UaeIbanValidator;
use Validators\Ae\UaeMobile as UaeMobileValidator;
use Validators\Eg\EgyptianNationalId as EgyptianNationalIdValidator;
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

        $validator->extend('egyptian_national_id', fn (string $attribute, mixed $value): bool => EgyptianNationalIdValidator::isValid($value));
        $validator->extend('emirates_id', fn (string $attribute, mixed $value): bool => EmiratesIdValidator::isValid($value));
        $validator->extend('uae_mobile', fn (string $attribute, mixed $value): bool => UaeMobileValidator::isValid($value));
        $validator->extend('uae_iban', fn (string $attribute, mixed $value): bool => UaeIbanValidator::isValid($value));

        $validator->replacer('saudi_national_id', fn (): string => __('validators::sa.national_id.invalid'));
        $validator->replacer('saudi_mobile', fn (): string => __('validators::sa.mobile.invalid'));
        $validator->replacer('saudi_iban', fn (): string => __('validators::sa.iban.invalid'));
        $validator->replacer('egyptian_national_id', fn (): string => __('validators::eg.national_id.invalid'));
        $validator->replacer('emirates_id', fn (): string => __('validators::ae.emirates_id.invalid'));
        $validator->replacer('uae_mobile', fn (): string => __('validators::ae.mobile.invalid'));
        $validator->replacer('uae_iban', fn (): string => __('validators::ae.iban.invalid'));
    }
}
