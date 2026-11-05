<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Core;

use ItalyStrap\Settings\Sanitizing;

/**
 * The sanitizing functions of the core, one for each method, the integration suite tests it.
 */
final readonly class CoreSanitizing implements Sanitizing
{
    public function text(string $value): string
    {
        return \sanitize_text_field($value);
    }

    public function html(string $value): string
    {
        return \wp_kses_post($value);
    }

    public function key(string $value): string
    {
        return \sanitize_key($value);
    }
}
