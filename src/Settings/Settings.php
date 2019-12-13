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
	protected $sections = [];

	/**
	 * The plugin options
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * The type of fields to create
	 *
	 * @var FieldsInterface
	 */
	protected $fields;

	/**
	 * The fields preregistered in the config file.
	 *
	 * @var array
	 */
	protected $settingsFields = [];

	/**
	 * @var string
	 */
	private $options_group;

	/**
	 * @var string
	 */
	private $options_name;

	/**
	 * Initialize Class
	 *
	 * @param array $options Get the plugin options.
	 * @param array $sections The configuration array plugin fields.
	 * @param FieldsInterface $fields_type The Fields object.
	 * @param string $options_name
	 * @param string $options_group
	 * @param string $capability
	 */
	public function __construct(
		FieldsInterface $fields_type,
		array $options,
		array $sections,
		string $options_name,
		string $options_group,
		string $capability
	) {

		$this->sections = $sections;

		$this->options = $options;

		$this->fields = $fields_type;

		$this->settingsFields = $this->getSectionsFields();

		$this->options_name = $options_name;
		$this->options_group = $options_group;
		$this->capability = $capability;
	}

	/**
	 * Init settings for admin area
	 */
	public function load() {

		// If the theme options doesn't exist, create them.
		$this->preloadOption();

		$this->addSections();

		$this->registerSections();
	}

	/**
	 *
	 */
	private function addSections(): void {
		foreach ( $this->sections as $setting ) {
			if ( isset( $setting[ 'show_on' ] ) && false === $setting[ 'show_on' ] ) {
				continue;
			}

			\add_settings_section(
				$setting[ self::ID ],
				$setting[ self::TITLE ],
				[$this, 'renderSectionCallback'], //array( $this, $field['callback'] ),
				$this->options_group //$setting['page']
			);

			foreach ( $setting[ 'fields' ] as $field ) {
				if ( isset( $field[ 'show_on' ] ) && false === $field[ 'show_on' ] ) {
					continue;
				}

				\add_settings_field(
					$field[ self::ID ],
					$field[ self::TITLE ],
					[ $this, 'renderField' ], //array( $this, $field['callback'] ),
					$this->options_group, //$field['page'],
					$setting[ self::ID ],
					$field[ 'args' ]
				);
			}
		}
	}

	/**
	 * Register settings.
	 * This allow you to override this method.
	 */
	private function registerSections(): void {
		\register_setting(
			$this->options_group,
			$this->options_name,
			[
				'sanitize_callback'	=>
					[ ( new DataParser() )->withFields( $this->settingsFields ), 'parse' ]
			]
		);
	}

	/**
	 * Render section CB
	 *
	 * @param  array $args The arguments for section CB.
	 */
	public function renderSectionCallback( array $args ) {

//		if ( \is_callable( $this->settings[ $args['id'] ]['desc'] ) ) {
//			\call_user_func( $this->settings[ $args['id'] ]['desc'], $args );
//		}

		echo $this->sections[ $args['id'] ]['desc'] ?? ''; // XSS ok.
	}

	/**
	 * Get the field type
	 *
	 * @param array $args Array with arguments.
	 */
	public function renderField( array $args ) {
		$args['_id'] = $args['_name'] = $this->options_name . '[' . $args['id'] . ']';
		echo $this->fields->render( $args, $this->options ); // XSS ok.
	}

	/**
	 * Get the plugin fields
	 *
	 * @return array The plugin fields
	 */
	public function getSectionsFields() {

		$fields = [];
		foreach ( (array) $this->sections as $section ) {
			foreach ( $section['fields'] as $fields_value ) {
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

		foreach ((array) $this->settingsFields as $key => $setting ) {
			$default_settings[ $key ] = $setting['value'] ?? '';
		}

		return $default_settings;
	}

	/**
	 * Add option
	 */
	private function preloadOption() {

		if ( false === \get_option( $this->options_name ) ) {
			$default = $this->getPluginSettingsArrayDefault();
			\add_option( $this->options_name, $default );
			$this->setThemeMods( (array) $default );
		}
	}

	/**
	 * Delete option
	 */
	private function deleteOption() {

		\delete_option( $this->options_name );
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

		if ( ! isset( $this->options_name ) ) {
			return $option;
		}

		if ( $option !== $this->options_name ) {
			return $option;
		}

		$this->setThemeMods( (array) $value );

		return $option;
	}
}
