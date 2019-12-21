<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class ValidateFilter
 * @package ItalyStrap\Settings\Filters
 */
class ValidateFilter implements FilterableInterface
{
	/**
	 * @var Validation
	 */
	private $validation;

	public function __construct( Validation $validation ) {
		$this->validation = $validation;
	}

	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {

		if ( ! $schema['validate'] ) {
			return $data;
		}

		$this->validation->addRules( $schema['validate'] );

		if ( false === $this->validation->validate( $data ) ) {

			/**
			 * Validate fields if $field['validate'] is set
			 * @todo add_settings_error()
			 */
			return '';
		}

		return $data;
	}
}
