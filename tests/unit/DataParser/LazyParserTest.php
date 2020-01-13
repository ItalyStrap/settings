<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\Filters\NullFilter;
use ItalyStrap\DataParser\LazyParser;
use ItalyStrap\DataParser\ParserInterface;

class LazyParserTest extends \Codeception\Test\Unit
{

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Closure
	 */
	private $filters_callback;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->filters_callback = function (): array {
			$filters = [
				new NullFilter(),
			];

			return $filters;
		};
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	public function getInstance(): LazyParser {
		$sut = new LazyParser( $this->filters_callback );
		$this->assertInstanceOf( ParserInterface::class, $sut, '' );
		$this->assertInstanceOf( LazyParser::class, $sut, '' );
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
	public function itShouldBeInstantiablehjcgf() {
		$sut = $this->getInstance();
		$data = $sut->parseValues( ['key' => 'value'] );
		$this->assertEquals( ['key' => 'value'], $data, '' );
	}
}
