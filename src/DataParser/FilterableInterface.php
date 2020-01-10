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
	 * @param string $key
	 * @param mixed $value
	 * @param array<mixed> $schema
	 * @return mixed
	 */
	public function filter( string $key, $value, array $schema );
}
