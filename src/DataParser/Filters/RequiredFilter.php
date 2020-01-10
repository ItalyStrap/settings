<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\Exception\ValueRequired;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class RequiredFilter
 * @package ItalyStrap\DataParser\Filters
 */
class RequiredFilter implements FilterableInterface {

	const KEY = 'required';

	use DefaultSchema;

	/**
	 * @inheritDoc
	 */
	public function filter( string $key, $value, array $schema ) {

		if ( $schema[ self::KEY ] && empty( $value ) ) {
			throw new ValueRequired( \sprintf(
				'The value with key: %s is required.',
				$key
			) );
		}

		return $value;
	}
}
