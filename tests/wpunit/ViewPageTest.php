<?php

class ViewPageTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;

	/**
	 * @var string
	 */
	private $group_name;
	/**
	 * @var array|WP_UnitTest_Factory|null
	 */
	private $sections;

	public function setUp(): void
    {
        // Before...
        parent::setUp();
		\wp_set_current_user( '1', 'admin' );
        // Your set up methods here.

		$this->group_name = 'group_name';

		$this->sections = [
			[
				'tab_title'	=> 'Standard'
			],
			[
				'tab_title'	=> 'Advanced'
			],
		];
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

	private function getInstance(): \ItalyStrap\Settings\ViewPageInterface {
		$sut = new \ItalyStrap\Settings\ViewPage();
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPageInterface::class, $sut, '' );
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPage::class, $sut, '' );
		return $sut;
	}

	private function getSections() {

		$sections_obj = $this->make( \ItalyStrap\Settings\Sections::class, [
			'count'		=> \count( $this->sections ),
			'getGroup'	=> function () {
				return $this->group_name;
			},
			'getSections'	=> function () {
				return $this->sections;
			},
		] );

		return $sections_obj;
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
	public function ItShouldBeRenderDefaultPageIfNoViewFileIsProvided() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
		$sut->render();
		$output = $this->getActualOutputForAssertion();
		$this->assertStringContainsString( '<form method="post" action="options.php"', $output, '' );
	}

	/**
	 * @test
	 */
	public function ItShouldBeRenderCustomPageProvided() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
		$sut->render( codecept_data_dir( 'fixtures/view/' ) . 'settings_form.php' );
		$output = $this->getActualOutputForAssertion();
		$this->assertStringContainsString( '<h1>Form Test</h1>', $output, '' );
	}

	/**
	 * @test
	 */
	public function ItShouldBeRenderTab() {
		$sut = $this->getInstance();
		$sut->withSections( $this->getSections() );
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
	public function ItShouldThrownRuntimeExceptionIfHasNoSections() {
		$this->expectException( \RuntimeException::class );
		$sut = $this->getInstance();
		$sut->render( '' );
	}
}
