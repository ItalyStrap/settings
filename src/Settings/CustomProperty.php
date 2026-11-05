<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * A value with its own JSON schema and sanitizing, like a list of records.
 */
final readonly class CustomProperty implements Property
{
    private PropertyName $name;

    /** @var callable(mixed, Sanitizing): mixed */
    private mixed $sanitize;

    /**
     * @param array<string, mixed> $jsonSchema
     * @param callable(mixed, Sanitizing): mixed $sanitize Returns null when the value is not stored.
     */
    public function __construct(
        string $name,
        private array $jsonSchema,
        callable $sanitize,
        private mixed $default = null,
    ) {
        $this->name = new PropertyName($name);
        $this->sanitize = $sanitize;
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return $this->jsonSchema;
    }

    public function default(): mixed
    {
        return $this->default;
    }

    public function sanitize(mixed $value, Sanitizing $sanitizing): mixed
    {
        return ($this->sanitize)($value, $sanitizing);
    }
}
