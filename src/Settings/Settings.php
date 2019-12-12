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
	 * @var Translator
	 */
	private $translator;
	/**
	 * @var Validation
	 */
	private $validation;
	/**
	 * @var Sanitization
	 */
	private $sanitization;

	/**
	 * @var ViewInterface
	 */
	private $view;
	/**
	 * @var array
	 */
	private $theme_mods;

	/**
	 * Returns an array of hooks that this subscriber wants to register with
	 * the WordPress plugin API.
	 *
	 * @hooked update_option - 10
	 *
	 * @return array
	 */
	public static function get_subscribed_events() {

		return array(
			// 'hook_name'							=> 'method_name',
			'update_option'	=> array(
				'function_to_add'	=> 'save',
				'accepted_args'		=> 3,
			),
		);
	}

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
	 * The plugin name
	 *
	 * @var string
	 */
	protected $plugin_slug;

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
	 * 	The array with all sub pages if exist
	 *
	 * @var array
	 */
	protected $submenus = array();

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
	 * @param ViewInterface $view
	 */
	public function __construct(
		array $options,
		array $settings,
		array $plugin,
		array $theme_mods,
		FieldsInterface $fields_type,
		ViewInterface $view
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

		$this->view = $view;
	}

	/**
	 * @return array
	 */
	public function getFields() : array {
		return $this->fields;
	}

	/**
	 * Add plugin primary page in admin panel
	 */
	public function addMenuPage() {

		if ( ! $this->plugin['menu_page'] ) {
			return;
		}

		\add_menu_page(
			$this->plugin['menu_page']['page_title'],
			$this->plugin['menu_page']['menu_title'],
			$this->capability, // $this->plugin['menu_page']['capability'],
			$this->plugin['menu_page']['menu_slug'],
			array( $this, 'getView'),
			$this->plugin['menu_page']['icon_url'],
			$this->plugin['menu_page']['position']
		);

		$this->addSubMenuPage( $this->plugin['submenu_pages'] );
	}


	/**
	 * Add sub menù pages for plugin's admin page
	 */
	private function addSubMenuPage( array $submenu_pages ) {

		if ( ! $submenu_pages ) {
			return;
		}

		foreach ( (array) $submenu_pages as $submenu ) {
			if ( isset( $submenu['show_on'] ) && ! $this->showOn( $submenu[ 'show_on' ] ) ) {
				continue;
			}

			add_submenu_page(
				$submenu['parent_slug'],
				$submenu['page_title'],
				$submenu['menu_title'],
				$this->capability, // $submenu['capability'],
				$submenu['menu_slug'],
				// $submenu['function_cb']
				[ $this, 'getView']
			);
		}
	}

	/**
	 * The add_submenu_page callback
	 */
	public function getView() {

		if ( ! \current_user_can( $this->capability ) ) {
			\wp_die( \esc_attr__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$file_path = \file_exists( $this->plugin['admin_view_path'] . $this->pagenow . '.php' )
			? $this->plugin['admin_view_path'] . $this->pagenow . '.php'
			: __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'form.php';

//		try {
//			echo $this->view->render( $this->pagenow, [] );
//		} catch (\Exception $e) {
//			require( $file_path );
//		}

		require $file_path;
	}

	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 2.7.0
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output.
	 */
	public function doSettingsSections( $page ) {

		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		$count = 1;

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			echo '<div id="tabs-' . $count . '" class="wrap">'; // XSS ok.
			if ( $section['title'] ) {
				echo "<h2>{$section['title']}</h2>\n"; // XSS ok.
			}

			if ( $section['callback'] ) {
				\call_user_func( $section['callback'], $section );
			}

			if (
				! isset( $wp_settings_fields )
				|| ! isset( $wp_settings_fields[ $page ] )
				|| ! isset( $wp_settings_fields[ $page ][ $section['id'] ] )
			) {
				continue;
			}
			echo '<table class="form-table">';
			\do_settings_fields( $page, $section['id'] );
			echo '</table>';
			echo '</div>';
			$count++;
		}
	}

	/**
	 * Create the nav tabs for section in admin plugin area
	 */
	public function createNavTab() {

		$count = 1;

		$out = '<ul>';

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $setting['show_on'] ) && false === $setting['show_on'] ) {
				continue;
			}

			$out .= '<li><a href="#tabs-' . $count . '">' . $setting['tab_title'] . '</a></li>';
			$count++;
		}

		$out .= '</ul>';
		echo $out; // XSS ok.
	}

	/**
	 * Init settings for admin area
	 */
	public function settingsInit() {

		// If the theme options doesn't exist, create them.
		$this->addOption();

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $setting['show_on'] ) && false === $setting['show_on'] ) {
				continue;
			}

			\add_settings_section(
				$setting['id'],
				$setting['title'],
				array( $this, 'renderSectionCb'), //array( $this, $field['callback'] ),
				$this->plugin['options_group'] //$setting['page']
			);

			foreach ( $setting['settings_fields'] as $key2 => $field ) {
				if ( isset( $field['show_on'] ) && false === $field['show_on'] ) {
					continue;
				}

				\add_settings_field(
					$field['id'],
					$field['title'],
					array( $this, 'getFieldType'), //array( $this, $field['callback'] ),
					$this->plugin['options_group'], //$field['page'],
					empty( $field['section'] ) ? $key : $field['section'],
					$field['args']
				);
			}
		}

		$this->registerSetting();
	}

	/**
	 * Register settings.
	 * This allow you to override this method.
	 */
	private function registerSetting() {

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
	 * Add style for ItalyStrap admin page
	 *
	 * @param  string $hook The admin page name (admin.php - tools.php ecc).
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 */
	public function enqueueAdminStyleScript( $hook ) {

		/**
		 * @todo Per ora cerca solo nel primo array, migliorare in caso di più pagine
		 */
		if ( \in_array( $this->pagenow, $this->plugin['submenu_pages'][0], true ) ) {
			\wp_enqueue_script(
				$this->pagenow,
				\plugins_url( 'js/' . $this->pagenow . '.min.js', __FILE__ ),
				array( 'jquery-ui-tabs', 'jquery-form' ),
				false,
				false
			);

			\wp_enqueue_style(
				$this->pagenow,
				\plugins_url( 'css/' . $this->pagenow . '.css', __FILE__ )
			);
		}
	}

	/**
	 * Render section CB
	 *
	 * @param  array $args The arguments for section CB.
	 */
	public function renderSectionCb( array $args ) {
		echo isset( $args['callback'][0]->settings[ $args['id'] ]['desc'] )
			? $args['callback'][0]->settings[ $args['id'] ]['desc']
			: ''; // XSS ok.
	}

	/**
	 * Get the field type
	 *
	 * @param array $args Array with arguments.
	 */
	public function getFieldType( array $args ) {

		/**
		 * Set field id and name
		 */
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
			$default_settings[ $key ] = isset( $setting['default'] ) ? $setting['default'] : '';
		}

		return $default_settings;
	}

	/**
	 * Add option
	 */
	private function addOption() {

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

	/**
	 * Get Aside for settings page
	 */
	public function getAside() {

		$file_path = \file_exists( $this->plugin['admin_view_path'] . 'aside.php' )
			? $this->plugin['admin_view_path'] . 'aside.php'
			: __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'aside.php';

		require $file_path;
	}

	/**
	 * Show on page
	 *
	 * @param string|bool $condition The config array.
	 * @return bool         Return true if conditions are resolved.
	 */
	private function showOn( $condition ) {

		if ( \is_bool( $condition ) ) {
			return $condition;
		}

		if ( \is_callable( $condition ) ) {
			return (bool) \call_user_func( $condition );
		}

		return false;
	}
}
