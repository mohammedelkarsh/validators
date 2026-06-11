<?php

declare(strict_types=1);

namespace Validators\LaravelEg;

use Illuminate\Support\ServiceProvider;
use Validators\Eg\EgyptianNationalId as EgyptianNationalIdValidator;

class ValidatorsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'validators');

        $validator = $this->app['validator'];

        $validator->extend('egyptian_national_id', fn (string $attribute, mixed $value): bool => EgyptianNationalIdValidator::isValid($value));
        $validator->replacer('egyptian_national_id', fn (): string => __('validators::eg.national_id.invalid'));
    }
}
