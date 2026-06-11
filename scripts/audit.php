<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;
use Validators\Core\ErrorMessages;
use Validators\Eg\EgyptianNationalId;
use Validators\Sa\IdentityType;
use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

$failures = [];

function check(bool $condition, string $label): void
{
    global $failures;

    if (! $condition) {
        $failures[] = $label;
    }
}

$usedKeys = [];

$scan = static function (string $directory) use (&$usedKeys): void {
    foreach (glob($directory.'/*.php') ?: [] as $file) {
        $contents = file_get_contents($file) ?: '';

        if (preg_match_all("/ValidationResult::invalid\\([^,]+,\\s*'([^']+)'/", $contents, $matches)) {
            foreach ($matches[1] as $key) {
                $usedKeys[$key] = true;
            }
        }
    }
};

$scan(dirname(__DIR__).'/packages/sa/src');
$scan(dirname(__DIR__).'/packages/eg/src');
$scan(dirname(__DIR__).'/packages/ae/src');

foreach (array_keys($usedKeys) as $key) {
    check(ErrorMessages::has($key), "missing error message for key: {$key}");
}

$fakeValidators = [
    SaudiNationalId::class,
    SaudiMobile::class,
    SaudiIban::class,
    EgyptianNationalId::class,
    EmiratesId::class,
    UaeMobile::class,
    UaeIban::class,
];

foreach ($fakeValidators as $class) {
    check(method_exists($class, 'fake'), "missing ::fake() on {$class}");
}

for ($index = 0; $index < 10; $index++) {
    check(SaudiNationalId::isValid(SaudiNationalId::fake()), 'SaudiNationalId::fake() invalid');
    check(SaudiNationalId::isValid(SaudiNationalId::fake(IdentityType::Citizen)), 'citizen fake invalid');
    check(SaudiMobile::isValid(SaudiMobile::fake()), 'SaudiMobile::fake() invalid');
    check(SaudiIban::isValid(SaudiIban::fake()), 'SaudiIban::fake() invalid');
    check(EgyptianNationalId::isValid(EgyptianNationalId::fake()), 'EgyptianNationalId::fake() invalid');
    check(EmiratesId::isValid(EmiratesId::fake()), 'EmiratesId::fake() invalid');
    check(UaeMobile::isValid(UaeMobile::fake()), 'UaeMobile::fake() invalid');
    check(UaeIban::isValid(UaeIban::fake()), 'UaeIban::fake() invalid');
}

$vectorDir = dirname(__DIR__).'/spec/test-vectors';
$vectorFiles = glob($vectorDir.'/*.json') ?: [];

check(count($vectorFiles) === 7, 'expected 7 spec test-vector files');

foreach ($vectorFiles as $file) {
    $data = json_decode(file_get_contents($file) ?: '[]', true);
    check(is_array($data), 'invalid json: '.basename($file));
}

if ($failures === []) {
    fwrite(STDOUT, "AUDIT OK: ".count($usedKeys)." error keys, ".count($vectorFiles)." vector files, all fake() checks passed\n");
    exit(0);
}

fwrite(STDERR, "AUDIT FAILED:\n");

foreach ($failures as $failure) {
    fwrite(STDERR, " - {$failure}\n");
}

exit(1);
