<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\ThemeModFilter;

use function tad\FunctionMockerLe\define;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
// phpcs:enable

/**
 * Class SanitizeTest
 * @package ItalyStrap\Tests
 */
class ThemeModTest extends BaseFilter
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private ?bool $mod_is_called = null;

    public function isModCalled(): bool
    {
        return $this->mod_is_called;
    }

	// phpcs:ignore -- Method from Codeception
	protected function _before() {

        $this->mod_is_called = false;

		// phpcs:ignore -- This is not a constant definition
		define( 'set_theme_mod', function ( $key, $string ) {
            $this->mod_is_called = true;
            return $string;
        });
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    protected function getInstance(): FilterableInterface
    {
        $sut = new ThemeModFilter();
        $this->assertInstanceOf(ThemeModFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldSetThemeMod(): void
    {
        $sut = $this->getInstance();
        $value = $sut->filter('', 'value', [ $sut::KEY => 'theme_mod' ]);
        $this->assertStringContainsString('value', $value, '');
        $this->assertTrue($this->isModCalled());
    }

    /**
     * @test
     */
    public function itShouldNotSetThemeMod(): void
    {
        $sut = $this->getInstance();
        $value = $sut->filter('', 'value', [ $sut::KEY => false ]);
        $this->assertStringContainsString('value', $value, '');
        $this->assertFalse($this->isModCalled());
    }
}
