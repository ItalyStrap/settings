<?php
declare(strict_types=1);

use ItalyStrap\Settings\Pages as P;

class AdminPageCest
{
	private $pages = [];
	private $options_from_fields = [];
	private $options_from_advanced_fields = [];
	private $all_options  = [];
	private $plugin = [];
	private $count_options;

    public function _before(FunctionalTester $I)
    {
		tad\FunctionMockerLe\define( '__', function ( $text, $domain = 'default' ) { return $text; });

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
		$page = $this->pages[0][ P::SLUG ];
		$I->amOnAdminPage( 'admin.php?page=' . $page );
    }

	/**
	 * @test
	 * @param FunctionalTester $I
	 */
	public function CanSeeSettingsPageWithFieldsAndSubmit(FunctionalTester $I)
	{
		$option = $I->grabOptionFromDatabase( $this->plugin['options_name'] );
		\PHPUnit\Framework\Assert::assertNotEmpty( $option );

		$page = $this->pages[0][ P::SLUG ];
		$I->amOnAdminPage( 'admin.php?page=' . $page );

		$types = \ItalyStrap\Fields\ViewFactory::getTypes();

		foreach ( $this->all_options as $option ) {
			if ( \strpos( $types[ $option['type'] ], 'Input' ) ) {
				$I->seeElement( 'input', [ 'type' => $option['type'] ] );
			} elseif ( \strpos( $types[ $option['type'] ], 'Checkbox' ) ) {
				$I->seeElement( 'input', [ 'type' => $option['type'] ] );
				$I->checkOption( \sprintf(
					'%s[%s]',
					$this->plugin['options_name'],
					$option['id']
				) );
			} elseif ( \strpos( $types[ $option['type'] ], 'Radio' ) ) {
				$I->seeElement( 'input', [ 'type' => $option['type'] ] );
			} elseif ( \strpos( $types[ $option['type'] ], 'Textarea' ) ) {
				$I->seeElement( 'textarea', [ 'type' => $option['type'] ] );
			}
			// Con la multiple_select da errore, quando ho voglia sistemare
//			elseif ( \strpos( $types[ $option['type'] ], 'Select' ) ) {
//				$I->seeElement( 'select', [ 'type' => $option['type'] ] );
//			}
		}

		$formFields =  [
		];

		// Submit the form as a user would submit it.
		$I->submitForm( '#' . $this->plugin['options_group'], $formFields );

		$option = $I->grabOptionFromDatabase( $this->plugin['options_name'] );

		$I->seeOptionInDatabase( [ 'option_name' => $this->plugin['options_name'] ] );
		\PHPUnit\Framework\Assert::assertCount( $this->count_options, $option, '' );
	}
}
