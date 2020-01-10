<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\CapabilityFilter;
use Symfony\Component\VarDumper\VarDumper;

// phpcs:disable
require_once 'BaseFilter.php';
// phpcs:enable

/**
 * Class CapabilityTest
 * @package ItalyStrap\Tests
 */
class CapabilityTest extends BaseFilter {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
    protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'current_user_can', function ( $string ) {
			return 'manage_options' === $string;
		} );
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	protected function getInstance(): FilterableInterface {
		$sut = new CapabilityFilter();
		$this->assertInstanceOf( CapabilityFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldFilter() {
		$sut = $this->getInstance();
		$sut->filter( 'key', 'value', [ $sut::KEY => 'manage_optionsd' ] );
	}
}
