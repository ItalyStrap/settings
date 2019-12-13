<?php

class AdminPageCest
{
    public function _before(AcceptanceTester $I)
    {
    	$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginInstalled( 'settings' );
		$I->activatePlugin( 'settings' );
		$I->seePluginActivated( 'settings' );
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    	$I->amOnAdminPage( '?page=italystrap-dashboard' );
    	$I->seeElement( 'input', [ 'name' => 'italystrap[test_mode]' ] );

    	$I->checkOption([ 'name' => 'italystrap[test_mode]' ] );

//    	$I->fillField( [ 'id' => 'italystrap[test_mode]' ], '1' );

//		$I->click('#italystrap_options_group input[type=submit]');

		$formFields =  [
			'italystrap[test_mode]'	=> 'on',
		];

		// Submit the form as a user would submit it.
		$I->submitForm( '#italystrap_options_group', $formFields );

		$option = $I->grabOptionFromDatabase( 'italystrap' );

		codecept_debug( $option );

		$I->seeOptionInDatabase( [ 'option_name' => 'italystrap' ] );

//		$I->seeElement( 'img', [ 'class' => 'loading-gif' ] );
    }
}
