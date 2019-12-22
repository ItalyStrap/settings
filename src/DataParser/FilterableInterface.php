<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Interface FilterableInterface
 * @package ItalyStrap\Settings
 */
interface FilterableInterface {

	/**
	 * The filter accept a data value {int|string} and apply a filter method
	 *
	 * The return value could be the type of int or string
	 *
	 * @param int|string $data
	 * @param array $schema
	 * @return mixed
	 */
	public function filter( $data, array $schema );
}
