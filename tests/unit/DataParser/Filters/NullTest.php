<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\CapabilityFilter;
use ItalyStrap\DataParser\Filters\DefaultSchema;
use ItalyStrap\DataParser\Filters\NullFilter;

require_once 'BaseFilter.php';

/**
 * Class NullTest
 * @package ItalyStrap\Tests
 */
class NullTest extends BaseFilter {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'current_user_can', function ( $string ) {
			return true;
		} );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	protected function getInstance(): FilterableInterface {

		$sut = new class extends NullFilter {
			const KEY = 'some-value';
			use DefaultSchema;
		};

		$this->assertInstanceOf( NullFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldFilter() {
		$sut = $this->getInstance();
		$value = $sut->filter( '', 'value', [ $sut::KEY => false ] );
		$this->assertStringContainsString( 'value', $value, '' );
	}
}
