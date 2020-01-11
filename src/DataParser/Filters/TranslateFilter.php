<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

use ItalyStrap\I18N\Translatable;
use ItalyStrap\DataParser\FilterableInterface;

class TranslateFilter implements FilterableInterface {

	const KEY = 'translate';

	use DefaultSchema;

	/**
	 * @var Translatable
	 */
	private $translator;

	public function __construct( Translatable $translator ) {
		$this->translator = $translator;
	}

	/**
	 * @inheritDoc
	 */
	public function filter( string $key, $value, array $schema ) {

		if ( $schema[ self::KEY ] ) {
			$this->translator->registerString( $key, \strip_tags( \strval( $value ) ) );
		}

		return $value;
	}
}
