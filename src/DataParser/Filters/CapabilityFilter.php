<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class CapabilityFilter
 * @package ItalyStrap\DataParser\Filters
 */
class CapabilityFilter implements FilterableInterface {

	const KEY = 'capability';

	use DefaultSchema;

	/**
	 * @inheritDoc
	 */
	public function filter( string $key, $value, array $schema ) {

		if ( \current_user_can( $schema[ self::KEY ] ) ) {
			return $value;
		}

		return $value;
	}
}
