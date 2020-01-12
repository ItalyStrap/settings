<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\Parser;
use ItalyStrap\DataParser\ParserFactory;
use ItalyStrap\DataParser\ParserInterface;

class ParserFactoryTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = ParserFactory::make();
		$this->assertInstanceOf( ParserInterface::class, $sut, '' );
		$this->assertInstanceOf( Parser::class, $sut, '' );
		$sut = ParserFactory::make( 'some-domain' );
		$this->assertInstanceOf( ParserInterface::class, $sut, '' );
		$this->assertInstanceOf( Parser::class, $sut, '' );
	}
}
