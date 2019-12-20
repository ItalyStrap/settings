<?php

use ItalyStrap\Settings\Page as P;

class AdminPageCest
{
	private $pages = [];
	private $options_from_fields = [];
	private $plugin = [];

    public function _before(AcceptanceTester $I)
    {
		tad\FunctionMockerLe\define( '__', function ( $text, $domain = 'default' ) { return $text; });
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

		$this->pages = require codecept_data_dir( 'fixtures/config/' ) . 'pages.php';
		$this->options_from_fields = require codecept_data_dir( 'fixtures/config/' ) . 'fields.php';
		$this->plugin = require codecept_data_dir( 'fixtures/config/' ) . 'plugin.php';

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginInstalled( 'settings' );
		$I->activatePlugin( 'settings' );
		$I->seePluginActivated( 'settings' );
		$I->seeOptionInDatabase( [ 'option_name' => $this->plugin['options_name'] ] );
    }

	/**
	 * @test
	 * @param AcceptanceTester $I
	 */
    public function CanSeeSettingsPageWithFieldsAndSubmit(AcceptanceTester $I)
    {
		$page = $this->pages[0][ P::SLUG ];
    	$I->amOnAdminPage( '?page=' . $page );

		// Submit the form as a user would submit it.
		$I->submitForm( '#' . $this->plugin['options_group'], [] );

		$I->seeOptionInDatabase( [ 'option_name' => $this->plugin['options_name'] ] );
    }
}
