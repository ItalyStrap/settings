<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * A submitted value as a string, the empty string when it is not a scalar.
 */
final readonly class Scalar
{
    public function __construct(
        private mixed $value,
    ) {
    }

    public function toString(): string
    {
        return \is_scalar($this->value) ? (string) $this->value : '';
    }
}
