<?php

declare(strict_types=1);

namespace Validators\Core;

final class ValidationResult
{
    /**
     * @param  list<string>  $errorKeys
     * @param  array<string, mixed>  $meta
     */
    private function __construct(
        private readonly bool $valid,
        private readonly string $normalized,
        private readonly array $errorKeys = [],
        private readonly array $meta = [],
    ) {}

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function valid(string $normalized, array $meta = []): self
    {
        return new self(true, $normalized, [], $meta);
    }

    /**
     * @param  string|list<string>  $errorKeys
     * @param  array<string, mixed>  $meta
     */
    public static function invalid(string $normalized, string|array $errorKeys, array $meta = []): self
    {
        $keys = is_array($errorKeys) ? $errorKeys : [$errorKeys];

        return new self(false, $normalized, $keys, $meta);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function normalized(): string
    {
        return $this->normalized;
    }

    public function errorKey(): ?string
    {
        return $this->errorKeys[0] ?? null;
    }

    /**
     * @return list<string>
     */
    public function errorKeys(): array
    {
        return $this->errorKeys;
    }

    /**
     * English fallback message for the first error.
     */
    public function firstError(): ?string
    {
        $key = $this->errorKey();

        return $key !== null ? ErrorMessages::message($key) : null;
    }

    /**
     * @return list<string> English fallback messages.
     */
    public function errors(): array
    {
        return array_map(
            static fn (string $key): string => ErrorMessages::message($key),
            $this->errorKeys
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function meta(): array
    {
        return $this->meta;
    }
}
