<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option\Fake;

use ItalyStrap\Settings\Sanitizing;

/**
 * Marks the sanitized values, the tests see which function sanitized them.
 */
final class MarkingSanitizing implements Sanitizing
{
    public function text(string $value): string
    {
        return 'text(' . $value . ')';
    }

    public function html(string $value): string
    {
        return 'html(' . $value . ')';
    }

    public function key(string $value): string
    {
        return 'key(' . $value . ')';
    }
}
