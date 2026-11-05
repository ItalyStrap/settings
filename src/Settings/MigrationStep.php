<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Brings the option from the previous version of the schema to this one.
 *
 * The step must not change an option already in the shape of its version, like the defaults of a new
 * installation: another request can store them while this one reads the previous version.
 */
interface MigrationStep
{
    /**
     * The version of the schema after this step, at least 2.
     */
    public function version(): int;

    /**
     * @param array<array-key, mixed> $option
     * @return array<array-key, mixed>
     */
    public function __invoke(array $option): array;
}
