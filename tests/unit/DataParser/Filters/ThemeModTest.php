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

	/**
	 * @var bool
	 */
	private $mod_is_called;

	/**
	 * @return bool
	 */
	public function isModCalled(): bool {
		return $this->mod_is_called;
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {

		$this->mod_is_called = false;

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'set_theme_mod', function ( $key, $string ) {
			$this->mod_is_called = true;
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
	public function itShouldSetThemeMod() {
		$sut = $this->getInstance();
		$value = $sut->filter( '', 'value', [ $sut::KEY => 'theme_mod' ] );
		$this->assertStringContainsString( 'value', $value, '' );
		$this->assertTrue( $this->isModCalled() );
	}

	/**
	 * @test
	 */
	public function itShouldNotSetThemeMod() {
		$sut = $this->getInstance();
		$value = $sut->filter( '', 'value', [ $sut::KEY => false ] );
		$this->assertStringContainsString( 'value', $value, '' );
		$this->assertFalse( $this->isModCalled() );
	}
}
