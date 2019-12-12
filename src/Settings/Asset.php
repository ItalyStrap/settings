<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface;

class Asset
{
	/**
	 * @var string
	 */
	private $pagenow = '';

	private $config;

	public function __construct( ConfigInterface $config ) {
		if ( isset( $_GET['page'] ) ) { // Input var okay.
			$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.
		}

		$this->config = $config;
	}

	/**
	 * Add style for ItalyStrap admin page
	 *
	 * @param  string $hook The admin page name (admin.php - tools.php ecc).
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 */
	public function enqueue( $hook ) {

		/**
		 * @todo Per ora cerca solo nel primo array, migliorare in caso di più pagine
		 */
		if ( \in_array( $this->pagenow, $this->config['submenu_pages'][0], true ) ) {
			\wp_enqueue_script(
				$this->pagenow,
				\plugins_url( 'js/' . $this->pagenow . '.min.js', __FILE__ ),
				['jquery-ui-tabs', 'jquery-form'],
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
