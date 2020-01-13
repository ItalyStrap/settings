<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

class LazyParser extends Parser implements ParserInterface {

	/**
	 * @var callable
	 */
	private $filters;

	/**
	 * LazyParser constructor.
	 * @param callable $filters
	 */
	public function __construct( callable $filters ) {
		$this->filters = $filters;
	}

	/**
	 * @inheritDoc
	 */
	public function parseValues( array $data ): array {
		$filters = $this->filters;
		parent::withFilters( ...$filters() );
		return parent::parseValues( $data );
	}
}
