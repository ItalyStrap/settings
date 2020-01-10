<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\ThemeModFilter;

// phpcs:disable
require_once 'BaseFilter.php';
// phpcs:enable

/**
 * Class SanitizeTest
 * @package ItalyStrap\Tests
 */
class ThemeModTest extends BaseFilter {


	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'set_theme_mod', function ( $key, $string ) {
			return $string;
		} );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	protected function getInstance(): FilterableInterface {
		$sut = new ThemeModFilter();
		$this->assertInstanceOf( ThemeModFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldFilter() {

		$sut = $this->getInstance();
		$value = $sut->filter( '', 'value', [$sut::KEY => 'theme_mod'] );
		$this->assertStringContainsString( 'value', $value, '' );
	}
}
