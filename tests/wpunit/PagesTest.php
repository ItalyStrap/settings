<?php

use ItalyStrap\Settings\Pages as P;

class PagesTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
	private $sections = [];
	private $plugin = [];
	private $pages;

	public function setUp(): void
    {
        // Before...
        parent::setUp();

		$this->sections = require codecept_data_dir( 'fixtures/config/' ) . 'sections.php';
		$this->plugin = require codecept_data_dir( 'fixtures/config/' ) . 'plugin.php';
		$this->pages = (array) require codecept_data_dir( 'fixtures/config/' ) . 'pages.php';

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

	private function getInstance( array $config = [] ) {

		if ( empty( $config ) ) {
			$config = $this->pages;
		}

		$config = \ItalyStrap\Config\ConfigFactory::make( $config );
		$view = $this->make( \ItalyStrap\Settings\ViewPage::class );
		$sections = $this->make( \ItalyStrap\Settings\Sections::class, [
			'options'	=> $this->make( \ItalyStrap\Settings\Options::class ),
		] );

		$sut = new \ItalyStrap\Settings\Pages( $config, $sections, $view );
		$this->assertInstanceOf( \ItalyStrap\Settings\Pages::class, $sut, '' );
		return $sut;
	}
}
