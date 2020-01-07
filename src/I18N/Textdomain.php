<?php
declare(strict_types=1);

namespace ItalyStrap\I18N;

/**
 * Class Textdomain
 * @package ItalyStrap\I18N
 */
class Textdomain {

	private $path;

	/**
	 * @var Translator
	 */
	private $translator;

	/**
	 * Constructor
	 *
	 * @param Translator $translator
	 * @param $path
	 */
	public function __construct( Translator $translator, $path ) {
		$this->path = $path;
		$this->translator = $translator;
	}

	/**
	 * @return bool
	 */
	public function isLoaded(): bool {
		return \is_textdomain_loaded( $this->translator->getDomain() );
	}

	/**
	 *
	 */
	private function textDomain() {

		/**
		 * Make theme available for translation.
		 */
//		\load_theme_textdomain( $this->translator->getDomain(), $this->path );

//		if ( is_child_theme() ) {
//			\load_child_theme_textdomain( $this->translator->getDomain(), $this->path );
//		}

		/**
		 * Adjust priority to make sure this runs
		 */
		//\add_action( 'init', function () {
				/**
				 * Load po file
				 */
		//	\load_plugin_textdomain( $this->translator->getDomain(), null, $this->path );
		//}, 100 );

//		\load_script_textdomain( $handle, $this->translator->getDomain(), $this->path );
	}
}
