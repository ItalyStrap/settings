<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\I18N\Translator;

class I18NTest extends Unit
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
		$sut = new Translator( 'some-domain' );
		$this->assertInstanceOf( Translator::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldBeHasDeomain() {
		$sut = $this->getInstance();
		$this->assertStringContainsString( 'some-domain', $sut->getDomain(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeHasDeomainsdgs() {

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'get_locale', function () {
			return 'it_IT';
		} );

		$sut = $this->getInstance();
		$this->assertStringContainsString( 'it_IT', $sut->getLanguage(), '' );
	}
}
