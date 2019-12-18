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

	private $fields = [];

	public function __construct() {
		$this->validation = new Validation;
		$this->sanitization = new Sanitization;
		$this->translator = new Translator( 'ItalyStrap' );
	}

	/**
	 * Sanitize the input data
	 *
	 * @param  array $data The input array.
	 * @return array           Return the array sanitized
	 */
	public function parse( $data ) {

		foreach ( $this->fields as $field ) {
			if ( ! isset( $data[ $field['id'] ] ) ) {
				$data[ $field['id'] ] = '';
			}

			/**
			 * Register string for translation
			 */
			if ( isset( $field['translate'] ) && true === $field['translate'] ) {
				$this->translator->registerString( $field['id'], strip_tags( $data[ $field['id'] ] ) );
			}

			/**
			 * Validate fields if $field['validate'] is set
			 * @todo add_settings_error()
			 */
			if ( isset( $field['validate'] ) ) {
				$this->validation->addRules( $field['validate'] );

				if ( false === $this->validation->validate( $data[ $field['id'] ] ) ) {
					$data[ $field['id'] ] = '';
				}
			}

			/**
			 * @todo Fare il controllo che $data[ $field['id'] ] non sia un array
			 *       Nel caso fosse un array bisogna fare un sanitize apposito,
			 *       per ora ho aggiunto un metodo ::sanitize_select_multiple() che
			 *       sanitizza i valori nell'array ma bisogna sempre indicarlo
			 *       nella configurazione del widget/option altrimenti da errore.
			 *       Valutare anche in futuro di fare un metodo ricorsivo per array
			 *       multidimensionali.
			 *       Altre possibilità sono gli array con valori boleani o float e int
			 *       Per ora sanitizza come fossero stringhe.
			 */
			if ( isset( $field['capability'] ) && true === $field['capability'] ) {
//				$data[ $field['id'] ] = $data[ $field['id'] ];
				continue;
			}

			if ( isset( $field['sanitize'] ) ) {
				$this->sanitization->addRules( $field['sanitize'] );
			} else {
				$this->sanitization->addRules( 'strip_tags|trim' );
			}

			$data[ $field['id'] ] = $this->sanitization->sanitize( $data[ $field['id'] ] );
		}

		return $data;
	}

	/**
	 * @param array $fields
	 * @return DataParser
	 */
	public function withFields( array $fields ): DataParser {
		$this->fields = $fields;
		return $this;
	}
}
