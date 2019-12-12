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
	private $plugin_name = '';

	/**
	 * Constructor
	 *
	 * @param  string $plugin_name The name of the plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Return the language 2-4 letters code
	 *
	 * @since   1.0.0
	 *
	 * @return     string 4 letters cod of the locale
	 */
	public function getLanguage(): string {

		switch ( true ) {
			case \defined( 'ICL_LANGUAGE_CODE' ):
				return \ICL_LANGUAGE_CODE;

			case \function_exists( 'cml_get_browser_lang' ):
				return \cml_get_browser_lang();

			case \function_exists( 'pll_current_language' ):
				return \pll_current_language();

			default:
				/**
				 * @link https://codex.wordpress.org/Function_Reference/get_locale
				 */
				return get_locale();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function registerString( $string_name, $value ) {

		switch ( true ) {
			case \function_exists( 'icl_register_string' ):
				\icl_register_string( $this->plugin_name, $string_name, $value );
				break;

			case \class_exists( 'CMLTranslations' ):
				\CMLTranslations::add(
					$string_name,
					$value,
					\str_replace( ' ', '-', $this->plugin_name )
				);
				break;

			case \function_exists( 'pll_register_string' ):
				\pll_register_string(
					\str_replace( ' ', '-', $this->plugin_name ),
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
				\icl_unregister_string( $this->plugin_name, $string_name );
				break;

			case \has_filter( 'cml_my_translations' ):
				\CMLTranslations::delete( \str_replace( ' ', '-', $this->plugin_name ) );
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
				return \icl_t( $this->plugin_name, $string_name, $value );

			case \has_filter( 'cml_my_translations' ):
				return \CMLTranslations::get(
					\CMLLanguage::get_current_id(),
					\strtolower( $string_name ),
					\str_replace( ' ', '-', $this->plugin_name )
				);

			case \function_exists( 'pll__' ):
				return \pll__( $string_name );

			default:
				return $value;
		}
	}

	/**
	 *
	 */
	public function textDomain() {

		/**
		 * Make theme available for translation.
		 */
		\load_theme_textdomain( 'italystrap', $this->config->get( 'PARENTPATH' ) . '/languages' );

//		if ( is_child_theme() ) {
//			\load_child_theme_textdomain( 'CHILD', $this->config->get( 'CHILDPATH' ) . '/languages' );
//		}
	}
}
