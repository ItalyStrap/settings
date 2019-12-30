<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Settings\Page as P;

class AdminPageCest {

	private $page = [];
	private $options_from_fields = [];
	private $option_name = [];

	// phpcs:ignore -- Method from Codeception
	public function _before(\AcceptanceTester $I) {
		\tad\FunctionMockerLe\define( '__', function ( $text, $domain = 'default' ) {
			return $text;
		});
//		tad\FunctionMockerLe\define( 'admin_url', function ( $text ) { return $text; });

		$constant = [
			'ITALYSTRAP_BASENAME'	=> '',
			'ITALYSTRAP_PLUGIN_PATH'	=> codecept_root_dir(),
		];

		foreach ( $constant as $name => $value ) {
			if ( ! \defined( $name ) ) {
				\define( $name, $value );
			}
		}

		/**
		 * Set to `italystrap` because is set in the dump of DB
		 */
		$this->option_name = 'italystrap';

		$this->page = require \codecept_data_dir( 'fixtures/config/' ) . 'page.php';
		$this->options_from_fields = require \codecept_data_dir( 'fixtures/config/' ) . 'fields.php';

		$I->amOnPage( '/wp-admin' );
		$I->tryToClick( 'Aggiornamento database WordPress', '.button-primary' );
		$I->tryToClick( 'Continua', '.button-large' );

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginInstalled( 'settings' );
		$I->activatePlugin( 'settings' );
		$I->seePluginActivated( 'settings' );
		$I->seeOptionInDatabase( [ 'option_name' => $this->option_name ] );
	}

	/**
	 * @test
	 * @param \AcceptanceTester $I
	 */
	public function canSeeSettingsPageWithFieldsAndSubmit(\AcceptanceTester $I) {
		$page = $this->page[ P::SLUG ];
		$I->amOnAdminPage( '?page=' . $page );

		// Submit the form as a user would submit it.
		$I->submitForm( '#' . $this->page[ P::SLUG ], [] );

		$I->seeOptionInDatabase( [ 'option_name' => $this->option_name ] );
	}
}
