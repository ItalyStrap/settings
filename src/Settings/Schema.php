<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * The option of a plugin or a theme: its name, the version of its shape and its properties.
 */
final readonly class Schema
{
    /** @var non-empty-string */
    private string $name;

    /** @var array<non-empty-string, Property> */
    private array $properties;

    /**
     * @param int $version Raised with each migration step, see Migrator.
     */
    public function __construct(
        string $name,
        private int $version,
        Property ...$properties,
    ) {
        $this->name = (new PropertyName($name))->value();
        if ($version < 1) {
            throw new InvalidArgumentException(\sprintf('The version of "%s" must be at least 1', $this->name));
        }

        $this->properties = $this->keyed($properties);
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function version(): int
    {
        return $this->version;
    }

    /**
     * @return array<non-empty-string, Property>
     */
    public function properties(): array
    {
        return $this->properties;
    }

    /**
     * The values of a new installation.
     *
     * @return array<non-empty-string, mixed>
     */
    public function defaults(): array
    {
        return \array_filter(
            \array_map(static fn (Property $property): mixed => $property->default(), $this->properties),
            static fn (mixed $default): bool => $default !== null
        );
    }

    /**
     * The JSON schema of the option, read by the REST API.
     *
     * @return array{
     *     type: 'object',
     *     properties: array<non-empty-string, array<string, mixed>>,
     *     additionalProperties: false
     * }
     */
    public function jsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => \array_map(
                static fn (Property $property): array => $property->jsonSchema(),
                $this->properties
            ),
            'additionalProperties' => false,
        ];
    }

    /**
     * @param array<array-key, Property> $properties
     * @return array<non-empty-string, Property>
     */
    private function keyed(array $properties): array
    {
        $keyed = [];
        foreach ($properties as $property) {
            if (\array_key_exists($property->name(), $keyed)) {
                throw new InvalidArgumentException(\sprintf(
                    'The property "%s" of "%s" is already in the schema',
                    $property->name(),
                    $this->name
                ));
            }

            $keyed[$property->name()] = $property;
        }

        return $keyed;
    }
}
