<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\I18N\Translator;

class DataParser {

	/**
	 * @var Validation
	 */
	private $validation;
	/**
	 * @var Sanitization
	 */
	private $sanitization;
	/**
	 * @var Translator
	 */
	private $translator;

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
		$this->validation = new Validation;
		$this->sanitization = new Sanitization;
		$this->translator = new Translator( 'ItalyStrap' );
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

			if ( ! isset( $data[ $key ] ) ) {
				$data[ $key ] = '';
			}

			if ( empty( $this->filters ) ) {
				$data[ $key ] = \trim( \strip_tags( $data[ $key ] ) );
			}

			/**
			 * @todo Fare il controllo che $data[ $key ] non sia un array
			 *       Nel caso fosse un array bisogna fare un sanitize apposito,
			 *       per ora ho aggiunto un metodo ::sanitize_select_multiple() che
			 *       sanitizza i valori nell'array ma bisogna sempre indicarlo
			 *       nella configurazione del widget/option altrimenti da errore.
			 *       Valutare anche in futuro di fare un metodo ricorsivo per array
			 *       multidimensionali.
			 *       Altre possibilità sono gli array con valori boleani o float e int
			 *       Per ora sanitizza come fossero stringhe.
			 */
//			if ( isset( $field['capability'] ) && true === $field['capability'] ) {
////				$data[ $key ] = $data[ $key ];
//				continue;
//			}

			foreach ( $this->filters as $filter ) {
				$data[ $key ] = $filter->filter( $schema, $data );
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
}
