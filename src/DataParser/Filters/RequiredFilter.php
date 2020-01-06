<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

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
	public function filter( $data, array $schema ) {
		// TODO: Implement filter() method.
	}
}
