<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Core;

use ItalyStrap\Settings\OptionStorage;

/**
 * The options of the core, one function for each method, the integration suite tests it.
 */
final readonly class CoreOptionStorage implements OptionStorage
{
    public function get(string $name): mixed
    {
        return \get_option($name, null);
    }

    public function add(string $name, mixed $value): void
    {
        \add_option($name, $value);
    }

    public function update(string $name, mixed $value): void
    {
        \update_option($name, $value);
    }
}
