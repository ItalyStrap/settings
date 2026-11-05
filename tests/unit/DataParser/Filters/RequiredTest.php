<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\Exception\ValueRequired;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\RequiredFilter;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
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

    protected function getInstance(): FilterableInterface
    {
        $sut = new RequiredFilter();
        $this->assertInstanceOf(RequiredFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function ifValueIsNotRequiredCouldReturnAnEmptyValue(): void
    {
        $sut = $this->getInstance();
        $value = $sut->filter('key', '', [ $sut::KEY => false ]);
        $this->assertStringContainsString('', $value, '');
    }

    /**
     * @test
     */
    public function itShouldThrownExceptionIfValueIsRequired(): void
    {
        $sut = $this->getInstance();
        $this->expectException(ValueRequired::class);
        $sut->filter('key', '', [ $sut::KEY => true ]);
    }
}
