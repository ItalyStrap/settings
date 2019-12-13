<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface;

class Asset {

	/**
	 * @var string
	 */
	private $pagenow = '';

	/**
	 * Add style for ItalyStrap admin page
	 *
	 * @param  string $hook The admin page name (admin.php - tools.php ecc).
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 */
	public function enqueue( $hook = '' ) {

		if ( ! isset( $_GET['page'] ) ) { // Input var okay.
			return;
		}

		$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.

		if ( \strpos( $hook, $this->pagenow ) !== false ) {
			\wp_enqueue_script(
				$this->pagenow,
				\plugins_url( '/js/script.min.js', __FILE__ ),
				['jquery-ui-tabs', 'jquery-form'],
				false,
				false
			);

			\wp_enqueue_style(
				$this->pagenow,
				\plugins_url( '/css/style.css', __FILE__ )
			);
		}
	}
}
