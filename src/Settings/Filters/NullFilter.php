<?php
declare(strict_types=1);

namespace ItalyStrap\Settings\Filters;

use ItalyStrap\Settings\FilterableInterface;

/**
 * Class NullFilter
 * FILTER_UNSAFE_RAW
 * @package ItalyStrap\Settings\Filters
 */
class NullFilter implements FilterableInterface
{
	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {
		return $data;
	}
}
