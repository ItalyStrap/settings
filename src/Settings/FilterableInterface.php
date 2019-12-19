<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Interface FilterableInterface
 * @package ItalyStrap\Settings
 */
interface FilterableInterface
{
	/**
	 * @param array $schema
	 * @param array $data
	 * @return mixed
	 */
	public function filter( array $schema, array $data );
}
