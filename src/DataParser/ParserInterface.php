<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Interface DataParserInterface
 * @package ItalyStrap\Settings
 */
interface ParserInterface
{
	/**
	 * @param array $schema
	 * @return Parser
	 */
	public function withSchema( array $schema );

	/**
	 * @param FilterableInterface ...$filters
	 * @return Parser
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
