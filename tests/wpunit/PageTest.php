<?php

use ItalyStrap\Settings\Page as P;

/**
 * Class PageTest
 * @covers \ItalyStrap\Settings\Page
 */
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

	public function setUp(): void {
		// Before...
		parent::setUp();

		$this->sections = require codecept_data_dir( 'fixtures/config/' ) . 'sections.php';
		$this->page = (array)require codecept_data_dir( 'fixtures/config/' ) . 'page.php';

		// Your set up methods here.
	}

	public function tearDown(): void {
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
			'options' => $this->make( \ItalyStrap\Settings\Options::class ),
		] );

		$sut = new \ItalyStrap\Settings\Page( $config, $view, $sections );
		$this->assertInstanceOf( \ItalyStrap\Settings\Page::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable() {
		$this->getInstance();
	}

	/**
	 * @test
	 */
	public function ItShouldReturnPageName() {

		$config = \array_merge(
			$this->page,
			[
				P::SLUG	=> 'test',
			]
		);

		$sut = $this->getInstance( $config );
		$page_name = $sut->getPageName();
		$this->assertStringContainsString( 'test', $page_name, '' );
	}

	/**
	 * @test
	 */
	public function ItShouldRegister() {
		$sut = $this->getInstance();
		$sut->register();
	}

	public function invalidConfigProvider() {
		return [
			'if no menu title is provided'	=> [
				[
					P::SLUG => 'italystrap-dashboard'
				],
			],
			'if no slug is provided'	=> [
				[
					P::MENU_TITLE => \__( 'ItalyStrap', 'italystrap' )
				],
			]
		];
	}

	/**
	 * @test
	 * @dataProvider invalidConfigProvider
	 */
	public function ItShouldThrownError( array $config ) {
		$this->expectException( RuntimeException::class );
		$sut = $this->getInstance( $config );
		$sut->register();
	}

	/**
	 * @test
	 */
	public function ItShouldRegisterMenuAndSubmenu() {

		global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
			   $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

		$slug = 'test-dashboard';

		$pages = [
			P::MENU_TITLE => \__( 'Menu title', 'italystrap' ),
			P::SLUG => $slug,
		];

		$sut = $this->getInstance( $pages );
		$sut->register();

		$this->assertArrayHasKey( $slug, $admin_page_hooks, '' );


		$sub_slug = 'sub-slug';

		$pages = [
			P::PARENT => $slug,
			P::MENU_TITLE => \__( 'Submenu title', 'italystrap' ),
			P::SLUG => $sub_slug,
		];

		$sut = $this->getInstance( $pages );
		$sut->register();

		$this->assertArrayHasKey( $slug, $_wp_submenu_nopriv, '' );
		$this->assertArrayHasKey( $sub_slug, $_wp_submenu_nopriv[ $slug ], '' );
	}
}
