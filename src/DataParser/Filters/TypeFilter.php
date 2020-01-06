<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class TypeFilter
 * @package ItalyStrap\DataParser\Filters
 */
class TypeFilter implements FilterableInterface
{
	const KEY = 'data-type';

	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {
		// TODO: Implement filter() method.
	}
}
