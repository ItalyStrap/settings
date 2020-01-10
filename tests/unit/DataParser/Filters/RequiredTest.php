<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\Exception\ValueRejected;
use ItalyStrap\DataParser\Exception\ValueRequired;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\CapabilityFilter;
use ItalyStrap\DataParser\Filters\RequiredFilter;
use Symfony\Component\VarDumper\VarDumper;

// phpcs:disable
require_once 'BaseFilter.php';
// phpcs:enable

/**
 * Class RequiredTest
 * @package ItalyStrap\Tests
 */
class RequiredTest extends BaseFilter
{

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

	protected function getInstance(): FilterableInterface {
		$sut = new RequiredFilter();
		$this->assertInstanceOf( RequiredFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function ifValueIsNotRequiredCouldReturnAlsoAnEmptyValue() {
		$sut = $this->getInstance();
		$value = $sut->filter( 'key', '', [ $sut::KEY => false ] );
		$this->assertStringContainsString( '', $value, '' );
	}

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfValueIsRequired() {
	$sut = $this->getInstance();
	$this->expectException( ValueRequired::class );
	$sut->filter( 'key', '', [ $sut::KEY => true ] );
}
}
