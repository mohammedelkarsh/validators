<?php

declare(strict_types=1);

use Validators\Core\ErrorMessages;

return array_filter(
    ErrorMessages::all(),
    static fn (string $key): bool => str_starts_with($key, 'ae.'),
    ARRAY_FILTER_USE_KEY
);
