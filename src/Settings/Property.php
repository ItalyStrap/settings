<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * A value of the option: its JSON schema, its default and its sanitizing.
 */
interface Property
{
    /**
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * The JSON schema of the value, read by the REST API.
     *
     * @return array<string, mixed>
     */
    public function jsonSchema(): array;

    /**
     * The value of a new installation, null when the value is not stored.
     */
    public function default(): mixed;

    /**
     * The value to store, null when it is not stored.
     */
    public function sanitize(mixed $value, Sanitizing $sanitizing): mixed;
}
