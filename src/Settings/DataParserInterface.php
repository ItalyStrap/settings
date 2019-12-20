<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Interface DataParserInterface
 * @package ItalyStrap\Settings
 */
interface DataParserInterface
{
	/**
	 * @param array $schema
	 * @return DataParser
	 */
	public function withSchema( array $schema );

	/**
	 * @param FilterableInterface ...$filters
	 * @return DataParser
	 */
	public function withFilters( FilterableInterface ...$filters );

	/**
	 * Sanitize the input data
	 *
	 * @param array $data The input array.
	 * @return array      Return the array sanitized
	 */
	public function parse( array $data ): array;
}
