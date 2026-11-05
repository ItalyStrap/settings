<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * One value of a list, like the options of a select, the default for any other value.
 */
final readonly class Choice implements Property
{
    private PropertyName $name;

    /**
     * @param list<string> $values
     * @param string|null $default One of the values, null when the value is not stored.
     */
    public function __construct(
        string $name,
        private array $values,
        private ?string $default = null,
    ) {
        $this->name = new PropertyName($name);
        if ($values === []) {
            throw new InvalidArgumentException(\sprintf('The choice "%s" must have values', $this->name()));
        }

        if ($default !== null && !\in_array($default, $values, true)) {
            throw new InvalidArgumentException(\sprintf(
                'The default of "%s" must be one of its values',
                $this->name()
            ));
        }
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return ['type' => 'string', 'enum' => $this->values];
    }

    public function default(): ?string
    {
        return $this->default;
    }

    public function sanitize(mixed $value, Sanitizing $sanitizing): ?string
    {
        return \is_string($value) && \in_array($value, $this->values, true) ? $value : $this->default;
    }
}
