<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Interface FilterableInterface
 * @package ItalyStrap\Settings
 */
interface FilterableInterface {

	/**
	 * Array must return a valid key with a value to use to process data
	 * Example:
	 * [ 'sanitize' => 'strip_tags|trim' ]
	 * @return array
	 */
	public function getDefault();

	/**
	 * The filter accept a data value {int|string} and apply a filter method
	 *
	 * The return value could be the type of int or string
	 *
	 * @param int|string|array $data
	 * @param array<mixed> $schema
	 * @return mixed
	 */
	public function filter( $data, array $schema );
}
