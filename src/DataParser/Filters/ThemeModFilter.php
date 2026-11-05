<?php

declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class ThemeModFilter
 * @package ItalyStrap\DataParser\Filters
 */
class ThemeModFilter implements FilterableInterface
{
    use DefaultSchema;

    public const KEY = 'option-type';

    /**
     * @inheritDoc
     */
    public function filter(string $key, $value, array $schema)
    {

        if ('theme_mod' === $schema[ self::KEY ]) {
            \set_theme_mod($key, $value);
        }

        return $value;
    }
}
