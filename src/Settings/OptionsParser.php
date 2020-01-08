<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class OptionsParser
 * @package ItalyStrap\Settings
 *
 * $options_parser = new \ItalyStrap\Settings\OptionsParser( $options_obj );
 * add_action( 'update_option', [ $options_parser, 'save' ], 10, 3 );
 */
class OptionsParser {

	/**
	 * The plugin options
	 *
	 * @var OptionsInterface
	 */
	protected $options;

	/**
	 * The fields preregistered in the config file.
	 *
	 * @var array
	 */
	protected $settingsFields = [];

	/**
	 * Initialize Class
	 *
	 * @param OptionsInterface $options Get the plugin options.
	 */
	public function __construct( OptionsInterface $options ) {
		$this->options = $options;
	}

	/**
	 * Get admin settings default value in an array
	 *
	 * @return array The new array with default options
	 */
	private function getPluginSettingsArrayDefault() {

		$default_settings = array();

		foreach ( (array) $this->settingsFields as $key => $setting ) {
			$default_settings[ $key ] = $setting['value'] ?? '';
		}

		return $default_settings;
	}

	/**
	 * Preload option
	 *
	 */
	public function preloadOption() {
		if ( empty( $this->options->toArray() ) ) {
			$default = $this->getPluginSettingsArrayDefault();
			$this->options->addAll( $default );
			$this->setThemeMods( (array) $default );
		}
	}

	/**
	 * Delete option
	 */
	public function deleteOption() {
		$this->options->removeAll();
		$this->removeThemeMods( $this->getPluginSettingsArrayDefault() );
	}

	/**
	 * Set theme mods
	 *
	 * @param array $value The options array with value.
	 */
	private function setThemeMods( array $value = array() ) {
		foreach ( (array) $this->settingsFields as $key => $field ) {
			if ( isset( $field['option_type'] ) && 'theme_mod' === $field['option_type'] ) {
				\set_theme_mod( $key, $value[ $key ] );
			}
		}
	}

	/**
	 * Remove theme mods
	 *
	 * @param array $value The options array with value.
	 */
	private function removeThemeMods( array $value = array() ) {
		foreach ( (array) $this->settingsFields as $key => $field ) {
			if ( isset( $field['option_type'] ) && 'theme_mod' === $field['option_type'] ) {
				\remove_theme_mod( $key );
			}
		}
	}

	/**
	 * Save options in theme_mod
	 *
	 * @param  string $option    The name of the option.
	 * @param  mixed  $old_value The old options.
	 * @param  mixed  $value     The new options.
	 *
	 * @return string            The name of the option.
	 */
	public function save( $option, $old_value, $value ) {

		if ( $option !== $this->options->getName() ) {
			return $option;
		}

		$this->setThemeMods( (array) $value );

		return $option;
	}
}
