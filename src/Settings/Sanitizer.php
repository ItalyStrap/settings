<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Sanitizes a submitted option with the properties of its schema, the sanitize_callback of register_setting().
 */
final readonly class Sanitizer
{
    public function __construct(
        private Schema $schema,
        private Sanitizing $sanitizing,
    ) {
    }

    /**
     * Only the properties of the schema, in their order, without the values that are not stored.
     *
     * @return array<non-empty-string, mixed>
     */
    public function __invoke(mixed $input): array
    {
        $input = \is_array($input) ? $input : [];
        $sanitized = [];
        foreach ($this->schema->properties() as $name => $property) {
            $value = $property->sanitize($input[$name] ?? null, $this->sanitizing);
            if ($value === null) {
                continue;
            }

            $sanitized[$name] = $value;
        }

        return $sanitized;
    }
}
