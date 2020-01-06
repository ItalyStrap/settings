<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class NullFilter
 * FILTER_UNSAFE_RAW
 * @package ItalyStrap\Settings\Filters
 */
class NullFilter implements FilterableInterface {

	const KEY = null;

	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {
		return $data;
	}
}
