<?php

class LinksTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $page;

	protected function _before() {
		\tad\FunctionMockerLe\define( 'apply_filters', function ( $filter_name, $value ) {
			return $value;
		} );
		\tad\FunctionMockerLe\define( 'esc_attr', function ( $value ) {
			return $value;
		} );
		\tad\FunctionMockerLe\define( 'esc_html', function ( $value ) {
			return $value;
		} );
		\tad\FunctionMockerLe\define( 'esc_url', function ( $value ) {
			return $value;
		} );
		\tad\FunctionMockerLe\define( 'admin_url', function ( $value ) {
			return $value;
		} );
		$this->page = $this->prophesize( \ItalyStrap\Settings\Page::class );
	}

	protected function _after() {
	}

	private function getInstance(): \ItalyStrap\Settings\Links {
		$tag = new \ItalyStrap\HTML\Tag( new \ItalyStrap\HTML\Attributes() );
		$sut = new \ItalyStrap\Settings\Links( $tag );
		$this->assertInstanceOf( \ItalyStrap\Settings\Links::class, $sut, '' );
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
	public function itShouldAddsLinksForPages() {
		$sut = $this->getInstance();

		$slug = 'some-test-slug';
		$this->page->getPageName()->willReturn( $slug );

		$content = 'Title';
		$this->page->getMenuTitle()->willReturn( $content );

		$sut->forPages( $this->getPage() );

		$links = $sut->getLinks();
		$this->assertIsArray( $links, '' );

		foreach ($links as $link) {
			codecept_debug( $link );
			$this->assertStringContainsString( $slug, $link, '' );
//			$this->assertStringContainsString( \sprintf(
//				'<a href="%1$s" aria-label="%2$s">%2$s</a>',
//				$slug,
//				$content
//			), $link, '' );
		}
	}
}
