<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\DefaultSchema;
use ItalyStrap\DataParser\Filters\NullFilter;

use function tad\FunctionMockerLe\define;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
// phpcs:enable

/**
 * Class NullTest
 * @package ItalyStrap\Tests
 */
class NullTest extends BaseFilter
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		define( 'current_user_can', fn($string): true => true);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    protected function getInstance(): FilterableInterface
    {

        $sut = new class extends NullFilter {
            use DefaultSchema;

            public const KEY = 'some-key';
        };

        $this->assertInstanceOf(NullFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldFilter(): void
    {
        $sut = $this->getInstance();
        $value = $sut->filter('', 'value', [ $sut::KEY => false ]);
        $this->assertStringContainsString('value', $value, '');
        $value = $sut->filter('', 'value', [ $sut::KEY => true ]);
        $this->assertStringContainsString('value', $value, '');
    }
}
