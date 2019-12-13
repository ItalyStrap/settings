<?php
class PagesTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

	private function getInstance() {
    	$config = $this->make( \ItalyStrap\Config\Config::class );
    	$view = $this->make( \ItalyStrap\View\View::class );
		$sut = new \ItalyStrap\Settings\Pages( $config, $view );
		$this->assertInstanceOf( \ItalyStrap\Settings\Pages::class, $sut, '' );
		return $sut;
    }

    // tests
    public function testSomeFeature()
    {
    	$this->getInstance();

    }
}
