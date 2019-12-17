<?php
/**
 *
 * @link http://codex.wordpress.org/Adding_Administration_Menus
 * @link http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-4-on-theme-options--wp-24902
 *
 * @todo Maybe add_settings_error()
 *
 * @since 1.0.0
 *
 * @package ItalyStrap\Settings
 */
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Fields\FieldsInterface;

/**
 * Class for admin area
 */
class Settings implements SettingsInterface {

	/**
	 * Definition of variables containing the configuration
	 * to be applied to the various function calls wordpress
	 *
	 * @var string
	 */
	protected $capability;

	/**
	 * Settings for plugin admin page
	 *
	 * @var array
	 */
	protected $sections_array = [];

	/**
	 * The plugin options
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * The fields preregistered in the config file.
	 *
	 * @var array
	 */
	protected $settingsFields = [];

	/**
	 * @var SectionsInterface
	 */
	private $sections;

	/**
	 * Initialize Class
	 *
	 * @param SectionsInterface $sections The configuration array plugin fields.
	 * @param Options $options Get the plugin options.
	 * @param string $capability
	 */
	public function __construct(
		SectionsInterface $sections,
		Options $options,
		string $capability
	) {

		$this->sections = $sections;

		$this->options = $options;

//		$this->settingsFields = $this->getSectionsFields();

		$this->capability = $capability;
	}

	/**
	 * Init settings for admin area
	 */
	public function load() {

		// If the theme options doesn't exist, create them.
//		$this->preloadOption();
		$this->sections->load();
	}

	/**
	 * Get the plugin fields
	 *
	 * @return array The plugin fields
	 */
//	public function getSectionsFields() {
//
//		$fields = [];
//		foreach ( (array) $this->sections->getSections() as $section ) {
//			foreach ( $section['fields'] as $fields_value ) {
//				$fields[ $fields_value['id'] ] = $fields_value['args'];
//			}
//		}
//
//		return $fields;
//	}

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
	 * Add option
	 */
	private function preloadOption() {

		if ( false === \get_option( $this->options->getName() ) ) {
			$default = $this->getPluginSettingsArrayDefault();
			\add_option( $this->options->getName(), $default );
			$this->setThemeMods( (array) $default );
		}
	}

	/**
	 * Delete option
	 */
	private function deleteOption() {

		\delete_option( $this->options->getName() );
		$this->removeThemeMods( $this->getPluginSettingsArrayDefault() );
	}

	/**
	 * Set theme mods
	 *
	 * @param array $value The options array with value.
	 */
	private function setThemeMods( array $value = array() ) {

		foreach ((array) $this->settingsFields as $key => $field ) {
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
