<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

class Asset
{
	/**
	 * @var string
	 */
	private $pagenow = '';

	private $plugin;

	public function __construct( array $plugin ) {
		if ( isset( $_GET['page'] ) ) { // Input var okay.
			$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.
		}

		$this->plugin = $plugin;
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

}
