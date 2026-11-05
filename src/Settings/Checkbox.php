<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * A checkbox, stored as '1' when it is on and not stored when it is off.
 */
final readonly class Checkbox implements Property
{
    private const ON = '1';

    private PropertyName $name;

    public function __construct(
        string $name,
        private bool $onByDefault = false,
    ) {
        $this->name = new PropertyName($name);
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return ['type' => 'string', 'enum' => [self::ON]];
    }

    public function default(): ?string
    {
        return $this->onByDefault ? self::ON : null;
    }

    public function sanitize(mixed $value, Sanitizing $sanitizing): ?string
    {
        return $value === self::ON ? self::ON : null;
    }
}
