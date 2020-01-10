<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\ThemeModFilter;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\I18N\Translator;

// phpcs:disable
require_once 'BaseFilter.php';
// phpcs:enable

/**
 * Class SanitizeTest
 * @package ItalyStrap\Tests
 */
class TranslateTest extends BaseFilter {



	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $translator;

	/**
	 * @return Translator
	 */
	public function getTranslator(): Translator {
		return $this->translator->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->translator = $this->prophesize( Translator::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	protected function getInstance(): FilterableInterface {
		$sut = new TranslateFilter( $this->getTranslator() );
		$this->assertInstanceOf( TranslateFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldFilter() {

		$sut = $this->getInstance();
		$value = $sut->filter( '', 'value', [ $sut::KEY => true, 'id' => 'key' ] );
		$this->assertStringContainsString( 'value', $value, '' );
	}
}
