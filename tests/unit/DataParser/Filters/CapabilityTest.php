<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\CapabilityFilter;

require_once 'BaseFilter.php';
/**
 * Class CapabilityTest
 * @package ItalyStrap\Tests
 */
class CapabilityTest extends BaseFilter
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	// phpcs:ignore -- Method from Codeception
    protected function _before()
    {
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'current_user_can', function ( $string ) {
			return true;
		} );
    }

	// phpcs:ignore -- Method from Codeception
    protected function _after()
    {
    }

    protected function getInstance(): FilterableInterface {
		$sut = new CapabilityFilter();
		$this->assertInstanceOf( CapabilityFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
//	public function itShouldFilter() {
//		$sut = $this->getInstance();
//		$sut->filter( '', '', [ $sut::KEY => false ] );
//	}
}
