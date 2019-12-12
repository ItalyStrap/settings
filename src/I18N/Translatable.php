<?php
/**
 * Translatable Interface API
 *
 * Interface for translatable Class in themes and plugins
 *
 * @link italystrap.com
 * @since 2.9.0
 *
 * @package ItalyStrap\I18N
 */
declare(strict_types=1);

namespace ItalyStrap\I18N;

interface Translatable {

	/**
	 * Add registration for multilanguage string (contain hook)
	 *
	 * @since   1.0.0
	 *
	 * @param     string $string_name              The name of the string.
	 * @param     string $value                    The value.
	 */
	public function registerString( $string_name, $value );

	/**
	 * Unregister multilanguage string, Polylang missing support of this feature
	 * For deleting Pulylang string go to the Polylang string translation admin page.
	 *
	 * @since   1.0.0
	 *
	 * @param     string $string_name The name of the string.
	 */
	public function deregisterString( $string_name );

	/**
	 * Get multilanguage string
	 *
	 * @since   1.0.0
	 *
	 * @param     string $string_name              The name of the string.
	 * @param     string $value                    The value.
	 * @return string
	 */
	public function getString( $string_name, $value );
}
