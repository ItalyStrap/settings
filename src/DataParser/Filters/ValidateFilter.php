<?php

declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\Exception\InvalidValue;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class ValidateFilter
 * @package ItalyStrap\Settings\Filters
 */
class ValidateFilter implements FilterableInterface
{
    use DefaultSchema;

    public const KEY = 'validate';

    public function __construct(private Validation $validation)
    {
    }

    /**
     * @inheritDoc
     */
    public function filter(string $key, $value, array $schema)
    {

        if (! $schema[ self::KEY ]) {
            return $value;
        }

        $this->validation->addRules($schema[ self::KEY ]);

        if (false === $this->validation->validate(\strval($value))) {
            throw new InvalidValue(\sprintf(
                'The "%s" is not valid.',
                $key
            ));
        }

        return $value;
    }
}
