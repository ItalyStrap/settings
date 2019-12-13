<?php
/**
 * Class for admin panel
 *
 * This class add some functions for use in admin panel
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

use ItalyStrap\I18N\Translator;
use ItalyStrap\Fields\FieldsInterface;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\View\ViewInterface;

/**
 * Class for admin area
 */
class Settings implements SettingsInterface {

	/**
	 * @var array
	 */
	private $plugin;

	/**
	 * @var array
	 */
	private $theme_mods;

	/**
	 * Definition of variables containing the configuration
	 * to be applied to the various function calls wordpress
	 *
	 * @var string
	 */
	protected $capability;

	/**
	 * Get the current admin page name
	 *
	 * @var string
	 */
	protected $pagenow;

	/**
	 * Settings for plugin admin page
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * The plugin options
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * The type of fields to create
	 *
	 * @var FieldsInterface
	 */
	protected $fields_type;

	/**
	 * The fields preregistered in the config file.
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Initialize Class
	 *
	 * @param array $options Get the plugin options.
	 * @param array $settings The configuration array plugin fields.
	 * @param array $plugin The configuration array for plugin.
	 * @param array $theme_mods The theme options.
	 * @param FieldsInterface $fields_type The Fields object.
	 */
	public function __construct(
		array $options,
		array $settings,
		array $plugin,
		array $theme_mods,
		FieldsInterface $fields_type
	) {

		if ( isset( $_GET['page'] ) ) { // Input var okay.
			$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.
		}

		$this->settings = $settings;

		$this->options = $options;

		$this->plugin = $plugin;

		$this->fields_type = $fields_type;

		$this->fields = $this->getSettingsFields();

		$this->theme_mods = $theme_mods;

		$this->capability = $plugin['capability'];
	}

	/**
	 * @return array
	 */
	public function getFields() : array {
		return $this->fields;
	}

	/**
	 * Init settings for admin area
	 */
	public function load() {

		// If the theme options doesn't exist, create them.
		$this->preloadOption();

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $setting['show_on'] ) && false === $setting['show_on'] ) {
				continue;
			}

			\add_settings_section(
				$setting['id'],
				$setting['title'],
				[$this, 'renderSectionCb'], //array( $this, $field['callback'] ),
				$this->plugin['options_group'] //$setting['page']
			);

			foreach ( $setting['settings_fields'] as $key2 => $field ) {
				if ( isset( $field['show_on'] ) && false === $field['show_on'] ) {
					continue;
				}

				\add_settings_field(
					$field['id'],
					$field['title'],
					[$this, 'renderField'], //array( $this, $field['callback'] ),
					$this->plugin['options_group'], //$field['page'],
					empty( $field['section'] ) ? $key : $field['section'],
					$field['args']
				);
			}
		}

		$this->register();
	}

	/**
	 * Register settings.
	 * This allow you to override this method.
	 */
	private function register() {

		\register_setting(
			$this->plugin['options_group'],
			$this->plugin['options_name'],
			[
				'sanitize_callback'	=>
					[ ( new DataParser() )->setFields( $this->fields ), 'parse' ]
			]
		);
	}

	/**
	 * Render section CB
	 *
	 * @param  array $args The arguments for section CB.
	 */
	public function renderSectionCb( array $args ) {
		echo $args['callback'][0]->settings[ $args['id'] ]['desc'] ?? ''; // XSS ok.
	}

	/**
	 * Get the field type
	 *
	 * @param array $args Array with arguments.
	 */
	public function renderField( array $args ) {
		$args['_id'] = $args['_name'] = $this->plugin['options_name'] . '[' . $args['id'] . ']';
		echo $this->fields_type->render( $args, $this->options ); // XSS ok.
	}

	/**
	 * Get the plugin fields
	 *
	 * @return array The plugin fields
	 */
	public function getSettingsFields() {

		$fields = [];
		foreach ( (array) $this->settings as $settings_value ) {
			foreach ( $settings_value['settings_fields'] as $fields_key => $fields_value ) {
				$fields[ $fields_value['id'] ] = $fields_value['args'];
			}
		}

		return $fields;
	}

	/**
	 * Get admin settings default value in an array
	 *
	 * @return array The new array with default options
	 */
	private function getPluginSettingsArrayDefault() {

		$default_settings = array();

		foreach ( (array) $this->fields as $key => $setting ) {
			$default_settings[ $key ] = $setting['value'] ?? '';
		}

		return $default_settings;
	}

	/**
	 * Add option
	 */
	private function preloadOption() {

		if ( false === \get_option( $this->plugin['options_name'] ) ) {
			$default = $this->getPluginSettingsArrayDefault();
			\add_option( $this->plugin['options_name'], $default );
			$this->setThemeMods( (array) $default );
		}
	}

	/**
	 * Delete option
	 */
	private function deleteOption() {

		\delete_option( $this->plugin['options_name'] );
		$this->removeThemeMods( $this->getPluginSettingsArrayDefault() );
	}

	/**
	 * Set theme mods
	 *
	 * @param array $value The options array with value.
	 */
	private function setThemeMods( array $value = array() ) {

		foreach ( (array) $this->fields as $key => $field ) {
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

		foreach ( (array) $this->fields as $key => $field ) {
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

		if ( ! isset( $this->plugin['options_name'] ) ) {
			return $option;
		}

		if ( $option !== $this->plugin['options_name'] ) {
			return $option;
		}

		$this->setThemeMods( (array) $value );

		return $option;
	}
}
