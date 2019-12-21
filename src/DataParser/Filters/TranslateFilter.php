<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;


use ItalyStrap\I18N\Translatable;
use ItalyStrap\DataParser\FilterableInterface;

class TranslateFilter implements FilterableInterface
{
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
	public function filter( $data, array $schema ) {

		if ( $schema['translate'] ) {
			/**
			 * @todo Maybe add some kind of error if no strings are registered
			 */
			$this->translator->registerString( $schema['id'], strip_tags( $data ) );
		}

		return $data;
	}
}
