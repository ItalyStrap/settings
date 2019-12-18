<?php
class DataParserTest extends \Codeception\Test\Unit
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

	public function getInstance() {
		$sut = new \ItalyStrap\Settings\DataParser();
		$this->assertInstanceOf( \ItalyStrap\Settings\DataParser::class, $sut, '' );
		return $sut;
    }

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable() {
		$sut = $this->getInstance();
    }
}
