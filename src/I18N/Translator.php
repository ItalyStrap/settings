<?php
/**
 * Translator API Class
 *
 * This is an adapter class for translating strings in themes and plugins.
 *
 * @forked from:
 * @link https://github.com/Mte90/WordPress-Plugin-Boilerplate-Powered
 * @link https://gist.github.com/Mte90/fe687ceed408ab743238
 *
 * @link italystrap.com
 *
 * @package ItalyStrap\I18N
 */
declare(strict_types=1);

namespace ItalyStrap\I18N;

/**
 * Translator Class
 */
class Translator implements Translatable {

	/**
	 * The name of the plugin to use as a string group
	 *
	 * @var string
	 */
	private $domain = '';

	/**
	 * @return string
	 */
	public function getDomain(): string {
		return $this->domain;
	}

	/**
	 * Constructor
	 *
	 * @param  string $domain The name of the plugin.
	 */
	public function __construct( $domain ) {
		$this->domain = $domain;
	}

	/**
	 * @inheritDoc
	 */
	public function getLanguage(): string {

		switch ( true ) {
			case \defined( 'ICL_LANGUAGE_CODE' ):
				return \ICL_LANGUAGE_CODE;

			case \function_exists( 'cml_get_browser_lang' ):
				return \cml_get_browser_lang();

			case \function_exists( 'pll_current_language' ):
				return \pll_current_language();

			case \function_exists( 'get_user_locale' ):
				return \get_user_locale();

			default:
				/**
				 * @link https://codex.wordpress.org/Function_Reference/get_locale
				 */
				return \get_locale();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getAvailableLanguages( $dir = null ): array {
		return \get_available_languages( $dir );
	}

	/**
	 * @inheritDoc
	 */
	public function registerString( $string_name, $value ) {

		switch ( true ) {
			case \function_exists( 'icl_register_string' ):
				\icl_register_string( $this->domain, $string_name, $value );
				break;

			case \class_exists( 'CMLTranslations' ):
				\CMLTranslations::add(
					$string_name,
					$value,
					\str_replace( ' ', '-', $this->domain )
				);
				break;

			case \function_exists( 'pll_register_string' ):
				\pll_register_string(
					\str_replace( ' ', '-', $this->domain ),
					$string_name
				);
				break;

			default:
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function deregisterString( $string_name ) {

		switch ( true ) {
			case \function_exists( 'icl_unregister_string' ):
				\icl_unregister_string( $this->domain, $string_name );
				break;

			case \has_filter( 'cml_my_translations' ):
				\CMLTranslations::delete( \str_replace( ' ', '-', $this->domain ) );
				break;

			default:
				break;
		}
	}

	/**
	 * @inheritDoc
	 *
	 */
	public function getString( $string_name, $value ) {

		switch ( true ) {
			case \function_exists( 'icl_t' ):
				return \icl_t( $this->domain, $string_name, $value );

			case \has_filter( 'cml_my_translations' ):
				return \CMLTranslations::get(
					\CMLLanguage::get_current_id(),
					\strtolower( $string_name ),
					\str_replace( ' ', '-', $this->domain )
				);

			case \function_exists( 'pll__' ):
				return \pll__( $string_name );

			default:
				return $value;
		}
	}
}
