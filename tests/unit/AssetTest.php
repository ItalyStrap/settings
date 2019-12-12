<?php
class AssetTest extends \Codeception\Test\Unit
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
		$sut = new \ItalyStrap\Settings\Asset( $config );
		$this->assertInstanceOf( \ItalyStrap\Settings\Asset::class, $sut, '' );

		return $sut;
    }

	/**
	 * @test
	 */
    public function ItShouldBeInstantiable()
    {
		$this->getInstance();
    }
}
