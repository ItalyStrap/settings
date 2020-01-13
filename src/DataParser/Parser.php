<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Class DataParser
 * @package ItalyStrap\Settings
 */
class Parser implements ParserInterface {


	/**
	 * @var array
	 */
	private $schema = [];

	/**
	 * @var array
	 */
	private $filters = [];

	/**
	 * @inheritDoc
	 */
	public function withSchema( array $schema ): Parser {
		$this->schema = (array) \array_replace_recursive( $this->schema, $schema );
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getSchema(): array {
		return $this->schema;
	}

	/**
	 * @inheritDoc
	 */
	public function withFilters( FilterableInterface ...$filters ): Parser {
		$this->filters = \array_merge( $this->filters, $filters );
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function parseValues( array $data ): array {

		$this->assertHasFilters();

		foreach ( $this->schema as $key => $schema ) {
			$data = $this->assertDataValueIsSet( $key, $data );
			$data[ $key ] = $this->parseValue( $key, $data[ $key ], $schema );
		}

		return $data;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param array $schema
	 * @return mixed
	 */
	private function parseValue( string $key, $value, array $schema ) {

		/* @var $filter FilterableInterface */
		foreach ( $this->filters as $filter ) {
			$schema = \array_replace_recursive( $filter->getDefault(), $schema );
			$value = $filter->filter( $key, $value, $schema );
		}

		return $value;
	}

	/**
	 * @param string $key
	 * @param array $data
	 * @return array
	 */
	private function assertDataValueIsSet( string $key, array $data ): array {

		if ( ! isset( $data[ $key ] ) ) {
			$data[ $key ] = '';
		}

		return $data;
	}

	private function assertHasFilters(): void {
		if ( empty( $this->filters ) ) {
			throw new \RuntimeException(
				\sprintf(
					'You must provide at least one filter that implements %s.',
					FilterableInterface::class
				)
			);
		}
	}
}
