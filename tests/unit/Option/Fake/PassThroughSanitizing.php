<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option\Fake;

use ItalyStrap\Settings\Sanitizing;

/**
 * Trims the texts like sanitize_text_field() and keeps the rest.
 */
final class PassThroughSanitizing implements Sanitizing
{
    public function text(string $value): string
    {
        return \trim($value);
    }

    public function html(string $value): string
    {
        return $value;
    }

    public function key(string $value): string
    {
        return $value;
    }
}
