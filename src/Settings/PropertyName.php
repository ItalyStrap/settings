<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * The key of a value in the option.
 */
final readonly class PropertyName
{
    /** @var non-empty-string */
    private string $value;

    public function __construct(string $value)
    {
        $value = \trim($value);
        if ($value === '') {
            throw new InvalidArgumentException('The name of a property must not be empty');
        }

        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
