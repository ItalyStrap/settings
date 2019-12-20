<?php
declare(strict_types=1);

namespace ItalyStrap\Settings\Filters;

use ItalyStrap\Settings\FilterableInterface;

/**
 * Class NullFilter
 * @package ItalyStrap\Settings\Filters
 */
class NullFilter implements FilterableInterface
{

	/**
	 * @inheritDoc
	 */
	public function filter( array $rules, array $data ) {
		return $data[ $rules['id'] ];
	}
}
