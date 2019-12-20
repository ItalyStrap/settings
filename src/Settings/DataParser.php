<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

class DataParser {

	/**
	 * @var array
	 */
	private $schema = [];

	/**
	 * @var array
	 */
	private $filters = [];

	public function __construct( array $schema = [] ) {
		$this->schema = $schema;
	}

	/**
	 * @param array $schema
	 * @return DataParser
	 */
	public function withSchema( array $schema ): DataParser {
		$this->schema = \array_replace_recursive( $this->schema, $schema );
		return $this;
	}

	/**
	 * @param FilterableInterface ...$filters
	 * @return DataParser
	 */
	public function withFilters( FilterableInterface ...$filters ): DataParser {
		$this->filters = \array_merge( $this->filters, $filters );
		return $this;
	}

	/**
	 * Sanitize the input data
	 *
	 * @param  array $data The input array.
	 * @return array           Return the array sanitized
	 */
	public function parse( array $data ): array {



		foreach ( $this->schema as $schema ) {
			$this->mergeWithDefault( $schema );
			$key = $schema['id'];
			$data = $this->assertDataValueIsSet( $data, $key );

			if ( empty( $this->filters ) ) {
				$data[ $key ] = \trim( \strip_tags( $data[ $key ] ) );
			}

			$data = $this->applyFilters( $data, $key, $schema );
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
	 * @param $key
	 * @return array
	 */
	private function assertDataValueIsSet( array $data, $key ): array {
		if ( ! isset( $data[ $key ] ) ) {
			$data[ $key ] = '';
		}
		return $data;
	}

	/**
	 * @param array $data
	 * @param string $key
	 * @param array $schema
	 * @return array
	 */
	private function applyFilters( array $data, string $key, array $schema ): array {
		foreach ( $this->filters as $filter ) {

			if ( ! \is_array( $data[ $key ] ) ) {
				$data[ $key ] = $filter->filter( $data[ $key ], $schema );
				continue;
			}

			foreach ( (array)$data[ $key ] as $index => $value ) {
				$data[ $key ][ $index ] = $filter->filter( $value, $schema );
			}
		}
		return $data;
	}
}
