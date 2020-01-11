<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\Exception\InvalidValue;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class ValidateFilter
 * @package ItalyStrap\Settings\Filters
 */
class ValidateFilter implements FilterableInterface {

	const KEY = 'validate';

	use DefaultSchema;

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
	public function filter( string $key, $value, array $schema ) {

		if ( ! $schema[ self::KEY ] ) {
			return $value;
		}

		$this->validation->addRules( $schema[ self::KEY ] );

		if ( false === $this->validation->validate( \strval( $value ) ) ) {
			throw new InvalidValue( \sprintf(
				'The "%s" is not valid.',
				$key
			) );
		}

		return $value;
	}
}
