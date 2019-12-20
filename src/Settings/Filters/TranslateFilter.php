<?php
declare(strict_types=1);

namespace ItalyStrap\Settings\Filters;


use ItalyStrap\I18N\Translatable;
use ItalyStrap\Settings\FilterableInterface;

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
	public function filter( $data, array $rules ) {

		if ( $rules['translate'] ) {
			/**
			 * @todo Maybe add some kind of error if no strings are registered
			 */
			$this->translator->registerString( $rules['id'], strip_tags( $data ) );
		}

		return $data;
	}
}
