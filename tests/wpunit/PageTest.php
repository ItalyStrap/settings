<?php

use ItalyStrap\Settings\Pages as P;

class PageTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
	private $sections;
	private $plugin;
	/**
	 * @var array|WP_UnitTest_Factory|null
	 */
	private $page;

	public function setUp(): void
    {
        // Before...
        parent::setUp();

		$this->sections = require codecept_data_dir( 'fixtures/config/' ) . 'sections.php';
		$this->plugin = require codecept_data_dir( 'fixtures/config/' ) . 'plugin.php';
		$this->page = (array) require codecept_data_dir( 'fixtures/config/' ) . 'page.php';

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
			$config = $this->page;
		}

		$config = \ItalyStrap\Config\ConfigFactory::make( $config );
		$view = $this->make( \ItalyStrap\Settings\ViewPage::class );
		$sections = $this->make( \ItalyStrap\Settings\Sections::class, [
			'options'	=> $this->make( \ItalyStrap\Settings\Options::class ),
		] );

		$sut = new \ItalyStrap\Settings\Page( $config, $sections, $view );
		$this->assertInstanceOf( \ItalyStrap\Settings\Page::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable()
	{
		$this->getInstance();
	}

	/**
	 * @test
	 */
	public function ItShouldRegister()
	{
		$sut = $this->getInstance();
		$sut->register();
	}

	/**
	 * @test
	 */
	public function ItShouldThrownErrorIfMenuTitleIsNotProvided()
	{
		$this->expectException( RuntimeException::class );

		$pages = [
//			P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ),
			P::SLUG			=> 'italystrap-dashboard',
		];

		$sut = $this->getInstance( $pages );
		$sut->register();
	}

	/**
	 * @test
	 */
	public function ItShouldThrownErrorIfSlugIsNotProvided()
	{
		$this->expectException( RuntimeException::class );

		$pages = [
			P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ),
//			P::SLUG			=> 'italystrap-dashboard',
		];

		$sut = $this->getInstance( $pages );
		$sut->register();
	}
}
