<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Settings\Page as P;

/**
 * Class PageTest
 * @covers \ItalyStrap\Settings\Page
 */
class PageTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var array
	 */
	private $sections_config;

	/**
	 * @var array|\WP_UnitTest_Factory|null
	 */
	private $page_config;

	public function setUp(): void {
		// Before...
		parent::setUp();

		$this->sections_config = require codecept_data_dir( 'fixtures/config/' ) . 'sections.php';
		$this->page_config = (array)require codecept_data_dir( 'fixtures/config/' ) . 'page.php';

		wp_set_current_user( 1 );

		global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
			   $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;
		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
			   $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

		$menu = [];
		$admin_page_hooks = [];
		$_registered_pages = [];
		$_parent_pages = [];
		$submenu = [];
		$_wp_real_parent_file = [];
		$_wp_submenu_nopriv = [];

		// Then...
		parent::tearDown();
	}

	private function getInstance( array $config = [] ) {

		if ( empty( $config ) ) {
			$config = $this->page_config;
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
	public function itShouldBeInstantiable() {
		$this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldReturnPageName() {

		$config = \array_merge(
			$this->page_config,
			[
				P::SLUG	=> 'test',
			]
		);

		$sut = $this->getInstance( $config );
		$page_name = $sut->getSlug();
		$this->assertStringContainsString( 'test', $page_name, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {
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
	public function itShouldThrownError( array $config ) {
		$this->expectException( \RuntimeException::class );
		$sut = $this->getInstance( $config );
		$sut->register();
	}

	/**
	 * @test
	 */
	public function itShouldRegisterMenuAndSubmenu() {

		global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
			   $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

		$slug = 'test-dashboard';

		$pages = [
			P::MENU_TITLE => \__( 'Menu title', 'italystrap' ),
			P::SLUG => $slug,
		];

		$sut = $this->getInstance( $pages );
		$hook = $sut->register();
		$this->assertStringContainsString( $hook, 'toplevel_page_' . $slug, '' );
		$this->assertArrayHasKey( $slug, $admin_page_hooks, '' );


		$sub_slug = 'sub-slug';

		$pages = [
			P::PARENT => $slug,
			P::MENU_TITLE => \__( 'Submenu title', 'italystrap' ),
			P::SLUG => $sub_slug,
		];

		$sut = $this->getInstance( $pages );
		$hook = $sut->register();
		$this->assertStringContainsString( $hook, 'menu-title_page_' . $sub_slug, '' );
		$this->assertArrayHasKey( $slug, $_parent_pages, '' );
		$this->assertArrayHasKey( $sub_slug, $_parent_pages, '' );
	}
}
