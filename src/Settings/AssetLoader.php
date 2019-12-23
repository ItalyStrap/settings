<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface;

class AssetLoader {

	/**
	 * @var string
	 */
	private $current_page = '';

	/**
	 * Add style for ItalyStrap admin page
	 *
	 * @param  string $hook The admin page name (admin.php - tools.php ecc).
	 *                      {toplevel}_page_{$this->current_page}
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 */
	public function load( $hook = '' ) {

		if ( ! isset( $_GET['page'] ) ) { // Input var okay.
			return;
		}

		$this->current_page = \stripslashes( $_GET['page'] ); // Input var okay.

		if ( \strpos( $hook, $this->current_page ) !== false ) {
			\wp_enqueue_script(
				$this->current_page,
				\plugins_url( '/assets/js/script.min.js', __FILE__ ),
				['jquery-ui-tabs', 'jquery-form'],
				false,
				false
			);

			\wp_enqueue_style(
				$this->current_page,
				\plugins_url( '/assets/css/style.css', __FILE__ )
			);

			/**
			 * Classe da aggiungere al color field
			 * @todo $('.wp-color-picker-field').wpColorPicker();
			 */
//			wp_enqueue_style( 'wp-color-picker' );
//
//			wp_enqueue_media();
//			wp_enqueue_script( 'wp-color-picker' );
//			wp_enqueue_script( 'jquery' );
		}
	}
}
