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
    }
}
