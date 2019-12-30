<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

class ViewPageTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var string
	 */
	private $page_name;

	/**
	 * @var array|\WP_UnitTest_Factory|null
	 */
	private $sections;

	public function setUp(): void {
		// Before...
		parent::setUp();
		\wp_set_current_user( '1', 'admin' );
		// Your set up methods here.

		$this->page_name = 'group_name';

		$this->sections = [
			[
				'tab_title'	=> 'Standard'
			],
			[
				'tab_title'	=> 'Advanced'
			],
		];
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	private function getInstance(): \ItalyStrap\Settings\ViewPage {
		$sut = new \ItalyStrap\Settings\ViewPage();
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPageInterface::class, $sut, '' );
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPage::class, $sut, '' );
		return $sut;
	}

	private function getSections() {

		$sections_obj = $this->make( \ItalyStrap\Settings\Sections::class, [
			'count'		=> \count( $this->sections ),
			'getPageName'	=> function () {
				return $this->page_name;
			},
			'getSections'	=> function () {
				return $this->sections;
			},
		] );

		return $sections_obj;
	}

	private function getPage() {

		$page_obj = $this->make( \ItalyStrap\Settings\Page::class, [
			'getPageName'	=> function () {
				return $this->page_name;
			},
		] );

		return $page_obj;
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
	public function itShouldBeRenderDefaultPageIfNoViewFileIsProvided() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
		$sut->forPage( $this->getPage() );
		$sut->render();
		$output = $this->getActualOutputForAssertion();
		$this->assertStringContainsString( '<form method="post" action="options.php"', $output, '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeRenderCustomPageProvided() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
		$sut->render( codecept_data_dir( 'fixtures/view/' ) . 'settings_form.php' );
		$output = $this->getActualOutputForAssertion();
		$this->assertStringContainsString( '<h1>Form Test</h1>', $output, '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeRenderTab() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
		$sut->forPage( $this->getPage() );
		$sut->render();
		$output = $this->getActualOutputForAssertion();

		$count = 1;
		foreach ( $this->sections as $section ) {
			$this->assertStringContainsString( '<a href="#tabs-' . $count . '">', $output, '' );
			$this->assertStringContainsString( $section['tab_title'], $output, '' );
			$count++;
		}
	}

	/**
	 * @test
	 */
	public function itShouldThrownRuntimeExceptionIfHasNoSections() {
		$this->expectException( \RuntimeException::class );
		$sut = $this->getInstance();
		$sut->render( '' );
	}
}
