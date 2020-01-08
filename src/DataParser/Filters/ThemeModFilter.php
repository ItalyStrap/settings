<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class ThemeModFilter
 * @package ItalyStrap\DataParser\Filters
 */
class ThemeModFilter implements FilterableInterface {

	const KEY = 'option-type';

	use DefaultSchema;

	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {

		if ( isset( $schema['id'] ) && 'theme_mod' === $schema['option_type'] ) {
			\set_theme_mod( $schema['id'], $data );
		}

		return $data;
	}
}
