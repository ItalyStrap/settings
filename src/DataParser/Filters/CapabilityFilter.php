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
	public function filter( $data, array $schema ) {
		// TODO: Implement filter() method.
	}
}
