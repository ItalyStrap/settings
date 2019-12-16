<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\View\Exceptions\ViewNotFoundException;

/**
 * Class ViewPage
 * @package ItalyStrap\Settings
 */
class ViewPage
{
	const DS = DIRECTORY_SEPARATOR;

	/**
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * @var FinderInterface
	 */
	private $finder;

	/**
	 * @var string
	 */
	private $options_group;

	/**
	 * @var string
	 */
	private $pagenow;

	private $config;

	private $sections;

	public function __construct( FinderInterface $finder ) {

		if ( isset( $_GET['page'] ) ) { // Input var okay.
			$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.
		}

		$this->finder = $finder;

		$this->options_group = '';
	}

	/**
	 * @param mixed $capability
	 * @return ViewPage
	 */
	public function withCapability( $capability ): ViewPage {
		$this->capability = $capability;
		return $this;
	}

	/**
	 * @param string $view
	 * @return string
	 */
	public function render( $view ) {
		$this->assertCurrentUserCan();

		if ( \is_readable( $view ) ) {
			require $view;
			return '';
		}

		try {
			require $this->finder->find( $view );
		} catch ( ViewNotFoundException $exception ) {
			// Fail silently and load default form view
			require __DIR__ . self::DS . 'view' . self::DS . 'form.php';
		}

		return '';
	}

	/**
	 * The add_submenu_page callback
	 */
	public function getView() {

		$this->assertCurrentUserCan();

		$file_path = \file_exists( $this->config['admin_view_path'] . $this->pagenow . '.php' )
			? $this->config['admin_view_path'] . $this->pagenow . '.php'
			: __DIR__ . self::DS . 'view' . self::DS . 'form.php';

		require $file_path;
	}

	/**
	 * Get Aside for settings page
	 */
	public function getAside() {

		$file_path = \file_exists( $this->config['admin_view_path'] . 'aside.php' )
			? $this->config['admin_view_path'] . 'aside.php'
			: __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'aside.php';

		require $file_path;
	}

	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections
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

		foreach ( $this->sections->getSections() as $key => $setting ) {
			if ( isset( $setting['show_on'] ) && false === $setting['show_on'] ) {
				continue;
			}

			$out .= '<li><a href="#tabs-' . $count . '">' . $setting['tab_title'] . '</a></li>';
			$count++;
		}

		$out .= '</ul>';

		if ( $count <= 2 ) {
			return '';
		}

		echo $out; // XSS ok.
		return '';
	}

	private function assertCurrentUserCan(): void {
		if ( !\current_user_can( $this->capability ) ) {
			\wp_die( \esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
	}
}
