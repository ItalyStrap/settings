<?php

class PagesTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;

    public function setUp(): void
    {
        // Before...
        parent::setUp();

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
		$sut = new \ItalyStrap\Settings\Pages( $config, $view );
		$this->assertInstanceOf( \ItalyStrap\Settings\Pages::class, $sut, '' );
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
	public function ItShouldLoad()
	{
		$sut = $this->getInstance();
		$sut->load();
	}
}
