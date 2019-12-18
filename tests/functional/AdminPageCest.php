<?php
declare(strict_types=1);

use ItalyStrap\Settings\Page as P;

class AdminPageCest
{
	private $pages = [];
	private $options_from_fields = [];
	private $plugin = [];

    public function _before(FunctionalTester $I)
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
		$this->options_from_advanced_fields = require codecept_data_dir( 'fixtures/config/' ) . 'fields-advanced.php';
		$this->plugin = require codecept_data_dir( 'fixtures/config/' ) . 'plugin.php';

		$this->all_options = \array_merge( $this->options_from_fields, $this->options_from_advanced_fields );

		$this->count_options = \count( $this->all_options );

		$I->loginAsAdmin();
		$page = $this->pages['page'][ P::SLUG ];
		$I->amOnAdminPage( 'admin.php?page=' . $page );
		$I->seeElement( 'input', [ 'name' => 'italystrap[text]' ] );
    }

	/**
	 * @test
	 * @param FunctionalTester $I
	 */
	public function CanSeeSettingsPageWithFieldsAndSubmit(FunctionalTester $I)
	{
		$option = $I->grabOptionFromDatabase( $this->plugin['options_name'] );
		codecept_debug( $option );

		$page = $this->pages['page'][ P::SLUG ];
		$I->amOnAdminPage( 'admin.php?page=' . $page );

		$types = \ItalyStrap\Fields\ViewFactory::getTypes();

//		foreach ( $this->options_from_fields as $field ) {
//
//			$I->seeElement( ['id' => 'italystrap[' . $field['args']['id'] . ']' ]
////				, [ 'type' => $field['args']['type'] ]
//			);
//		}

//					$I->seeElement( '#italystrap');

		$I->seeElement( 'input', [ 'type' => 'checkbox' ] );

		$I->checkOption([ 'name' => 'italystrap[checkbox]' ] );

		$formFields =  [
			'italystrap[text]'	=> 'on',
		];

		// Submit the form as a user would submit it.
		$I->submitForm( '#' . $this->plugin['options_group'], $formFields );

		$option = $I->grabOptionFromDatabase( $this->plugin['options_name'] );
		codecept_debug( $option );

		$I->seeOptionInDatabase( [ 'option_name' => $this->plugin['options_name'] ] );

		\PHPUnit\Framework\Assert::assertCount( $this->count_options, $option, '' );
	}
}
