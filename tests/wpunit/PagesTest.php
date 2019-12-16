<?php

class PagesTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
	private $sections = [];
	private $plugin = [];

    public function setUp(): void
    {
        // Before...
        parent::setUp();

		$this->sections = require codecept_data_dir( 'fixtures/config/' ) . 'sections.php';
		$this->plugin = require codecept_data_dir( 'fixtures/config/' ) . 'plugin.php';

        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

	private function getInstance() {
		$config = $this->make( \ItalyStrap\Config\Config::class );
		$view = $this->make( \ItalyStrap\View\View::class );
		$sections = $this->make( \ItalyStrap\Settings\Sections::class, [
			'options'	=> $this->make( \ItalyStrap\Settings\Options::class ),
		] );
		$sut = new \ItalyStrap\Settings\Page( $config, $sections );
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
//	public function ItShouldLoad()
//	{
//		$sut = $this->getInstance();
//		$sut->load();
//	}
}
