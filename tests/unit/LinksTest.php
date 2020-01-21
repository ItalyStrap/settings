<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

class LinksTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $page;

	/**
	 * @var string
	 */
	private $admin_url;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'apply_filters', function ( $filter_name, $value ) {
			return $value;
		} );
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'esc_attr', function ( $value ) {
			return $value;
		} );
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'esc_html', function ( $value ) {
			return $value;
		} );
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'esc_url', function ( $value ) {
			return $value;
		} );

		$this->admin_url = $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/';

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'admin_url', function ( $value = '' ) {
			return $this->admin_url . $value;
		} );

		$this->page = $this->prophesize( \ItalyStrap\Settings\Page::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance(): \ItalyStrap\Settings\PluginLinks {
		$tag = new \ItalyStrap\HTML\Tag( new \ItalyStrap\HTML\Attributes() );
		$sut = new \ItalyStrap\Settings\PluginLinks( $tag, 'plugin_base_name' );
		$this->assertInstanceOf( \ItalyStrap\Settings\PluginLinks::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}

	private function getPage() {
		return $this->page->reveal();
	}

	/**
	 * @test
	 */
	public function itShouldAddsLinksForPage() {
		$sut = $this->getInstance();

		$slug = 'some-test-slug';
		$content = 'Title';
		$this->page->getSlug()->willReturn( $slug );
		$this->page->getPageTitle()->willReturn( $content );
		$this->page->isSubmenu()->willReturn( false );

		$sut->forPages( $this->getPage() );

		$links = $sut->getLinks();
		$this->assertIsArray( $links, '' );

		foreach ($links as $link) {
			$this->assertStringContainsString( \sprintf(
				'<a href="%1$s" aria-label="%2$s">%2$s</a>',
				$this->admin_url . 'admin.php?page=' . $slug,
				$content
			), $link, '' );
		}
	}

	/**
	 * @test
	 */
	public function itShouldAddsLinksForSubPage() {
		$sut = $this->getInstance();

		$slug = 'some-test-slug';
		$content = 'Title';
		$this->page->getSlug()->willReturn( $slug );
		$this->page->getPageTitle()->willReturn( $content );
		$this->page->isSubmenu()->willReturn( true );
		$this->page->getParentPageSlug()->willReturn( 'parent' );

		$sut->forPages( $this->getPage() );

		$links = $sut->getLinks();
		$this->assertIsArray( $links, '' );

		foreach ($links as $link) {
			$this->assertStringContainsString( \sprintf(
				'<a href="%1$s" aria-label="%2$s">%2$s</a>',
				$this->admin_url . 'admin.php?page=' . $slug,
				$content
			), $link, '' );
		}
	}

	/**
	 * @test
	 */
	public function itShouldAddsLinksForSubPageOfWPPages() {
		$sut = $this->getInstance();

		$parents = $sut->getDefaultPages();

		foreach ( $parents as $key => $parent ) {
			$slug = 'some-test-slug';
			$content = 'Title';
			$this->page->getSlug()->willReturn( $slug );
			$this->page->getPageTitle()->willReturn( $content );
			$this->page->isSubmenu()->willReturn( true );
			$this->page->getParentPageSlug()->willReturn( $parent );

			$sut->forPages( $this->getPage() );

			$links = $sut->getLinks();
			$this->assertIsArray( $links, '' );

			foreach ($links as $link) {
				$this->assertStringContainsString( \sprintf(
					'<a href="%1$s" aria-label="%2$s">%2$s</a>',
					$this->admin_url . $parent . '?page=' . $slug,
					$content
				), $link, '' );
			}
		}
	}

	/**
	 * @test
	 */
	public function itShouldAddCustomLink() {
		$sut = $this->getInstance();

		$key = 'some-key';
		$url = 'http://localhost.com';
		$content = 'Title';

		$sut->addLink($key, $url, $content);

		$links = $sut->getLinks();

		$this->assertArrayHasKey( $key, $links, '' );

		$this->assertStringContainsString( \sprintf(
			'<a href="%1$s" aria-label="%2$s">%2$s</a>',
			$url,
			$content
		), $links[ $key ], '' );
	}
}
