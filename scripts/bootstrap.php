<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$illuminateAutoload = dirname($root).'/laravel-tenant-kit/vendor/autoload.php';

if (is_file($illuminateAutoload)) {
    require $illuminateAutoload;
}

spl_autoload_register(static function (string $class) use ($root): void {
    $map = [
        'Validators\\Core\\' => $root.'/packages/core/src/',
        'Validators\\Sa\\' => $root.'/packages/sa/src/',
        'Validators\\Eg\\' => $root.'/packages/eg/src/',
        'Validators\\Ae\\' => $root.'/packages/ae/src/',
        'Validators\\LaravelSa\\' => $root.'/packages/laravel-sa/src/',
        'Validators\\LaravelEg\\' => $root.'/packages/laravel-eg/src/',
        'Validators\\LaravelAe\\' => $root.'/packages/laravel-ae/src/',
        'Validators\\CodeIgniterSa\\' => $root.'/packages/codeigniter-sa/src/',
        'Validators\\CodeIgniterEg\\' => $root.'/packages/codeigniter-eg/src/',
        'Validators\\CodeIgniterAe\\' => $root.'/packages/codeigniter-ae/src/',
    ];

    foreach ($map as $prefix => $base) {
        if (! str_starts_with($class, $prefix)) {
            continue;
        }

        $relative = str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
        $file = $base.$relative;

        if (is_file($file)) {
            require $file;
        }
    }
});
