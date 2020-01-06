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
	 * DataParser constructor.
	 * @param array $schema
	 */
	public function __construct( array $schema = [] ) {
		$this->schema = $schema;
	}

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
	public function parse( array $data ): array {

		if ( empty( $this->filters ) ) {
			throw new \RuntimeException( 'You must provide at least one filter' );
		}

		foreach ( $this->schema as $key => $schema ) {
			$data = $this->applyFilters( $data, $key, $schema );
		}

		return $data;
	}

	/**
	 * @param array $data
	 * @param string $key
	 * @param array $schema
	 * @return array
	 */
	private function applyFilters( array $data, $key, array $schema ): array {

		$this->mergeWithDefault( $schema );
		$data = $this->assertDataValueIsSet( $data, $key );

		/* @var $filter FilterableInterface */
		foreach ( $this->filters as $filter ) {
			if ( ! \is_array( $data[ $key ] ) ) {
				$data[ $key ] = $filter->filter( $data[ $key ], $schema );
				continue;
			}

			foreach ( (array) $data[ $key ] as $index => $value ) {
				$data[ $key ][ $index ] = $filter->filter( $value, $schema );
			}
		}
		return $data;
	}

	/**
	 * @param array $schema
	 */
	private function mergeWithDefault( array &$schema ) {
		$default = [
			'capability'	=> false,
			'sanitize'		=> 'strip_tags|trim',
			'translate'		=> false,
			'validate'		=> false,
		];

		$schema = \array_replace_recursive( $default, $schema );
	}

	/**
	 * @param array $data
	 * @param string $key
	 * @return array
	 */
	private function assertDataValueIsSet( array $data, $key ): array {
		if ( ! isset( $data[ $key ] ) ) {
			$data[ $key ] = '';
		}
		return $data;
	}
}
