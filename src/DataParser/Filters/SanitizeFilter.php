<?php

declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class SanitizeFilter
 * @package ItalyStrap\Settings\Filters
 */
class SanitizeFilter implements FilterableInterface
{
    use DefaultSchema;

    public const KEY = 'sanitize';

    public function __construct(private Sanitization $sanitization)
    {
    }

    /**
     * @inheritDoc
     */
    public function filter(string $key, $value, array $schema)
    {

        $this->sanitization->addRules($schema[ self::KEY ]);

        return $this->sanitization->sanitize($value);
    }
}
