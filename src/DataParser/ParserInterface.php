<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Interface DataParserInterface
 * @package ItalyStrap\Settings
 */
interface ParserInterface {

	/**
	 * @param array<mixed> $schema
	 * @return Parser
	 */
	public function withSchema( array $schema );

	/**
	 * @return array
	 */
	public function getSchema(): array;

	/**
	 * @param FilterableInterface ...$filters
	 * @return Parser
	 */
	public function withFilters( FilterableInterface ...$filters );

	/**
	 * Sanitize the input data
	 *
	 * @param array<int|string> $data The input array.
	 * @return array<int|string>      Return the array sanitized
	 */
	public function parseValues( array $data ): array;
}
