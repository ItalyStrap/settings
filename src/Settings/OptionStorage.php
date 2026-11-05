<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Where the options are stored, the core in Core\CoreOptionStorage.
 */
interface OptionStorage
{
    /**
     * Null when the option does not exist.
     */
    public function get(string $name): mixed;

    public function add(string $name, mixed $value): void;

    public function update(string $name, mixed $value): void;
}
