<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * The stored values of an option, read with their type.
 */
final readonly class Values
{
    /**
     * @param array<array-key, mixed> $values
     */
    public function __construct(
        private array $values,
    ) {
    }

    /**
     * A checkbox is on when its value is '1'.
     */
    public function isChecked(string $name): bool
    {
        return ($this->values[$name] ?? null) === '1';
    }

    /**
     * A text, the default when it is missing, empty or not a string.
     */
    public function text(string $name, string $default = ''): string
    {
        $value = $this->values[$name] ?? null;

        return \is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * The non empty texts of a list.
     *
     * @return list<string>
     */
    public function texts(string $name): array
    {
        return \array_values(\array_filter(
            $this->list($name),
            static fn (mixed $value): bool => \is_string($value) && \trim($value) !== ''
        ));
    }

    /**
     * The arrays of a list, like the records of a repeater.
     *
     * @return list<array<array-key, mixed>>
     */
    public function records(string $name): array
    {
        return \array_values(\array_filter($this->list($name), \is_array(...)));
    }

    /**
     * The values of a map, like the texts of the categories keyed by category id.
     *
     * @return array<array-key, mixed>
     */
    public function map(string $name): array
    {
        return $this->list($name);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function list(string $name): array
    {
        $value = $this->values[$name] ?? [];

        return \is_array($value) ? $value : [];
    }
}
