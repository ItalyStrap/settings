<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * How the values are sanitized, the core in Core\CoreSanitizing.
 */
interface Sanitizing
{
    /**
     * A text on one line, without tags.
     */
    public function text(string $value): string;

    /**
     * A text with the tags allowed in a post.
     */
    public function html(string $value): string;

    /**
     * A key with lowercase letters, numbers, dashes and underscores.
     */
    public function key(string $value): string;
}
