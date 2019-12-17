<?php

class ViewPageTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;

    public function setUp(): void
    {
        // Before...
        parent::setUp();
		\wp_set_current_user( '1', 'admin' );
        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

	private function getInstance(): \ItalyStrap\Settings\ViewPage {
		$sut = new \ItalyStrap\Settings\ViewPage();
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPage::class, $sut, '' );
		return $sut;
	}

	private function getSections() {
		$sections = $this->make( \ItalyStrap\Settings\Sections::class, [
			'count'		=> 1,
			'getGroup'	=> function () {
				return 'group_name';
			},
		] );

		return $sections;
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
		$sut->render( '' );
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
}
