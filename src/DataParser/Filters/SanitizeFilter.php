<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class SanitizeFilter
 * @package ItalyStrap\Settings\Filters
 */
class SanitizeFilter implements FilterableInterface {

	const KEY = 'sanitize';

	use DefaultSchema;

	/**
	 * @var Sanitization
	 */
	private $sanitization;

	public function __construct( Sanitization $sanitization ) {
		$this->sanitization = $sanitization;
	}

	/**
	 * @inheritDoc
	 */
	public function filter( $data, array $schema ) {

		$this->sanitization->addRules( $schema[ self::KEY ] );
		$data = $this->sanitization->sanitize( $data );

		return $data;
	}
}
